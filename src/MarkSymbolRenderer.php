<?php

namespace Cite;

use MediaWiki\Config\Config;

/**
 * Render the label for footnote marks, for example "1", "2", …
 *
 * Marks can be customized by group.
 *
 * @license GPL-2.0-or-later
 */
class MarkSymbolRenderer {

	/** @var array<string,string[]> In-memory cache for the cite_link_label_group-… link label lists */
	private array $legacyLinkLabels = [];

	private ReferenceMessageLocalizer $messageLocalizer;
	private AlphabetsProvider $alphabetsProvider;
	private Config $config;

	public function __construct(
		ReferenceMessageLocalizer $messageLocalizer,
		AlphabetsProvider $alphabetsProvider,
		Config $config
	) {
		// TODO: deprecate the i18n mechanism.
		$this->messageLocalizer = $messageLocalizer;
		$this->alphabetsProvider = $alphabetsProvider;
		$this->config = $config;
	}

	public function makeLabel( string $group, int $number, ?int $extendsIndex = null ): string {
		$label = $this->fetchLegacyCustomLinkLabel( $group, $number ) ??
		$this->makeDefaultLabel( $group, $number );
		if ( $extendsIndex !== null ) {
			// TODO: design better behavior, especially when using custom group markers.
			$label .= '.' . $this->messageLocalizer->localizeDigits( (string)$extendsIndex );
		}
		return $label;
	}

	private function makeDefaultLabel( string $group, int $number ): string {
		$label = $this->messageLocalizer->localizeDigits( (string)$number );
		return $group === Cite::DEFAULT_GROUP ? $label : "$group $label";
	}

	/**
	 * Look up the symbol in a literal sequence stored in a local system message override.
	 *
	 * @deprecated since 1.44
	 */
	private function fetchLegacyCustomLinkLabel( string $group, int $number ): ?string {
		if ( $group === Cite::DEFAULT_GROUP ) {
			// TODO: Possibly make the default group configurable, eg. to use a
			// different numeral system than the content language or Western
			// Arabic.
			return null;
		}

		// TODO: deprecate this mechanism.
		$message = "cite_link_label_group-$group";
		if ( !array_key_exists( $group, $this->legacyLinkLabels ) ) {
			$msg = $this->messageLocalizer->msg( $message );
			$this->legacyLinkLabels[$group] = $msg->isDisabled() ? [] : preg_split( '/\s+/', $msg->plain() );
		}

		// Expected behavior for groups without custom labels
		if ( !$this->legacyLinkLabels[$group] ) {
			return null;
		}

		return $this->legacyLinkLabels[$group][$number - 1] ?? null;
	}

	/**
	 * Returns an Alphabet of symbols that can be used to generate backlink markers.
	 * The Alphabet will either be retrieved from config or the CLDR AlphabetsProvider.
	 *
	 * @param string $code
	 * @return string[]
	 */
	public function getBacklinkAlphabet( string $code ): array {
		$alphabet = preg_split(
			'/\s+/',
			$this->config->get( 'CiteDefaultBacklinkAlphabet' ) ?? '',
			-1,
			PREG_SPLIT_NO_EMPTY
		);

		if ( !$alphabet ) {
			$alphabet = $this->alphabetsProvider->getIndexCharacters( $code ) ?? [];
			$alphabet = array_map( 'mb_strtolower', $alphabet );
		}

		if ( !$alphabet ) {
			$alphabet = range( 'a', 'z' );
		}

		return $alphabet;
	}

	/**
	 * Returns the backlink label for the n-th re-use of a reference
	 */
	public function makeBacklinkLabel( array $symbols, int $number ): string {
		if ( count( $symbols ) < 1 ) {
			return '';
		}

		return $this->buildAlphaLabel( $symbols, $number );
	}

	/**
	 * Recursive method to build a mark using an alphabet, repeating symbols to
	 * extend the range like "a…z, aa, ab…"
	 *
	 * @param array $symbols List of alphabet characters as strings
	 * @param int $number One-based footnote group index
	 * @param string $result Recursively-constructed output
	 * @return string Caller sees the final result
	 */
	private function buildAlphaLabel( array $symbols, int $number, string $result = '' ): string {
		// Done recursing?
		if ( $number <= 0 ) {
			return $result;
		}
		$radix = count( $symbols );
		// Use a zero-based index as it becomes more convenient for integer
		// modulo and symbol lookup (though not for division!)
		$remainder = ( $number - 1 ) % $radix;
		return $this->buildAlphaLabel(
			$symbols,
			// Subtract so the units value is zero and divide, moving to the next place leftwards.
			( $number - ( $remainder + 1 ) ) / $radix,
			// Prepend current symbol, moving left.
			$symbols[ $remainder ] . $result
		);
	}
}
