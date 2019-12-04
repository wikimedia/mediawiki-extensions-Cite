<?php

namespace Cite;

use Html;
use Parser;

/**
 * @license GPL-2.0-or-later
 */
class FootnoteBodyFormatter {

	/**
	 * The backlinks, in order, to pass as $3 to
	 * 'cite_references_link_many_format', defined in
	 * 'cite_references_link_many_format_backlink_labels
	 *
	 * @var string[]|null
	 */
	private $backlinkLabels = null;

	/**
	 * @var CiteErrorReporter
	 */
	private $errorReporter;

	/**
	 * @var CiteKeyFormatter
	 */
	private $citeKeyFormatter;

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var ReferenceMessageLocalizer
	 */
	private $messageLocalizer;

	/**
	 * @param Parser $parser
	 * @param CiteErrorReporter $errorReporter
	 * @param CiteKeyFormatter $citeKeyFormatter
	 * @param ReferenceMessageLocalizer $messageLocalizer
	 */
	public function __construct(
		Parser $parser,
		CiteErrorReporter $errorReporter,
		CiteKeyFormatter $citeKeyFormatter,
		ReferenceMessageLocalizer $messageLocalizer
	) {
		$this->errorReporter = $errorReporter;
		$this->citeKeyFormatter = $citeKeyFormatter;
		$this->parser = $parser;
		$this->messageLocalizer = $messageLocalizer;
	}

	/**
	 * @param array $groupRefs
	 * @param bool $responsive
	 * @param bool $isSectionPreview
	 * @return string
	 */
	public function referencesFormat(
		array $groupRefs,
		bool $responsive,
		bool $isSectionPreview
	) : string {
		if ( !$groupRefs ) {
			return '';
		}

		// Add new lines between the list items (ref entries) to avoid confusing tidy (T15073).
		// Note: This builds a string of wikitext, not html.
		$parserInput = "\n";
		/** @var string|bool $indented */
		$indented = false;
		// After sorting the list, we can assume that references are in the same order as their
		// numbering.  Subreferences will come immediately after their parent.
		uasort(
			$groupRefs,
			function ( array $a, array $b ) : int {
				$cmp = ( $a['number'] ?? 0 ) - ( $b['number'] ?? 0 );
				return $cmp ?: ( $a['extendsIndex'] ?? 0 ) - ( $b['extendsIndex'] ?? 0 );
			}
		);
		foreach ( $groupRefs as $key => $value ) {
			if ( !$indented && isset( $value['extends'] ) ) {
				// The nested <ol> must be inside the parent's <li>
				if ( preg_match( '#</li>\s*$#D', $parserInput, $matches, PREG_OFFSET_CAPTURE ) ) {
					$parserInput = substr( $parserInput, 0, $matches[0][1] );
				}
				$parserInput .= Html::openElement( 'ol', [ 'class' => 'mw-extended-references' ] );
				$indented = $matches[0][0] ?? true;
			} elseif ( $indented && !isset( $value['extends'] ) ) {
				$parserInput .= $this->closeIndention( $indented );
				$indented = false;
			}
			// Make sure the parent is not a subreference.
			// FIXME: Move to a validation function.
			if ( isset( $value['extends'] ) &&
				isset( $groupRefs[$value['extends']]['extends'] )
			) {
				$value['text'] = ( $value['text'] ?? '' ) .
					$this->errorReporter->plain( 'cite_error_ref_too_many_keys' );
			}
			$parserInput .= $this->referencesFormatEntry( $key, $value, $isSectionPreview ) . "\n";
		}
		$parserInput .= $this->closeIndention( $indented );
		$parserInput = Html::rawElement( 'ol', [ 'class' => [ 'references' ] ], $parserInput );
		// Live hack: parse() adds two newlines on WM, can't reproduce it locally -ævar
		$ret = rtrim( $this->parser->recursiveTagParse( $parserInput ), "\n" );

		if ( $responsive ) {
			// Use a DIV wrap because column-count on a list directly is broken in Chrome.
			// See https://bugs.chromium.org/p/chromium/issues/detail?id=498730.
			$wrapClasses = [ 'mw-references-wrap' ];
			if ( count( $groupRefs ) > 10 ) {
				$wrapClasses[] = 'mw-references-columns';
			}
			$ret = Html::rawElement( 'div', [ 'class' => $wrapClasses ], $ret );
		}

		return $ret;
	}

	/**
	 * @param string|bool $closingLi
	 *
	 * @return string
	 */
	private function closeIndention( $closingLi ) : string {
		if ( !$closingLi ) {
			return '';
		}

		return Html::closeElement( 'ol' ) . ( is_string( $closingLi ) ? $closingLi : '' );
	}

