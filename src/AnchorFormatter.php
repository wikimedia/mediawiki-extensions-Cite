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
	 *
	 * @param string|int $key
	 * @param string|null $num The number of the key
	 * @return string
	 */
	private function getBackLinkIdentifier( $key, ?string $num ): string {
		if ( $num !== null ) {
			$key = $key . '_' . $num;
		}
		return $this->normalizeFragmentIdentifier( "cite_ref-$key" );
	}

	/**
	 * @param string|int $key
	 * @param string|null $num
	 * @return string Escaped to be used as part of a [[#…]] link
	 */
	public function backLink( $key, ?string $num = null ): string {
		$key = $this->getBackLinkIdentifier( $key, $num );
		// This does both URL encoding (e.g. %A0, which only makes sense in href="…") and HTML
		// entity encoding (e.g. &#xA0;). The browser will decode in reverse order.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	/**
	 * @param string|int $key
	 * @param string|null $num
	 * @return string Already escaped to be used directly in an id="…" attribute
	 */
	public function backLinkTarget( $key, ?string $num ): string {
		$key = $this->getBackLinkIdentifier( $key, $num );
		return Sanitizer::safeEncodeAttribute( $key );
	}

	/**
	 * Generates identifiers for use in reference links and their targets to jump from a footnote
	 * marker in the text down to the corresponding item in the reference list.
	 */
	private function getJumpLinkIdentifier( string $key ): string {
		return $this->normalizeFragmentIdentifier( "cite_note-$key" );
	}

	/**
	 * @param string $key
	 * @return string Escaped to be used as part of a [[#…]] link
	 */
	public function jumpLink( string $key ): string {
		$key = $this->getJumpLinkIdentifier( $key );
		// This does both URL encoding (e.g. %A0, which only makes sense in href="…") and HTML
		// entity encoding (e.g. &#xA0;). The browser will decode in reverse order.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	/**
	 * @param string $key
	 * @return string Already escaped to be used directly in an id="…" attribute
	 */
	public function jumpLinkTarget( string $key ): string {
		$key = $this->getJumpLinkIdentifier( $key );
		return Sanitizer::safeEncodeAttribute( $key );
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
