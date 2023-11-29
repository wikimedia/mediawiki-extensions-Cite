<?php

namespace Cite;

use Sanitizer;

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
	 * @return string A key for use in wikitext
	 */
	public function refKey( string $key, string $num = null ): string {
		$prefix = $this->messageLocalizer->msg( 'cite_reference_link_prefix' )->plain();
		$suffix = $this->messageLocalizer->msg( 'cite_reference_link_suffix' )->plain();
		if ( $num !== null ) {
			$key = $this->messageLocalizer->msg( 'cite_reference_link_key_with_num', $key, $num )
				->plain();
		}

		return $this->normalizeAndEncode( $prefix . $key . $suffix );
	}

	/**
	 * Return an id for use in wikitext output based on a key and
	 * optionally the number of it, used in <ref>, not <references>
	 * (since otherwise it would link to itself)
	 *
	 * @param string $key
	 *
	 * @return string A key for use in wikitext
	 */
	public function getReferencesKey( string $key ): string {
		$prefix = $this->messageLocalizer->msg( 'cite_references_link_prefix' )->plain();
		$suffix = $this->messageLocalizer->msg( 'cite_references_link_suffix' )->plain();

		return $this->normalizeAndEncode( $prefix . $key . $suffix );
	}

	private function normalizeAndEncode( string $key ): string {
		$key = $this->normalizeKey( $key );
		// FIXME: This does both URL encoding (%A0) as well as HTML entity encoding (&#xA0;), but
		// URL encoding only makes sense in links.
		return Sanitizer::safeEncodeAttribute( Sanitizer::escapeIdForLink( $key ) );
	}

	private function normalizeKey( string $key ): string {
		// MediaWiki normalizes spaces and underscores in [[#…]] links, but not in id="…"
		// attributes. To make them behave the same we normalize in advance.
		return preg_replace( '/[_\s]+/', '_', $key );
	}

}
