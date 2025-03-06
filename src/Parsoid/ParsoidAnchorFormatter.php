<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Cite\AnchorFormatter;
use Wikimedia\Parsoid\Core\Sanitizer;

/**
 * Contains small pieces of additional knowledge that is specific to the Parsoid implementation and
 * doesn't belong to the generic {@see AnchorFormatter}.
 *
 * @license GPL-2.0-or-later
 */
class ParsoidAnchorFormatter {

	/**
	 * The clickable href="#…" link and id="…" target to jump from a footnote marker in the article
	 * down to the corresponding "note" item in the reference list.
	 */
	public static function getNoteIdentifier( RefGroupItem $ref ): string {
		$id = AnchorFormatter::getNoteIdentifier( $ref->name, $ref->globalId );
		// FIXME: This extra escaping is probably a mistake!
		return Sanitizer::escapeIdForLink( $id );
	}

	/**
	 * Identifier for the inner <span class="mw-reference-text"> node.
	 */
	public static function getNoteTextIdentifier( RefGroupItem $ref ): string {
		return 'mw-reference-text-' .
			AnchorFormatter::getNoteIdentifier( $ref->name, $ref->globalId );
	}

	/**
	 * The clickable href="#…" backlink and id="…" target to jump from the reference list back up to
	 * one of the possibly many footnote markers in the article.
	 */
	public static function getBackLinkIdentifier( RefGroupItem $ref, ?int $count = null ): string {
		$id = AnchorFormatter::getBackLinkIdentifier( $ref->name, $ref->globalId,
			$count ?? $ref->visibleNodes );
		// FIXME: This extra escaping is probably a mistake!
		return Sanitizer::escapeIdForLink( $id );
	}

}
