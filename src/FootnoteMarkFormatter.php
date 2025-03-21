<?php

namespace Cite;

use MediaWiki\Parser\Parser;
use MediaWiki\Parser\Sanitizer;

/**
 * Footnote markers in the context of the Cite extension are the numbers in the article text, e.g.
 * [1], that can be hovered or clicked to be able to read the attached footnote.
 *
 * @license GPL-2.0-or-later
 */
class FootnoteMarkFormatter {

	private AnchorFormatter $anchorFormatter;
	private MarkSymbolRenderer $markSymbolRenderer;
	private ReferenceMessageLocalizer $messageLocalizer;

	public function __construct(
		AnchorFormatter $anchorFormatter,
		MarkSymbolRenderer $markSymbolRenderer,
		ReferenceMessageLocalizer $messageLocalizer
	) {
		$this->anchorFormatter = $anchorFormatter;
		$this->markSymbolRenderer = $markSymbolRenderer;
		$this->messageLocalizer = $messageLocalizer;
	}

	/**
	 * Generate a link (<sup ...) for the <ref> element from a key
	 * and return XHTML ready for output
	 *
	 * @suppress SecurityCheck-DoubleEscaped
	 * @param Parser $parser
	 * @param ReferenceStackItem $ref
	 *
	 * @return string HTML
	 */
	public function linkRef( Parser $parser, ReferenceStackItem $ref ): string {
		$label = $this->markSymbolRenderer->makeLabel( $ref->group, $ref->numberInGroup, $ref->subrefIndex );
		return $parser->recursiveTagParse(
			$this->messageLocalizer->msg(
				'cite_reference_link',
				$this->anchorFormatter->backLinkTarget( $ref->name, $ref->globalId, $ref->count ),
				$this->anchorFormatter->jumpLink( $ref->name, $ref->globalId ),
				Sanitizer::safeEncodeAttribute( $label )
			)->plain()
		);
	}
}
