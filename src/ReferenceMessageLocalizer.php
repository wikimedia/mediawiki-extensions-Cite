<?php

namespace Cite;

use MediaWiki\Language\Language;
use MediaWiki\Message\Message;
use MessageLocalizer;
use Wikimedia\Message\MessageSpecifier;

/**
 * Interface abstracts everything a Cite needs to do with languages.
 *
 * @license GPL-2.0-or-later
 */
class ReferenceMessageLocalizer implements MessageLocalizer {

	public function __construct(
		private readonly Language $language,
	) {
	}

	public function localizeSeparators( string $number ): string {
		// Filter to make sure characters are never removed
		return strtr( $number, array_filter( $this->language->separatorTransformTable() ?: [] ) );
	}

	/**
	 * Transliterate numerals, without adding or changing separators.
	 */
	public function localizeDigits( string $number ): string {
		return $this->language->formatNumNoSeparators( $number );
	}

	/**
	 * This is the method for getting translated interface messages.
	 *
	 * Note that it returns messages coerced to a specific language, the content language
	 * rather than the UI language.
	 *
	 * @see https://www.mediawiki.org/wiki/Manual:Messages_API
	 * @see Message::__construct
	 *
	 * @param string|string[]|MessageSpecifier $key Message key, or array of keys,
	 *   or a MessageSpecifier.
	 * @param mixed ...$params Normal message parameters
	 *
	 * @return Message
	 */
	public function msg( $key, ...$params ): Message {
		return wfMessage( $key, ...$params )->inLanguage( $this->language );
	}

}