	/**
	 * Format a single entry for the referencesFormat() function
	 *
	 * @param string|int $key The key of the reference
	 * @param array $val A single reference as documented at {@see ReferenceStack::$refs}
	 * @param bool $isSectionPreview
	 * @return string Wikitext, wrapped in a single <li> element
	 */
	private function referencesFormatEntry( $key, array $val, bool $isSectionPreview ) : string {
		$text = $this->referenceText( $key, $val['text'] ?? null, $isSectionPreview );
		$error = '';
		$extraAttributes = '';

		if ( isset( $val['dir'] ) ) {
			$dir = strtolower( $val['dir'] );
			if ( in_array( $dir, [ 'ltr', 'rtl' ] ) ) {
				$extraAttributes = Html::expandAttributes( [ 'class' => 'mw-cite-dir-' . $dir ] );
			} else {
				// TODO: Move to validation.
				$error .= $this->errorReporter->plain( 'cite_error_ref_invalid_dir', $val['dir'] ) . "\n";
			}
		}

		// Fallback for a broken, and therefore unprocessed follow="…". Note this returns a <p>, not
		// an <li> as expected!
		if ( isset( $val['follow'] ) ) {
			return $this->messageLocalizer->msg(
				'cite_references_no_link',
				$this->citeKeyFormatter->getReferencesKey( $val['follow'] ),
				$text
			)->plain();
		}

		// This counts the number of reuses. 0 means the reference appears only 1 time.
		if ( isset( $val['count'] ) && $val['count'] < 1 ) {
			// Anonymous, auto-numbered references can't be reused and get marked with a -1.
			if ( $val['count'] < 0 ) {
				$id = $val['key'];
				$backlinkId = $this->citeKeyFormatter->refKey( $val['key'] );
			} else {
				$id = $key . '-' . $val['key'];
				$backlinkId = $this->citeKeyFormatter->refKey( $key, $val['key'] . '-' . $val['count'] );
			}
			return $this->messageLocalizer->msg(
				'cite_references_link_one',
				$this->citeKeyFormatter->getReferencesKey( $id ),
				$backlinkId,
				$text . $error,
				$extraAttributes
			)->plain();
		}

		// Named references with >1 occurrences
		$backlinks = [];
		// There is no count in case of a section preview
		for ( $i = 0; $i <= ( $val['count'] ?? -1 ); $i++ ) {
			$backlinks[] = $this->messageLocalizer->msg(
				'cite_references_link_many_format',
				$this->citeKeyFormatter->refKey( $key, $val['key'] . '-' . $i ),
				$this->referencesFormatEntryNumericBacklinkLabel(
					$val['number'] .
						( isset( $val['extendsIndex'] ) ? '.' . $val['extendsIndex'] : '' ),
					$i,
					$val['count']
				),
				$this->referencesFormatEntryAlternateBacklinkLabel( $i )
			)->plain();
		}
		return $this->messageLocalizer->msg(
			'cite_references_link_many',
			$this->citeKeyFormatter->getReferencesKey( $key . '-' . ( $val['key'] ?? '' ) ),
			$this->listToText( $backlinks ),
			$text . $error,
			$extraAttributes
		)->plain();
	}

	/**
	 * Returns formatted reference text
	 *
	 * @param string|int $key
	 * @param ?string $text
	 * @param bool $isSectionPreview
	 *
	 * @return string
	 */
	private function referenceText( $key, ?string $text, bool $isSectionPreview ) : string {
		if ( $text === null ) {
			return $this->errorReporter->plain( $isSectionPreview
				? 'cite_warning_sectionpreview_no_text'
				: 'cite_error_references_no_text', $key );
		}

		return '<span class="reference-text">' . rtrim( $text, "\n" ) . "</span>\n";
	}

	/**
	 * Generate a numeric backlink given a base number and an
	 * offset, e.g. $base = 1, $offset = 2; = 1.2
	 * Since bug #5525, it correctly does 1.9 -> 1.10 as well as 1.099 -> 1.100
	 *
	 * @param int|string $base
	 * @param int $offset
	 * @param int $max Maximum value expected.
	 *
	 * @return string
	 */
	private function referencesFormatEntryNumericBacklinkLabel(
		$base,
		int $offset,
		int $max
	) : string {
		return $this->messageLocalizer->getLanguage()->formatNum(
			$base .
			'.' .
			str_pad( $offset, strlen( $max ), '0', STR_PAD_LEFT )
		);
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
	private function referencesFormatEntryAlternateBacklinkLabel( int $offset ) : string {
		if ( $this->backlinkLabels === null ) {
			$this->backlinkLabels = preg_split(
				'/\s+/',
				$this->messageLocalizer->msg( 'cite_references_link_many_format_backlink_labels' )
					->plain()
			);
		}

		return $this->backlinkLabels[$offset]
			?? $this->errorReporter->plain( 'cite_error_references_no_backlink_label' );
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
	private function listToText( array $arr ) : string {
		$lastElement = array_pop( $arr );

		if ( $arr === [] ) {
			return (string)$lastElement;
		}

		$sep = $this->messageLocalizer->msg( 'cite_references_link_many_sep' )->plain();
		$and = $this->messageLocalizer->msg( 'cite_references_link_many_and' )->plain();
		return implode( $sep, $arr ) . $and . $lastElement;
	}

}
