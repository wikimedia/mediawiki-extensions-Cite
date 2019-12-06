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
use Parser;
use Sanitizer;
use StatusValue;

class Cite {

	public const DEFAULT_GROUP = '';

	/**
	 * Wikitext attribute name for Book Referencing.
	 */
	public const BOOK_REF_ATTRIBUTE = 'extends';

	/**
	 * Page property key for the Book Referencing `extends` attribute.
	 */
	public const BOOK_REF_PROPERTY = 'ref-extends';

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
	 * @var FootnoteMarkFormatter
	 */
	private $footnoteMarkFormatter;

	/**
	 * @var FootnoteBodyFormatter
	 */
	private $footnoteBodyFormatter;

	/**
	 * @var CiteErrorReporter
	 */
	private $errorReporter;

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
	 * @var ReferenceStack
	 */
	private $referenceStack;

	/**
	 * @var ReferenceMessageLocalizer $messageLocalizer
	 */
	private $messageLocalizer;

	/**
	 * @param Parser $parser
	 */
	private function rememberParser( Parser $parser ) {
		if ( $parser !== $this->mParser ) {
			$this->mParser = $parser;
			$this->isPagePreview = $parser->getOptions()->getIsPreview();
			$this->isSectionPreview = $parser->getOptions()->getIsSectionPreview();
			$this->messageLocalizer = new ReferenceMessageLocalizer( $parser->getContentLanguage() );
			$this->errorReporter = new CiteErrorReporter(
				$parser->getOptions()->getUserLangObj(),
				$parser,
				$this->messageLocalizer
			);
			$this->referenceStack = new ReferenceStack( $this->errorReporter );
			$citeKeyFormatter = new CiteKeyFormatter( $this->messageLocalizer );
			$this->footnoteMarkFormatter = new FootnoteMarkFormatter(
				$this->mParser, $this->errorReporter, $citeKeyFormatter, $this->messageLocalizer );
			$this->footnoteBodyFormatter = new FootnoteBodyFormatter(
				$this->mParser, $this->errorReporter, $citeKeyFormatter, $this->messageLocalizer );
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
	public function ref( ?string $text, array $argv, Parser $parser ) {
		if ( $this->mInCite ) {
			return false;
		}

		$this->rememberParser( $parser );

		$this->mInCite = true;
		$ret = $this->guardedRef( $text, $argv, $parser );
		$this->mInCite = false;

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
	private function validateRef(
		?string $text,
		?string $name,
		?string $group,
		?string $follow,
		?string $extends
	) : StatusValue {
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

			// This doesn't catch the null case because that's guaranteed to trigger other errors
			// FIXME: We allow whitespace-only text, should this be invalid?  It leaves a
			//  loophole around the trimmed-text test outside of <references>.
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
					// No such named ref exists in this group.
					return StatusValue::newFatal( 'cite_error_references_missing_key',
						Sanitizer::safeEncodeAttribute( $name ) );
				}
			}
		} else {
			// Not in a references tag

			if ( !$name ) {
				if ( $text === null ) {
					// Something like <ref />; this makes no sense.
					return StatusValue::newFatal( 'cite_error_ref_no_key' );
				} elseif ( trim( $text ) === '' ) {
					// Must have content or reuse another ref by name.
					return StatusValue::newFatal( 'cite_error_ref_no_input' );
				}
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

			// TODO: Assert things such as $text is different than existing ref with $name,
			//  currently done in `pushRef`.
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
		?string $text,
		array $argv,
		Parser $parser
	) : string {
		// Tag every page where Book Referencing has been used, whether or not the ref tag is valid.
		// This code and the page property will be removed once the feature is stable.  See T237531.
		if ( array_key_exists( self::BOOK_REF_ATTRIBUTE, $argv ) ) {
			$parser->getOutput()->setProperty( self::BOOK_REF_PROPERTY, true );
		}

		$status = $this->parseArguments(
			$argv,
			[ 'dir', self::BOOK_REF_ATTRIBUTE, 'follow', 'group', 'name' ]
		);
		[
			'dir' => $dir,
			self::BOOK_REF_ATTRIBUTE => $extends,
			'follow' => $follow,
			'group' => $group,
			'name' => $name
		] = $status->getValue();

		// Use the default group, or the references group when inside one.
		if ( $group === null ) {
			$group = $this->inReferencesGroup ?? self::DEFAULT_GROUP;
		}

		$status->merge( $this->validateRef( $text, $name, $group, $follow, $extends ) );

		// Validation cares about the difference between null and empty, but from here on we don't
		if ( $text !== null && trim( $text ) === '' ) {
			$text = null;
		}

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

		$ref = $this->referenceStack->pushRef(
			$text, $name, $group, $extends, $follow, $argv, $dir, $parser->getStripState() );
		if ( $ref === null ) {
			return '';
		} else {
			return $this->footnoteMarkFormatter->linkRef( $group, $ref );
		}
	}

	/**
	 * @param string[] $argv The argument vector
	 * @param string[] $allowedAttributes Allowed attribute names
	 *
	 * @return StatusValue Either an error, or has a value with the dictionary of field names and
	 * parsed or default values.  Missing attributes will be `null`.
	 */
	private function parseArguments( array $argv, array $allowedAttributes ) : StatusValue {
		$maxCount = count( $allowedAttributes );
		$allValues = array_merge( array_fill_keys( $allowedAttributes, null ), $argv );
		$status = StatusValue::newGood( array_slice( $allValues, 0, $maxCount ) );

		if ( count( $allValues ) > $maxCount ) {
			// A <ref> must have a name (can be null), but <references> can't have one
			$status->fatal( in_array( 'name', $allowedAttributes )
				? 'cite_error_ref_too_many_keys'
				: 'cite_error_references_invalid_parameters'
			);
		}

		return $status;
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
	public function references( ?string $text, array $argv, Parser $parser ) {
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
		?string $text,
		array $argv,
		Parser $parser
	) : string {
		global $wgCiteResponsiveReferences;

		$status = $this->parseArguments(
			$argv,
			[ 'group', 'responsive' ]
		);
		[ 'group' => $group, 'responsive' => $responsive ] = $status->getValue();
		$this->inReferencesGroup = $group ?? self::DEFAULT_GROUP;

		if ( $text !== null && trim( $text ) !== '' ) {
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

		if ( !$status->isOK() ) {
			// Bail out with an error.
			$error = $status->getErrors()[0];
			return $this->errorReporter->halfParsed( $error['message'], ...$error['params'] );
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
	private function referencesFormat( string $group, bool $responsive ) : string {
		$ret = $this->footnoteBodyFormatter->referencesFormat(
			$this->referenceStack->getGroupRefs( $group ), $responsive, $this->isSectionPreview );

		// done, clean up so we can reuse the group
		$this->referenceStack->deleteGroup( $group );

		return $ret;
	}

	/**
	 * Gets run when Parser::clearState() gets run, since we don't
	 * want the counts to transcend pages and other instances
	 *
	 * @param string $force Set to "force" to interrupt parsing
	 */
	public function clearState( string $force = '' ) {
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
	 * @param bool $isSectionPreview
	 * @return string
	 */
	public function checkRefsNoReferences(
		bool $isSectionPreview
	) : string {
		global $wgCiteResponsiveReferences;

		$s = '';
		if ( $this->referenceStack ) {
			foreach ( $this->referenceStack->getGroups() as $group ) {
				if ( $group === self::DEFAULT_GROUP || $isSectionPreview ) {
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
		if ( $isSectionPreview && $s !== '' ) {
			$headerMsg = $this->messageLocalizer->msg( 'cite_section_preview_references' );
			if ( !$headerMsg->isDisabled() ) {
				$s = '<h2 id="mw-ext-cite-cite_section_preview_references_header" >'
					. $headerMsg->escaped()
					. '</h2>' . $s;
			}
			// provide a preview of references in its own section
			$s = "\n" . '<div class="mw-ext-cite-cite_section_preview_references" >' . $s . '</div>';
		}
		return $s;
	}

}
