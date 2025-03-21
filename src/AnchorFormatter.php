<?php

namespace Cite;

use MediaWiki\Parser\Sanitizer;

/**
 * Compiles unique identifiers and formats them as anchors for use in `href="#…"` and `id="…"`
 * attributes.
 *
 * @license GPL-2.0-or-later
 */
class AnchorFormatter {

	/**
	 * Generates identifiers for use in backlinks and their targets to jump from the reference list
	 * back up to one of possibly many footnote markers in the text.
	 */
	private function getBackLinkIdentifier( ?string $name, int $globalId, int $count ): string {
		// Note this intentionally drops "0" and such, that's invalid anyway
		if ( $name ) {
			// TODO: Can we change this to use the number as it is, without decrementing?
			$id = "cite_ref-{$name}_$globalId-" . ( $count - 1 );
		} else {
			$id = "cite_ref-$globalId";
		}
		return $this->normalizeFragmentIdentifier( $id );
	}

	/**
	 * @return string Escaped to be used as part of a [[#…]] link
	 */
	public function backLink( ?string $name, int $globalId, int $count ): string {
		$id = $this->getBackLinkIdentifier( $name, $globalId, $count );
		// This does both URL encoding (e.g. %A0, which only makes sense in href="…") and HTML
		// entity encoding (e.g. &#xA0;). The browser will decode in reverse order.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $id ) );
	}

	/**
	 * @return string Already escaped to be used directly in an id="…" attribute
	 */
	public function backLinkTarget( ?string $name, int $globalId, int $count ): string {
		$id = $this->getBackLinkIdentifier( $name, $globalId, $count );
		return Sanitizer::safeEncodeAttribute( $id );
	}

	/**
	 * Generates identifiers for use in reference links and their targets to jump from a footnote
	 * marker in the text down to the corresponding item in the reference list.
	 */
	private function getJumpLinkIdentifier( ?string $name, int $globalId ): string {
		// Note this intentionally drops "0" and such, that's invalid anyway
		if ( $name ) {
			$id = "cite_note-$name-$globalId";
		} else {
			$id = "cite_note-$globalId";
		}
		// TODO: Get rid of this random special case for follow, probably not even needed
		if ( !$globalId ) {
			$id = "cite_note-$name";
		}
		return $this->normalizeFragmentIdentifier( $id );
	}

	/**
	 * @return string Escaped to be used as part of a [[#…]] link
	 */
	public function jumpLink( ?string $name, int $globalId ): string {
		$id = $this->getJumpLinkIdentifier( $name, $globalId );
		// This does both URL encoding (e.g. %A0, which only makes sense in href="…") and HTML
		// entity encoding (e.g. &#xA0;). The browser will decode in reverse order.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $id ) );
	}

	/**
	 * @return string Already escaped to be used directly in an id="…" attribute
	 */
	public function jumpLinkTarget( ?string $name, int $globalId ): string {
		$id = $this->getJumpLinkIdentifier( $name, $globalId );
		return Sanitizer::safeEncodeAttribute( $id );
	}

	/**
	 * Normalizes and sanitizes anchor names for use in id="…" and <a href="#…"> attributes.
	 */
	private function normalizeFragmentIdentifier( string $id ): string {
		// MediaWiki normalizes spaces and underscores in [[#…]] links, but not in id="…"
		// attributes. To make them behave the same we normalize in advance.
		return preg_replace( '/[_\s]+/u', '_', $id );
	}

}
