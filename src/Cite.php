<?php

/**
 * A parser extension that adds two tags, <ref> and <references> for adding
 * citations to pages
 *
 * @ingroup Extensions
 *
 * Documentation
 * @link https://www.mediawiki.org/wiki/Extension:Cite/Cite.php
 *
 * <cite> definition in HTML
 * @link http://www.w3.org/TR/html4/struct/text.html#edef-CITE
 *
 * <cite> definition in XHTML 2.0
 * @link http://www.w3.org/TR/2005/WD-xhtml2-20050527/mod-text.html#edef_text_cite
 *
 * @bug https://phabricator.wikimedia.org/T6579
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license GPL-2.0-or-later
 */

namespace Cite;

use Exception;
use Html;
use MediaWiki\MediaWikiServices;
use Parser;
use ParserOptions;
use ParserOutput;
use Sanitizer;
use StatusValue;

class Cite {

	private const DEFAULT_GROUP = '';

	/**
	 * Maximum storage capacity for the pp_value field of the page_props table. 2^16-1 = 65535 is
	 * the size of a MySQL 'blob' field.
	 * @todo Find a way to retrieve this information from the DBAL
	 */
	public const MAX_STORAGE_LENGTH = 65535;

	/**
	 * Key used for storage in parser output's ExtensionData and ObjectCache
	 */
	public const EXT_DATA_KEY = 'Cite:References';

	/**
	 * Version number in case we change the data structure in the future
	 */
	private const DATA_VERSION_NUMBER = 1;

	/**
	 * Cache duration when parsing a page with references, in seconds. 3,600 seconds = 1 hour.
	 */
	public const CACHE_DURATION_ONPARSE = 3600;

	/**
	 * Wikitext attribute name for Book Referencing.
	 */
	public const BOOK_REF_ATTRIBUTE = 'extends';

	/**
	 * Page property key for the Book Referencing `extends` attribute.
	 */
	public const BOOK_REF_PROPERTY = 'ref-extends';

	/**
	 * The backlinks, in order, to pass as $3 to
	 * 'cite_references_link_many_format', defined in
	 * 'cite_references_link_many_format_backlink_labels
	 *
	 * @var string[]
	 */
	private $mBacklinkLabels;

	/**
	 * The links to use per group, in order.
	 *
	 * @var (string[]|false)[]
	 */
	private $mLinkLabels = [];

	/**
	 * @var Parser
	 */
	private $mParser;

	/**
	 * @var bool
	 */
	private $isPagePreview;

	/**
	 * @var bool
	 */
	private $isSectionPreview;

	/**
	 * @var CiteErrorReporter
	 */
	private $errorReporter;

	/**
	 * True when the ParserAfterParse hook has been called.
	 * Used to avoid doing anything in ParserBeforeTidy.
	 *
	 * @var bool
	 */
	private $mHaveAfterParse = false;

	/**
	 * True when a <ref> tag is being processed.
	 * Used to avoid infinite recursion
	 *
	 * @var bool
	 */
	private $mInCite = false;

	/**
	 * @var null|string The current group name while parsing nested <ref> in <references>. Null when
	 *  parsing <ref> outside of <references>. Warning, an empty string is a valid group name!
	 */
	private $inReferencesGroup = null;

	/**
	 * Error stack used when defining refs in <references>
	 *
	 * @var string[]
	 */
	private $mReferencesErrors = [];

	/**
	 * @var ReferenceStack $referenceStack
	 */
	private $referenceStack;

	/**
	 * @var bool
	 */
	private $mBumpRefData = false;

	/**
	 * @param Parser $parser
	 */
	private function rememberParser( Parser $parser ) {
		if ( $parser !== $this->mParser ) {
			$this->mParser = $parser;
			$this->isPagePreview = $parser->getOptions()->getIsPreview();
			$this->isSectionPreview = $parser->getOptions()->getIsSectionPreview();
			$this->errorReporter = new CiteErrorReporter(
				$parser->getOptions()->getUserLangObj(),
				$parser
			);
			$this->referenceStack = new ReferenceStack( $this->errorReporter );
		}
	}

