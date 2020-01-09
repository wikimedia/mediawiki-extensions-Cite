<?php

namespace Cite;

use Html;
use Language;
use Parser;
use Sanitizer;

/**
 * @license GPL-2.0-or-later
 */
class ErrorReporter {

	/**
	 * @var ReferenceMessageLocalizer
	 */
	private $messageLocalizer;

	/**
	 * @var Language
	 */
	private $cachedInterfaceLanguage = null;

	/**
	 * @param ReferenceMessageLocalizer $messageLocalizer
	 */
	public function __construct(
		ReferenceMessageLocalizer $messageLocalizer
	) {
		$this->messageLocalizer = $messageLocalizer;
	}

	/**
	 * @param Parser $parser
	 * @param string $key Message name of the error or warning
	 * @param mixed ...$params
	 *
	 * @return string Half-parsed wikitext with extension's tags already being expanded
	 */
	public function halfParsed( Parser $parser, string $key, ...$params ) : string {
		// FIXME: We suspect this is not necessary and can just be removed
		return $parser->recursiveTagParse( $this->plain( $parser, $key, ...$params ) );
	}

	/**
	 * @param Parser $parser
	 * @param string $key Message name of the error or warning
	 * @param mixed ...$params
	 *
	 * @return string Plain, unparsed wikitext
	 * @return-taint tainted
	 */
	public function plain( Parser $parser, string $key, ...$params ) : string {
		$interfaceLanguage = $this->getInterfaceLanguageAndSplitCache( $parser );
		$msg = $this->messageLocalizer->msg( $key, ...$params )->inLanguage( $interfaceLanguage );

		if ( strncmp( $msg->getKey(), 'cite_warning_', 13 ) === 0 ) {
			$type = 'warning';
			$id = substr( $msg->getKey(), 13 );
			$extraClass = ' mw-ext-cite-warning-' . Sanitizer::escapeClass( $id );
		} else {
			$type = 'error';
			$extraClass = '';

			// Take care; this is a sideeffect that might not belong to this class.
			$parser->addTrackingCategory( 'cite-tracking-category-cite-error' );
		}

		return Html::rawElement(
			'span',
			[
				'class' => "$type mw-ext-cite-$type" . $extraClass,
				'lang' => $interfaceLanguage->getHtmlCode(),
				'dir' => $interfaceLanguage->getDir(),
			],
			$this->messageLocalizer->msg( "cite_$type", $msg->plain() )
				->inLanguage( $interfaceLanguage )->plain()
		);
	}

	/**
	 * Note the startling side effect of splitting ParserCache by user interface language!
	 *
	 * @param Parser $parser
	 *
	 * @return Language
	 */
	private function getInterfaceLanguageAndSplitCache( Parser $parser ) : Language {
		if ( !$this->cachedInterfaceLanguage ) {
			$this->cachedInterfaceLanguage = $parser->getOptions()->getUserLangObj();
		}
		return $this->cachedInterfaceLanguage;
	}

}
