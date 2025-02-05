<?php

namespace Cite;

use MediaWiki\Html\Html;
use MediaWiki\Parser\Parser;

/**
 * Renderer for the actual list of references in place of the <references /> tag at the end of an
 * article.
 *
 * @license GPL-2.0-or-later
 */
class ReferenceListFormatter {

	private ErrorReporter $errorReporter;
	private AnchorFormatter $anchorFormatter;
	private ReferenceMessageLocalizer $messageLocalizer;
	private BacklinkMarkRenderer $backlinkMarkRenderer;

	public function __construct(
		ErrorReporter $errorReporter,
		AnchorFormatter $anchorFormatter,
		BacklinkMarkRenderer $backlinkMarkRenderer,
		ReferenceMessageLocalizer $messageLocalizer
	) {
		$this->errorReporter = $errorReporter;
		$this->anchorFormatter = $anchorFormatter;
		$this->messageLocalizer = $messageLocalizer;
		$this->backlinkMarkRenderer = $backlinkMarkRenderer;
	}

	/**
	 * @param Parser $parser
	 * @param array<string|int,ReferenceStackItem> $groupRefs
	 * @param bool $responsive
	 * @param bool $isSectionPreview
	 *
	 * @return string HTML
	 */
	public function formatReferences(
		Parser $parser,
		array $groupRefs,
		bool $responsive,
		bool $isSectionPreview
	): string {
		if ( !$groupRefs ) {
			return '';
		}

		$wikitext = $this->formatRefsList( $parser, $groupRefs, $isSectionPreview );
		$html = $parser->recursiveTagParse( $wikitext );
		$html = Html::rawElement( 'ol', [ 'class' => 'references' ], $html );

		if ( $responsive ) {
			$wrapClasses = [ 'mw-references-wrap' ];
			if ( count( $groupRefs ) > 10 ) {
				$wrapClasses[] = 'mw-references-columns';
			}
			// Use a DIV wrap because column-count on a list directly is broken in Chrome.
			// See https://bugs.chromium.org/p/chromium/issues/detail?id=498730.
			return Html::rawElement( 'div', [ 'class' => $wrapClasses ], $html );
		}

		return $html;
	}

	/**
	 * @param Parser $parser
	 * @param array<string|int,ReferenceStackItem> $groupRefs
	 * @param bool $isSectionPreview
	 *
	 * @return string Wikitext
	 */
	private function formatRefsList(
		Parser $parser,
		array $groupRefs,
		bool $isSectionPreview
	): string {
		// After sorting the list, we can assume that references are in the same order as their
		// numbering.  Subreferences will come immediately after their parent.
		uasort(
			$groupRefs,
			static function ( ReferenceStackItem $a, ReferenceStackItem $b ): int {
				$cmp = ( $a->number ?? 0 ) - ( $b->number ?? 0 );
				return $cmp;
			}
		);

		// Add new lines between the list items (ref entries) to avoid confusing tidy (T15073).
		// Note: This builds a string of wikitext, not html.
		$parserInput = "\n";
		foreach ( $groupRefs as $key => $ref ) {
			$parserInput .= $this->formatListItem( $parser, $key, $ref, $isSectionPreview ) . "\n";
		}
		return $parserInput;
	}

	/**
	 * @param string|bool $closingLi
	 *
	 * @return string
	 */
	private function closeIndention( $closingLi ): string {
		if ( !$closingLi ) {
			return '';
		}

		return Html::closeElement( 'ol' ) . ( is_string( $closingLi ) ? $closingLi : '' );
	}