	/**
	 * Callback function for <ref>
	 *
	 * @param string|null $text Raw content of the <ref> tag.
	 * @param string[] $argv Arguments
	 * @param Parser $parser
	 *
	 * @return string|false False in case a <ref> tag is not allowed in the current context
	 */
	public function ref( $text, array $argv, Parser $parser ) {
		if ( $this->mInCite ) {
			return false;
		}

		$this->rememberParser( $parser );

		$this->mInCite = true;
		$ret = $this->guardedRef( $text, $argv, $parser );
		$this->mInCite = false;

		// new <ref> tag, we may need to bump the ref data counter
		// to avoid overwriting a previous group
		$this->mBumpRefData = true;

		return $ret;
	}

	/**
	 * @param string|null $text
	 * @param string|null $name
	 * @param string|null $group
	 * @param string|null $follow
	 * @param string|null $extends
	 * @return StatusValue
	 */
	private function validateRef( $text, $name, $group, $follow, $extends ) : StatusValue {
		if ( ctype_digit( $name ) || ctype_digit( $follow ) || ctype_digit( $extends ) ) {
			// Numeric names mess up the resulting id's, potentially producing
			// duplicate id's in the XHTML.  The Right Thing To Do
			// would be to mangle them, but it's not really high-priority
			// (and would produce weird id's anyway).
			return StatusValue::newFatal( 'cite_error_ref_numeric_key' );
		}

		global $wgCiteBookReferencing;
		// Temporary feature flag until mainstreamed.  See T236255
		if ( !$wgCiteBookReferencing && $extends ) {
			return StatusValue::newFatal( 'cite_error_ref_too_many_keys' );
		}

		if ( $follow && ( $name || $extends ) ) {
			// TODO: Introduce a specific error for this case.
			return StatusValue::newFatal( 'cite_error_ref_too_many_keys' );
		}

		if ( $this->inReferencesGroup !== null ) {
			// Inside a references tag.  Note that we could have be deceived by `{{#tag`, so don't
			// take any actions that we can't reverse later.
			// FIXME: Some assertions make assumptions that rely on earlier tests not failing.
			//  These dependencies need to be explicit so they aren't accidentally broken by
			//  reordering in the future.

			if ( $group !== $this->inReferencesGroup ) {
				// <ref> and <references> have conflicting group attributes.
				return StatusValue::newFatal( 'cite_error_references_group_mismatch',
					Sanitizer::safeEncodeAttribute( $group ) );
			}

			if ( !$name ) {
				// <ref> calls inside <references> must be named
				return StatusValue::newFatal( 'cite_error_references_no_key' );
			}

			if ( $text === '' ) {
				// <ref> called in <references> has no content.
				return StatusValue::newFatal(
					'cite_error_empty_references_define',
					Sanitizer::safeEncodeAttribute( $name )
				);
			}

			// Section previews are exempt from some rules.
			if ( !$this->isSectionPreview ) {
				if ( !$this->referenceStack->hasGroup( $group ) ) {
					// Called with group attribute not defined in text.
					return StatusValue::newFatal( 'cite_error_references_missing_group',
						Sanitizer::safeEncodeAttribute( $group ) );
				}

				$groupRefs = $this->referenceStack->getGroupRefs( $group );

				if ( !isset( $groupRefs[$name] ) ) {
					// No such group exists.
					return StatusValue::newFatal( 'cite_error_references_missing_key',
						Sanitizer::safeEncodeAttribute( $name ) );
				}
			}
		} else {
			// Not in a references tag

			if ( $text !== null && trim( $text ) === '' && !$name ) {
				// Must have content or reuse another ref by name.
				// TODO: Trim text before validation.
				return StatusValue::newFatal( 'cite_error_ref_no_input' );
			}

			if ( $name === false ) {
				// Invalid attribute in the tag like <ref no_valid_attr="foo" />
				// or name and follow attribute used both in one tag
				// TODO: Move validation out of parseArguments
				return StatusValue::newFatal( 'cite_error_ref_too_many_keys' );
			}

			if ( $text === null && $name === null ) {
				// Something like <ref />; this makes no sense.
				// TODO: Is this redundant with no_input?
				return StatusValue::newFatal( 'cite_error_ref_no_key' );
			}

			if ( preg_match( '/<ref\b[^<]*?>/',
				preg_replace( '#<([^ ]+?).*?>.*?</\\1 *>|<!--.*?-->#', '', $text ) ) ) {
				// (bug T8199) This most likely implies that someone left off the
				// closing </ref> tag, which will cause the entire article to be
				// eaten up until the next <ref>.  So we bail out early instead.
				// The fancy regex above first tries chopping out anything that
				// looks like a comment or SGML tag, which is a crude way to avoid
				// false alarms for <nowiki>, <pre>, etc.
				//
				// Possible improvement: print the warning, followed by the contents
				// of the <ref> tag.  This way no part of the article will be eaten
				// even temporarily.
				return StatusValue::newFatal( 'cite_error_included_ref' );
			}
		}

		return StatusValue::newGood();
	}

