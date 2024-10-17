<?php

namespace Cite;

use MediaWiki\Config\Config;
use MediaWiki\Html\Html;
use MediaWiki\Parser\Parser;

/**
 * Renderer for the actual list of references in place of the <references /> tag at the end of an
 * article.
 *
 * @license GPL-2.0-or-later
 */
class ReferenceListFormatter {

	/**
	 * The backlinks, in order, to pass as $3 to
	 * 'cite_references_link_many_format', defined in
	 * 'cite_references_link_many_format_backlink_labels
	 *
	 * @var string[]|null
	 */
	private ?array $backlinkLabels = null;
	private ErrorReporter $errorReporter;
	private AnchorFormatter $anchorFormatter;
	private ReferenceMessageLocalizer $messageLocalizer;
	private MarkSymbolRenderer $markSymbolRenderer;
	private Config $config;

	public function __construct(
		ErrorReporter $errorReporter,
		AnchorFormatter $anchorFormatter,
		MarkSymbolRenderer $markSymbolRenderer,
		ReferenceMessageLocalizer $messageLocalizer,
		Config $config
	) {
		$this->errorReporter = $errorReporter;
		$this->anchorFormatter = $anchorFormatter;
		$this->messageLocalizer = $messageLocalizer;
		$this->markSymbolRenderer = $markSymbolRenderer;
		$this->config = $config;
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
				return $cmp ?: ( $a->extendsIndex ?? 0 ) - ( $b->extendsIndex ?? 0 );
			}
		);

		// Add new lines between the list items (ref entries) to avoid confusing tidy (T15073).
		// Note: This builds a string of wikitext, not html.
		$parserInput = "\n";
		/** @var string|bool $indented */
		$indented = false;
		foreach ( $groupRefs as $key => &$ref ) {
			// Make sure the parent is not a subreference.
			// FIXME: Move to a validation function.
			$extends =& $ref->extends;
			if ( $extends !== null && isset( $groupRefs[$extends] ) && $groupRefs[$extends]->extends !== null ) {
				$ref->warnings[] = [ 'cite_error_ref_nested_extends',
					$extends, $groupRefs[$extends]->extends ];
			}

			if ( !$indented && $extends !== null ) {
				// The nested <ol> must be inside the parent's <li>
				if ( preg_match( '#</li>\s*$#D', $parserInput, $matches, PREG_OFFSET_CAPTURE ) ) {
					$parserInput = substr( $parserInput, 0, $matches[0][1] );
				}
				$parserInput .= Html::openElement( 'ol', [ 'class' => 'mw-extended-references' ] );
				$indented = $matches[0][0] ?? true;
			} elseif ( $indented && $extends === null ) {
				$parserInput .= $this->closeIndention( $indented );
				$indented = false;
			}
			$parserInput .= $this->formatListItem( $parser, $key, $ref, $isSectionPreview ) . "\n";
		}
		$parserInput .= $this->closeIndention( $indented );
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
		if ( !$this->config->get( 'CiteUseLegacyBacklinkLabels' ) ) {
			$backlinkAlphabet = $this->markSymbolRenderer->getBacklinkAlphabet(
				$parser->getContentLanguage()->getCode()
			);
		}

		$alternateLabel = '';
		for ( $i = 0; $i < $ref->count; $i++ ) {
			$numericLabel = $this->referencesFormatEntryNumericBacklinkLabel(
				$ref->number . ( $ref->extendsIndex ? '.' . $ref->extendsIndex : '' ),
				$i,
				$ref->count
			);

			if ( isset( $backlinkAlphabet ) ) {
				$alphaBacklinkLabel = $this->markSymbolRenderer->makeBacklinkLabel(
					$backlinkAlphabet,
					$i + 1
				);
			}

			// TODO Allow transition away from the numeric default labels
			$backlinkLabel = $alphaBacklinkLabel ?? $numericLabel;

			// Stop trying after the first failure, critical so the error appears only once
			if ( $alternateLabel !== null ) {
				$alternateLabel = $this->referencesFormatEntryAlternateBacklinkLabel( $ref, $i );
			}

			$backlinks[] = $this->messageLocalizer->msg(
				'cite_references_link_many_format',
				$this->anchorFormatter->backLink( $key, $ref->key . '-' . $i ),
				// TODO Eventually we'll make $2 and $3 behave the same and remove
				// `cite_references_link_many_format_backlink_labels`
				$backlinkLabel,
				// Fallback when we run out of alternate labels or they are disabled
				$alternateLabel ?? $backlinkLabel
			)->plain();
		}

		// Duplicate call required for the rendering to include all new errors
		$text = $this->renderTextAndWarnings( $parser, $key, $ref, $isSectionPreview );

		// The parent of a subref might actually be unused and therefor have zero backlinks
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
	 * Generate a numeric backlink given a base number and an
	 * offset, e.g. $base = 1, $offset = 2; = 1.2
	 * Since bug #5525, it correctly does 1.9 -> 1.10 as well as 1.099 -> 1.100
	 *
	 * @param string $base
	 * @param int $offset
	 * @param int $max Maximum value expected.
	 *
	 * @return string
	 */
	private function referencesFormatEntryNumericBacklinkLabel(
		string $base,
		int $offset,
		int $max
	): string {
		return $this->messageLocalizer->localizeDigits( $base ) .
			$this->messageLocalizer->localizeSeparators( '.' ) .
			$this->messageLocalizer->localizeDigits(
				str_pad( (string)$offset, strlen( (string)$max ), '0', STR_PAD_LEFT )
			);
	}

	/**
	 * Return one of the custom backlink labels from the list in the message
	 * [[MediaWiki:Cite_references_link_many_format_backlink_labels]].
	 *
	 * @return string|null Null when we run out of alternate labels or they are disabled
	 */
	private function referencesFormatEntryAlternateBacklinkLabel(
		ReferenceStackItem $ref,
		int $offset
	): ?string {
		if ( $this->backlinkLabels === null ) {
			$msg = $this->messageLocalizer->msg( 'cite_references_link_many_format_backlink_labels' );
			// Disabling the message just disables the feature
			$this->backlinkLabels = $msg->isDisabled() ? [] : preg_split( '/\s+/', $msg->plain() );
		}

		if ( $this->backlinkLabels && !isset( $this->backlinkLabels[$offset] ) ) {
			$ref->warnings[] = [ 'cite_error_references_no_backlink_label' ];
		}

		return $this->backlinkLabels[$offset] ?? null;
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