	/**
	 * @param Parser $parser
	 * @param string|int $key The key of the reference
	 * @param ReferenceStackItem $ref
	 * @param bool $isSectionPreview
	 *
	 * @return string Wikitext, wrapped in a single <li> element
	 */
	private function formatListItem(
		Parser $parser, $key, ReferenceStackItem $ref, bool $isSectionPreview
	): string {
		$text = $this->renderTextAndWarnings( $parser, $key, $ref, $isSectionPreview );

		// Special case for an incomplete follow="…". This is valid e.g. in the Page:… namespace on
		// Wikisource. Note this returns a <p>, not an <li> as expected!
		if ( $ref->follow !== null ) {
			return '<p id="' . $this->anchorFormatter->jumpLinkTarget( $ref->follow ) . '">' . $text . '</p>';
		}

		// Parameter $4 in the cite_references_link_one and cite_references_link_many messages
		$extraAttributes = '';
		if ( $ref->dir !== null ) {
			// The following classes are generated here:
			// * mw-cite-dir-ltr
			// * mw-cite-dir-rtl
			$extraAttributes = Html::expandAttributes( [ 'class' => 'mw-cite-dir-' . $ref->dir ] );
		}

		if ( $ref->count === 1 ) {
			if ( $ref->name === null ) {
				$id = $ref->key;
				$backlinkId = $this->anchorFormatter->backLink( $ref->key );
			} else {
				$id = $key . '-' . $ref->key;
				// TODO: Use count without decrementing.
				$backlinkId = $this->anchorFormatter->backLink( $key, $ref->key . '-' . ( $ref->count - 1 ) );
			}
			return $this->messageLocalizer->msg(
				'cite_references_link_one',
				$this->anchorFormatter->jumpLinkTarget( $id ),
				$backlinkId,
				$text,
				$extraAttributes
			)->plain();
		}

		$backlinks = [];
		for ( $i = 0; $i < $ref->count; $i++ ) {
			if ( $this->backlinkMarkRenderer->isLegacyMode() ) {
				// FIXME: parent mark should be explicitly markSymbolRenderer'd if it
				// stays here.
				$parentLabel = $this->messageLocalizer->localizeDigits( (string)$ref->number );

				$backlinks[] = $this->messageLocalizer->msg(
					'cite_references_link_many_format',
					$this->anchorFormatter->backLink( $key, $ref->key . '-' . $i ),
					$this->backlinkMarkRenderer->getLegacyNumericMarker( $i, $ref->count, $parentLabel ),
					$this->backlinkMarkRenderer->getLegacyAlphabeticMarker( $i + 1, $ref->count, $parentLabel )
				)->plain();
			} else {
				$backlinkLabel = $this->backlinkMarkRenderer->getBacklinkMarker( $i + 1 );

				$backlinks[] = $this->messageLocalizer->msg(
					'cite_references_link_many_format',
					$this->anchorFormatter->backLink( $key, $ref->key . '-' . $i ),
					$backlinkLabel,
					$backlinkLabel
				)->plain();
			}
		}

		// The parent of a subref might actually be unused and therefore have zero backlinks
		$linkTargetId = $ref->count > 0 ?
			$this->anchorFormatter->jumpLinkTarget( $key . '-' . $ref->key ) : '';
		return $this->messageLocalizer->msg(
			'cite_references_link_many',
			$linkTargetId,
			$this->listToText( $backlinks ),
			$text,
			$extraAttributes
		)->plain();
	}

	/**
	 * @param Parser $parser
	 * @param string|int $key
	 * @param ReferenceStackItem $ref
	 * @param bool $isSectionPreview
	 *
	 * @return string Wikitext
	 */
	private function renderTextAndWarnings(
		Parser $parser, $key, ReferenceStackItem $ref, bool $isSectionPreview
	): string {
		if ( $ref->text === null ) {
			return $this->errorReporter->plain( $parser,
				$isSectionPreview
					? 'cite_warning_sectionpreview_no_text'
					: 'cite_error_references_no_text', $key );
		}

		$text = $ref->text ?? '';
		foreach ( $ref->warnings as $warning ) {
			// @phan-suppress-next-line PhanParamTooFewUnpack
			$text .= ' ' . $this->errorReporter->plain( $parser, ...$warning );
			// FIXME: We could use a StatusValue object to get rid of duplicates
			break;
		}

		return '<span class="reference-text">' . rtrim( $text, "\n" ) . "</span>\n";
	}

	/**
	 * This does approximately the same thing as
	 * Language::listToText() but due to this being used for a
	 * slightly different purpose (people might not want , as the
	 * first separator and not 'and' as the second, and this has to
	 * use messages from the content language) I'm rolling my own.
	 *
	 * @param string[] $arr The array to format
	 *
	 * @return string Wikitext
	 */
	private function listToText( array $arr ): string {
		$lastElement = array_pop( $arr );

		if ( $arr === [] ) {
			return (string)$lastElement;
		}

		$sep = $this->messageLocalizer->msg( 'cite_references_link_many_sep' )->plain();
		$and = $this->messageLocalizer->msg( 'cite_references_link_many_and' )->plain();
		return implode( $sep, $arr ) . $and . $lastElement;
	}

}