	/**
	 * TODO: Looks like this should be split into a section insensitive to context, and the
	 *  special handling for each context.
	 *
	 * @param string|null $text Raw content of the <ref> tag.
	 * @param string[] $argv Arguments
	 * @param Parser $parser
	 *
	 * @throws Exception
	 * @return string
	 */
	private function guardedRef(
		$text,
		array $argv,
		Parser $parser
	) {
		// Tag every page where Book Referencing has been used, whether or not the ref tag is valid.
		// This code and the page property will be removed once the feature is stable.  See T237531.
		if ( array_key_exists( self::BOOK_REF_ATTRIBUTE, $argv ) ) {
			$parser->getOutput()->setProperty( self::BOOK_REF_PROPERTY, true );
		}

		$result = $this->parseArguments(
			$argv,
			[ 'dir', self::BOOK_REF_ATTRIBUTE, 'follow', 'group', 'name' ]
		);
		[
			'dir' => $dir,
			self::BOOK_REF_ATTRIBUTE => $extends,
			'follow' => $follow,
			'group' => $group,
			'name' => $name
		] = $result;

		# Split these into groups.
		if ( $group === null ) {
			$group = $this->inReferencesGroup ?? self::DEFAULT_GROUP;
		}

		$status = $this->validateRef( $text, $name, $group, $follow, $extends );

		if ( $this->inReferencesGroup !== null ) {
			if ( !$status->isOK() ) {
				foreach ( $status->getErrors() as $error ) {
					$this->mReferencesErrors[] = $this->errorReporter->halfParsed(
						$error['message'], ...$error['params'] );
				}
			} else {
				$groupRefs = $this->referenceStack->getGroupRefs( $group );
				if ( !isset( $groupRefs[$name]['text'] ) ) {
					$this->referenceStack->setRefText( $group, $name, $text );
				} else {
					if ( $groupRefs[$name]['text'] !== $text ) {
						// two refs with same key and different content
						// adds error message to the original ref
						// TODO: report these errors the same way as the others, rather than a
						//  special case to append to the second one's content.
						$text =
							$groupRefs[$name]['text'] . ' ' .
							$this->errorReporter->plain( 'cite_error_references_duplicate_key',
								$name );
						$this->referenceStack->setRefText( $group, $name, $text );
					}
				}
			}
			return '';
		}

		if ( $text !== null && trim( $text ) === '' && $name ) {
			$text = null;
		}

		if ( !$status->isOK() ) {
			$this->referenceStack->pushInvalidRef();

			// FIXME: If we ever have multiple errors, these must all be presented to the user,
			//  so they know what to correct.
			// TODO: Make this nicer, see T238061
			$error = $status->getErrors()[0];
			return $this->errorReporter->halfParsed( $error['message'], ...$error['params'] );
		}

		# We don't care about the content: if the name exists, the ref
		# is presumptively valid.  Either it stores a new ref, or re-
		# fers to an existing one.  If it refers to a nonexistent ref,
		# we'll figure that out later.  Likewise it's definitely valid
		# if there's any content, regardless of name.

		$result = $this->referenceStack->pushRef(
			$text, $name, $group, $follow, $argv, $dir, $parser->getStripState() );
		if ( $result === null ) {
			return '';
		} else {
			[ $key, $count, $label, $subkey ] = $result;
			return $this->linkRef( $group, $key, $count, $label, $subkey );
		}
	}

