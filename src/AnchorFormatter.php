<?php

namespace Cite;

use MediaWiki\Parser\Sanitizer;

/**
 * @license GPL-2.0-or-later
 */
class AnchorFormatter {

	private ReferenceMessageLocalizer $messageLocalizer;

	public function __construct( ReferenceMessageLocalizer $messageLocalizer ) {
		$this->messageLocalizer = $messageLocalizer;
	}

	/**
	 * Return an id for use in wikitext output based on a key and
	 * optionally the number of it, used in <references>, not <ref>
	 * (since otherwise it would link to itself)
	 *
	 * @param string $key
	 * @param string|null $num The number of the key
	 *
	 * @return string
	 */
	private function refKey( string $key, ?string $num ): string {
		$prefix = $this->messageLocalizer->msg( 'cite_reference_link_prefix' )->plain();
		if ( $num !== null ) {
			$key .= '_' . $num;
		}
		return $this->normalizeKey( $prefix . $key );
	}

	/**
	 * @param string $key
	 * @param string|null $num
	 * @return string Escaped to be used as part of a [[#…]] link
	 */
	public function backLink( string $key, ?string $num = null ): string {
		$key = $this->refKey( $key, $num );
		// This does both URL encoding (e.g. %A0, which only makes sense in href="…") and HTML
		// entity encoding (e.g. &#xA0;). The browser will decode in reverse order.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	/**
	 * @param string $key
	 * @param string|null $num
	 * @return string Already escaped to be used directly in an id="…" attribute
	 */
	public function backLinkTarget( string $key, ?string $num ): string {
		$key = $this->refKey( $key, $num );
		// FIXME: This does both URL encoding (%A0) as well as HTML entity encoding (&#xA0;), but
		// URL encoding only makes sense in links.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	/**
	 * Return an id for use in wikitext output based on a key and
	 * optionally the number of it, used in <ref>, not <references>
	 * (since otherwise it would link to itself)
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function getReferencesKey( string $key ): string {
		$prefix = $this->messageLocalizer->msg( 'cite_references_link_prefix' )->plain();
		return $this->normalizeKey( $prefix . $key );
	}

	/**
	 * @param string $key
	 * @return string Escaped to be used as part of a [[#…]] link
	 */
	public function jumpLink( string $key ): string {
		$key = $this->getReferencesKey( $key );
		// This does both URL encoding (e.g. %A0, which only makes sense in href="…") and HTML
		// entity encoding (e.g. &#xA0;). The browser will decode in reverse order.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	/**
	 * @param string $key
	 * @return string Already escaped to be used directly in an id="…" attribute
	 */
	public function jumpLinkTarget( string $key ): string {
		$key = $this->getReferencesKey( $key );
		// FIXME: This does both URL encoding (%A0) as well as HTML entity encoding (&#xA0;), but
		// URL encoding only makes sense in links.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	private function normalizeKey( string $key ): string {
		// MediaWiki normalizes spaces and underscores in [[#…]] links, but not in id="…"
		// attributes. To make them behave the same we normalize in advance.
		return preg_replace( '/[_\s]+/u', '_', $key );
	}

}
