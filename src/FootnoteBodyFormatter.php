<?php

namespace Cite;

use Html;
use MediaWiki\MediaWikiServices;
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
	 * @var string[]
	 */
	private $backlinkLabels;

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
	 * @param Parser $parser
	 * @param CiteErrorReporter $errorReporter
	 * @param CiteKeyFormatter $citeKeyFormatter
	 */
	public function __construct(
		Parser $parser,
		CiteErrorReporter $errorReporter,
		CiteKeyFormatter $citeKeyFormatter
	) {
		$this->errorReporter = $errorReporter;
		$this->citeKeyFormatter = $citeKeyFormatter;
		$this->parser = $parser;
	}

	/**
	 * @param array $groupRefs
	 * @param bool $responsive
	 * @param bool $isSectionPreview
	 * @return string
	 */
	public function referencesFormat( array $groupRefs, bool $responsive, bool $isSectionPreview ) {
		if ( !$groupRefs ) {
			return '';
		}

		// Add new lines between the list items (ref entries) to avoid confusing tidy (T15073).
		// Note: This builds a string of wikitext, not html.
		$parserInput = "\n";
		$indented = false;
		foreach ( $groupRefs as $key => $value ) {
			// FIXME: This assumes extended references appear immediately after their parent in the
			//  array.  Reorder the refs according to their stored numbering.
			if ( !$indented && isset( $value['extends'] ) ) {
				// Hack: The nested <ol> needs to go inside of the <li>.
				$parserInput = preg_replace( '/<\/li>\s*$/', '', $parserInput );
				$parserInput .= Html::openElement( 'ol', [ 'class' => 'mw-extended-references' ] );
				$indented = true;
			} elseif ( $indented && !isset( $value['extends'] ) ) {
				// FIXME: This is't closed if there is no unindented element at the end
				$parserInput .= Html::closeElement( 'ol' );
				$indented = false;
			}
			$parserInput .= $this->referencesFormatEntry(
					$key, $value, $isSectionPreview ) . "\n";
		}
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
			return Html::rawElement( 'div', [ 'class' => $wrapClasses ], $ret );
		} else {
			return $ret;
		}
	}

	/**
	 * Format a single entry for the referencesFormat() function
	 *
	 * @param string|int $key The key of the reference
	 * @param array $val A single reference as documented at {@see ReferenceStack::$refs}
	 * @param bool $isSectionPreview
	 * @return string Wikitext, wrapped in a single <li> element
	 */
	private function referencesFormatEntry( $key, array $val, bool $isSectionPreview ) {
		$text = $this->referenceText( $key, $val['text'], $isSectionPreview );
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
				$this->citeKeyFormatter->getReferencesKey( $val['follow'] ),
				$text
			)->inContentLanguage()->plain();
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
			return wfMessage(
				'cite_references_link_one',
				$this->citeKeyFormatter->getReferencesKey( $id ),
				$backlinkId,
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
				$this->citeKeyFormatter->refKey( $key, $val['key'] . '-' . $i ),
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
			$this->citeKeyFormatter->getReferencesKey( $key . '-' . ( $val['key'] ?? '' ) ),
			$this->listToText( $backlinks ),
			$text . $error,
			$extraAttributes
		)->inContentLanguage()->plain();
	}

	/**
	 * Returns formatted reference text
	 * @param string|int $key
	 * @param ?string $text
	 * @param bool $isSectionPreview
	 * @return string
	 */
	private function referenceText( $key, ?string $text, bool $isSectionPreview ) {
		if ( trim( $text ) === '' ) {
			if ( $isSectionPreview ) {
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
			$base . '.' . sprintf( "%0{$scope}s", $offset )
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
		if ( !isset( $this->backlinkLabels ) ) {
			$this->genBacklinkLabels();
		}
		return $this->backlinkLabels[$offset]
			?? $this->errorReporter->plain( 'cite_error_references_no_backlink_label', null );
	}

	/**
	 * Generate the labels to pass to the
	 * 'cite_references_link_many_format' message, the format is an
	 * arbitrary number of tokens separated by whitespace.
	 */
	private function genBacklinkLabels() {
		$text = wfMessage( 'cite_references_link_many_format_backlink_labels' )
			->inContentLanguage()->plain();
		$this->backlinkLabels = preg_split( '/\s+/', $text );
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

}