	/**
	 * @param string[] $argv The argument vector
	 * @param string[] $allowedAttributes Allowed attribute names
	 *
	 * @return (string|false|null)[] An array with exactly five elements, where each is a string on
	 *  valid input, false on invalid input, or null on no input.
	 */
	private function parseArguments( array $argv, array $allowedAttributes ) {
		$defaultValues = array_fill_keys( $allowedAttributes, null );

		if ( array_diff_key( $argv, $defaultValues ) ) {
			// FIXME: This shouldn't kill the <ref>, but still display it, along with an error
			return array_fill_keys( $allowedAttributes, false );
		}

		return array_merge( $defaultValues, $argv );
	}

	/**
	 * Callback function for <references>
	 *
	 * @param string|null $text Raw content of the <references> tag.
	 * @param string[] $argv Arguments
	 * @param Parser $parser
	 *
	 * @return string|false False in case a <references> tag is not allowed in the current context
	 */
	public function references( $text, array $argv, Parser $parser ) {
		if ( $this->mInCite || $this->inReferencesGroup !== null ) {
			return false;
		}

		$this->rememberParser( $parser );
		$ret = $this->guardedReferences( $text, $argv, $parser );
		$this->inReferencesGroup = null;

		return $ret;
	}

	/**
	 * Must only be called from references(). Use that to prevent recursion.
	 *
	 * @param string|null $text Raw content of the <references> tag.
	 * @param string[] $argv
	 * @param Parser $parser
	 *
	 * @return string
	 */
	private function guardedReferences(
		$text,
		array $argv,
		Parser $parser
	) {
		global $wgCiteResponsiveReferences;

		$result = $this->parseArguments(
			$argv,
			[ 'group', 'responsive' ]
		);
		[ 'group' => $group, 'responsive' => $responsive ] = $result;
		$this->inReferencesGroup = $group ?? self::DEFAULT_GROUP;

		if ( strval( $text ) !== '' ) {
			# Detect whether we were sent already rendered <ref>s.
			# Mostly a side effect of using #tag to call references.
			# The following assumes that the parsed <ref>s sent within
			# the <references> block were the most recent calls to
			# <ref>.  This assumption is true for all known use cases,
			# but not strictly enforced by the parser.  It is possible
			# that some unusual combination of #tag, <references> and
			# conditional parser functions could be created that would
			# lead to malformed references here.
			$count = substr_count( $text, Parser::MARKER_PREFIX . "-ref-" );

			# Undo effects of calling <ref> while unaware of containing <references>
			$redoStack = $this->referenceStack->rollbackRefs( $count );

			# Rerun <ref> call now that mInReferences is set.
			foreach ( $redoStack as $call ) {
				[ $ref_argv, $ref_text ] = $call;
				$this->guardedRef( $ref_text, $ref_argv, $parser );
			}

			# Parse $text to process any unparsed <ref> tags.
			$parser->recursiveTagParse( $text );
		}

		// FIXME: This feature is not covered by parser tests!
		if ( isset( $argv['responsive'] ) ) {
			$responsive = $responsive !== '0';
		} else {
			$responsive = $wgCiteResponsiveReferences;
		}

		// There are remaining parameters we don't recognise
		if ( $group === false ) {
			return $this->errorReporter->halfParsed( 'cite_error_references_invalid_parameters' );
		}

		$s = $this->referencesFormat( $this->inReferencesGroup, $responsive );

		# Append errors generated while processing <references>
		if ( $this->mReferencesErrors ) {
			$s .= "\n" . implode( "<br />\n", $this->mReferencesErrors );
			$this->mReferencesErrors = [];
		}
		return $s;
	}

	/**
	 * Make output to be returned from the references() function.
	 *
	 * If called outside of references(), caller is responsible for ensuring
	 * `mInReferences` is enabled before the call and disabled after call.
	 *
	 * @param string $group
	 * @param bool $responsive
	 * @return string HTML ready for output
	 */
	private function referencesFormat( $group, $responsive ) {
		if ( !$this->referenceStack->hasGroup( $group ) ) {
			return '';
		}

		// Add new lines between the list items (ref entries) to avoid confusing tidy (T15073).
		// Note: This builds a string of wikitext, not html.
		$parserInput = "\n";
		$groupRefs = $this->referenceStack->getGroupRefs( $group );
		foreach ( $groupRefs as $key => $value ) {
			$parserInput .= $this->referencesFormatEntry( $key, $value ) . "\n";
		}
		$parserInput = Html::rawElement( 'ol', [ 'class' => [ 'references' ] ], $parserInput );

		// Live hack: parse() adds two newlines on WM, can't reproduce it locally -ævar
		$ret = rtrim( $this->mParser->recursiveTagParse( $parserInput ), "\n" );

		if ( $responsive ) {
			// Use a DIV wrap because column-count on a list directly is broken in Chrome.
			// See https://bugs.chromium.org/p/chromium/issues/detail?id=498730.
			$wrapClasses = [ 'mw-references-wrap' ];
			if ( count( $groupRefs ) > 10 ) {
				$wrapClasses[] = 'mw-references-columns';
			}
			$ret = Html::rawElement( 'div', [ 'class' => $wrapClasses ], $ret );
		}

		if ( !$this->isPagePreview ) {
			// save references data for later use by LinksUpdate hooks
			$this->saveReferencesData( $this->mParser->getOutput(), $group );
		}

		// done, clean up so we can reuse the group
		$this->referenceStack->deleteGroup( $group );

		return $ret;
	}

	/**
	 * Format a single entry for the referencesFormat() function
	 *
	 * @param string|int $key The key of the reference
	 * @param array $val A single reference as documented at {@see ReferenceStack::$refs}
	 * @return string Wikitext, wrapped in a single <li> element
	 */
	private function referencesFormatEntry( $key, array $val ) {
		$text = $this->referenceText( $key, $val['text'] );
		$error = '';
		$extraAttributes = '';

		if ( isset( $val['dir'] ) ) {
			$dir = strtolower( $val['dir'] );
			if ( in_array( $dir, [ 'ltr', 'rtl' ] ) ) {
				$extraAttributes = Html::expandAttributes( [ 'class' => 'mw-cite-dir-' . $dir ] );
			} else {
				$error .= $this->errorReporter->plain( 'cite_error_ref_invalid_dir', $val['dir'] ) . "\n";
			}
		}

		// Fallback for a broken, and therefore unprocessed follow="…". Note this returns a <p>, not
		// an <li> as expected!
		if ( isset( $val['follow'] ) ) {
			return wfMessage(
				'cite_references_no_link',
				$this->normalizeKey(
					self::getReferencesKey( $val['follow'] )
				),
				$text
			)->inContentLanguage()->plain();
		}

		// This counts the number of reuses. 0 means the reference appears only 1 time.
		if ( isset( $val['count'] ) && $val['count'] < 1 ) {
			// Anonymous, auto-numbered references can't be reused and get marked with a -1.
			if ( $val['count'] < 0 ) {
				$id = $val['key'];
				$backlinkId = $this->refKey( $val['key'] );
			} else {
				$id = $key . '-' . $val['key'];
				$backlinkId = $this->refKey( $key, $val['key'] . '-' . $val['count'] );
			}
			return wfMessage(
				'cite_references_link_one',
				$this->normalizeKey( self::getReferencesKey( $id ) ),
				$this->normalizeKey( $backlinkId ),
				$text . $error,
				$extraAttributes
			)->inContentLanguage()->plain();
		}

		// Named references with >1 occurrences
		$backlinks = [];
		// There is no count in case of a section preview
		for ( $i = 0; $i <= ( $val['count'] ?? -1 ); $i++ ) {
			$backlinks[] = wfMessage(
				'cite_references_link_many_format',
				$this->normalizeKey(
					$this->refKey( $key, $val['key'] . '-' . $i )
				),
				$this->referencesFormatEntryNumericBacklinkLabel(
					$val['number'],
					$i,
					$val['count']
				),
				$this->referencesFormatEntryAlternateBacklinkLabel( $i )
			)->inContentLanguage()->plain();
		}
		return wfMessage(
			'cite_references_link_many',
			$this->normalizeKey(
				self::getReferencesKey( $key . '-' . ( $val['key'] ?? '' ) )
			),
			$this->listToText( $backlinks ),
			$text . $error,
			$extraAttributes
		)->inContentLanguage()->plain();
	}

	/**
	 * Returns formatted reference text
	 * @param string|int $key
	 * @param string|null $text
	 * @return string
	 */
	private function referenceText( $key, $text ) {
		if ( trim( $text ) === '' ) {
			if ( $this->isSectionPreview ) {
				return $this->errorReporter->plain( 'cite_warning_sectionpreview_no_text', $key );
			}
			return $this->errorReporter->plain( 'cite_error_references_no_text', $key );
		}
		return '<span class="reference-text">' . rtrim( $text, "\n" ) . "</span>\n";
	}

	/**
	 * Generate a numeric backlink given a base number and an
	 * offset, e.g. $base = 1, $offset = 2; = 1.2
	 * Since bug #5525, it correctly does 1.9 -> 1.10 as well as 1.099 -> 1.100
	 *
	 * @param int $base
	 * @param int $offset
	 * @param int $max Maximum value expected.
	 * @return string
	 */
	private function referencesFormatEntryNumericBacklinkLabel( $base, $offset, $max ) {
		$scope = strlen( $max );
		$ret = MediaWikiServices::getInstance()->getContentLanguage()->formatNum(
			sprintf( "%s.%0{$scope}s", $base, $offset )
		);
		return $ret;
	}

	/**
	 * Generate a custom format backlink given an offset, e.g.
	 * $offset = 2; = c if $this->mBacklinkLabels = [ 'a',
	 * 'b', 'c', ...]. Return an error if the offset > the # of
	 * array items
	 *
	 * @param int $offset
	 *
	 * @return string
	 */
	private function referencesFormatEntryAlternateBacklinkLabel( $offset ) {
		if ( !isset( $this->mBacklinkLabels ) ) {
			$this->genBacklinkLabels();
		}
		return $this->mBacklinkLabels[$offset]
			?? $this->errorReporter->plain( 'cite_error_references_no_backlink_label', null );
	}

	/**
	 * Generate a custom format link for a group given an offset, e.g.
	 * the second <ref group="foo"> is b if $this->mLinkLabels["foo"] =
	 * [ 'a', 'b', 'c', ...].
	 * Return an error if the offset > the # of array items
	 *
	 * @param int $offset
	 * @param string $group The group name
	 * @param string $label The text to use if there's no message for them.
	 *
	 * @return string
	 */
	private function getLinkLabel( $offset, $group, $label ) {
		$message = "cite_link_label_group-$group";
		if ( !isset( $this->mLinkLabels[$group] ) ) {
			$this->genLinkLabels( $group, $message );
		}
		if ( $this->mLinkLabels[$group] === false ) {
			// Use normal representation, ie. "$group 1", "$group 2"...
			return $label;
		}

		return $this->mLinkLabels[$group][$offset - 1]
			?? $this->errorReporter->plain( 'cite_error_no_link_label_group', [ $group, $message ] );
	}

	/**
	 * Return an id for use in wikitext output based on a key and
	 * optionally the number of it, used in <references>, not <ref>
	 * (since otherwise it would link to itself)
	 *
	 * @param string $key
	 * @param int|null $num The number of the key
	 * @return string A key for use in wikitext
	 */
	private function refKey( $key, $num = null ) {
		$prefix = wfMessage( 'cite_reference_link_prefix' )->inContentLanguage()->text();
		$suffix = wfMessage( 'cite_reference_link_suffix' )->inContentLanguage()->text();
		if ( $num !== null ) {
			$key = wfMessage( 'cite_reference_link_key_with_num', $key, $num )
				->inContentLanguage()->plain();
		}

		return "$prefix$key$suffix";
	}

	/**
	 * Return an id for use in wikitext output based on a key and
	 * optionally the number of it, used in <ref>, not <references>
	 * (since otherwise it would link to itself)
	 *
	 * @param string $key
	 * @return string A key for use in wikitext
	 */
	public static function getReferencesKey( $key ) {
		$prefix = wfMessage( 'cite_references_link_prefix' )->inContentLanguage()->text();
		$suffix = wfMessage( 'cite_references_link_suffix' )->inContentLanguage()->text();

		return "$prefix$key$suffix";
	}

	/**
	 * Generate a link (<sup ...) for the <ref> element from a key
	 * and return XHTML ready for output
	 *
	 * @suppress SecurityCheck-DoubleEscaped
	 * @param string $group
	 * @param string $key The key for the link
	 * @param int|null $count The index of the key, used for distinguishing
	 *                   multiple occurrences of the same key
	 * @param int $label The label to use for the link, I want to
	 *                   use the same label for all occurrences of
	 *                   the same named reference.
	 * @param string|null $subkey
	 *
	 * @return string
	 */
	private function linkRef( $group, $key, $count, $label, $subkey ) {
		$contLang = MediaWikiServices::getInstance()->getContentLanguage();

		return $this->mParser->recursiveTagParse(
				wfMessage(
					'cite_reference_link',
					$this->normalizeKey(
						$this->refKey( $key, $count )
					),
					$this->normalizeKey(
						self::getReferencesKey( $key . $subkey )
					),
					Sanitizer::safeEncodeAttribute(
						$this->getLinkLabel( $label, $group,
							( ( $group === self::DEFAULT_GROUP ) ? '' : "$group " ) . $contLang->formatNum( $label ) )
					)
				)->inContentLanguage()->plain()
			);
	}

	/**
	 * Normalizes and sanitizes a reference key
	 *
	 * @param string $key
	 * @return string
	 */
	private function normalizeKey( $key ) {
		$ret = Sanitizer::escapeIdForAttribute( $key );
		$ret = preg_replace( '/__+/', '_', $ret );
		$ret = Sanitizer::safeEncodeAttribute( $ret );

		return $ret;
	}

	/**
	 * This does approximately the same thing as
	 * Language::listToText() but due to this being used for a
	 * slightly different purpose (people might not want , as the
	 * first separator and not 'and' as the second, and this has to
	 * use messages from the content language) I'm rolling my own.
	 *
	 * @param string[] $arr The array to format
	 * @return string
	 */
	private function listToText( array $arr ) {
		$lastElement = array_pop( $arr );

		if ( $arr === [] ) {
			return (string)$lastElement;
		}

		$sep = wfMessage( 'cite_references_link_many_sep' )->inContentLanguage()->plain();
		$and = wfMessage( 'cite_references_link_many_and' )->inContentLanguage()->plain();
		return implode( $sep, $arr ) . $and . $lastElement;
	}

	/**
	 * Generate the labels to pass to the
	 * 'cite_references_link_many_format' message, the format is an
	 * arbitrary number of tokens separated by whitespace.
	 */
	private function genBacklinkLabels() {
		$text = wfMessage( 'cite_references_link_many_format_backlink_labels' )
			->inContentLanguage()->plain();
		$this->mBacklinkLabels = preg_split( '/\s+/', $text );
	}

	/**
	 * Generate the labels to pass to the
	 * 'cite_reference_link' message instead of numbers, the format is an
	 * arbitrary number of tokens separated by whitespace.
	 *
	 * @param string $group
	 * @param string $message
	 */
	private function genLinkLabels( $group, $message ) {
		$text = false;
		$msg = wfMessage( $message )->inContentLanguage();
		if ( $msg->exists() ) {
			$text = $msg->plain();
		}
		$this->mLinkLabels[$group] = $text ? preg_split( '/\s+/', $text ) : false;
	}

	/**
	 * Gets run when Parser::clearState() gets run, since we don't
	 * want the counts to transcend pages and other instances
	 *
	 * @param string $force Set to "force" to interrupt parsing
	 */
	public function clearState( $force = '' ) {
		if ( $force === 'force' ) {
			$this->mInCite = false;
			$this->inReferencesGroup = null;
		} elseif ( $this->mInCite || $this->inReferencesGroup !== null ) {
			// Don't clear when we're in the middle of parsing a <ref> or <references> tag
			return;
		}
		if ( $this->referenceStack ) {
			$this->referenceStack->clear();
		}
	}

	/**
	 * Called at the end of page processing to append a default references
	 * section, if refs were used without a main references tag. If there are references
	 * in a custom group, and there is no references tag for it, show an error
	 * message for that group.
	 * If we are processing a section preview, this adds the missing
	 * references tags and does not add the errors.
	 *
	 * @param bool $afterParse True if called from the ParserAfterParse hook
	 * @param ParserOptions $parserOptions
	 * @param ParserOutput $parserOutput
	 * @param string &$text
	 */
	public function checkRefsNoReferences(
		$afterParse,
		ParserOptions $parserOptions,
		ParserOutput $parserOutput,
		&$text
	) {
		global $wgCiteResponsiveReferences;

		if ( $afterParse ) {
			$this->mHaveAfterParse = true;
		} elseif ( $this->mHaveAfterParse ) {
			return;
		}

		if ( !$parserOptions->getIsPreview() ) {
			// save references data for later use by LinksUpdate hooks
			if ( $this->referenceStack &&
				$this->referenceStack->hasGroup( self::DEFAULT_GROUP )
			) {
				$this->saveReferencesData( $parserOutput );
			}
		}

		$s = '';
		if ( $this->referenceStack ) {
			foreach ( $this->referenceStack->getGroups() as $group ) {
				if ( $group === self::DEFAULT_GROUP || $parserOptions->getIsSectionPreview() ) {
					$this->inReferencesGroup = $group;
					$s .= $this->referencesFormat( $group, $wgCiteResponsiveReferences );
					$this->inReferencesGroup = null;
				} else {
					$s .= "\n<br />" .
						$this->errorReporter->halfParsed(
							'cite_error_group_refs_without_references',
							Sanitizer::safeEncodeAttribute( $group )
						);
				}
			}
		}
		if ( $parserOptions->getIsSectionPreview() && $s !== '' ) {
			// provide a preview of references in its own section
			$text .= "\n" . '<div class="mw-ext-cite-cite_section_preview_references" >';
			$headerMsg = wfMessage( 'cite_section_preview_references' );
			if ( !$headerMsg->isDisabled() ) {
				$text .= '<h2 id="mw-ext-cite-cite_section_preview_references_header" >'
				. $headerMsg->escaped()
				. '</h2>';
			}
			$text .= $s . '</div>';
		} else {
			$text .= $s;
		}
	}

	/**
	 * Saves references in parser extension data
	 * This is called by each <references/> tag, and by checkRefsNoReferences
	 *
	 * @param ParserOutput $parserOutput
	 * @param string $group
	 */
	private function saveReferencesData( ParserOutput $parserOutput, $group = self::DEFAULT_GROUP ) {
		global $wgCiteStoreReferencesData;
		if ( !$wgCiteStoreReferencesData ) {
			return;
		}
		$savedRefs = $parserOutput->getExtensionData( self::EXT_DATA_KEY );
		if ( $savedRefs === null ) {
			// Initialize array structure
			$savedRefs = [
				'refs' => [],
				'version' => self::DATA_VERSION_NUMBER,
			];
		}
		if ( $this->mBumpRefData ) {
			// This handles pages with multiple <references/> tags with <ref> tags in between.
			// On those, a group can appear several times, so we need to avoid overwriting
			// a previous appearance.
			$savedRefs['refs'][] = [];
			$this->mBumpRefData = false;
		}
		$n = count( $savedRefs['refs'] ) - 1;
		// save group
		$savedRefs['refs'][$n][$group] = $this->referenceStack->getGroupRefs( $group );

		$parserOutput->setExtensionData( self::EXT_DATA_KEY, $savedRefs );
	}

}
