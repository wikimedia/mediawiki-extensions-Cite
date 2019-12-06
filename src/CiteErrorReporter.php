<?php

namespace Cite;

use Html;
use Language;
use Parser;
use Sanitizer;

/**
 * @license GPL-2.0-or-later
 */
class CiteErrorReporter {

	/**
	 * @var Parser
	 */
	private $parser;

	/**
	 * @var ReferenceMessageLocalizer
	 */
	private $messageLocalizer;

	/**
	 * @var Language
	 */
	private $cachedInterfaceLanguage = null;

	/**
	 * @param Parser $parser
	 * @param ReferenceMessageLocalizer $messageLocalizer
	 */
	public function __construct(
		Parser $parser,
		ReferenceMessageLocalizer $messageLocalizer
	) {
		$this->parser = $parser;
		$this->messageLocalizer = $messageLocalizer;
	}

	/**
	 * @param string $key Message name of the error or warning
	 * @param mixed ...$params
	 *
	 * @return string Half-parsed wikitext with extension's tags already being expanded
	 */
	public function halfParsed( string $key, ...$params ) : string {
		// FIXME: We suspect this is not necessary and can just be removed
		return $this->parser->recursiveTagParse( $this->plain( $key, ...$params ) );
	}

	/**
	 * @param string $key Message name of the error or warning
	 * @param mixed ...$params
	 *
	 * @return string Plain, unparsed wikitext
	 * @return-taint tainted
	 */
	public function plain( string $key, ...$params ) : string {
		$msg = $this->messageLocalizer->msg( $key, ...$params );

		if ( strncmp( $msg->getKey(), 'cite_warning_', 13 ) === 0 ) {
			$type = 'warning';
			$id = substr( $msg->getKey(), 13 );
			$extraClass = ' mw-ext-cite-warning-' . Sanitizer::escapeClass( $id );
		} else {
			$type = 'error';
			$extraClass = '';

			// Take care; this is a sideeffect that might not belong to this class.
			$this->parser->addTrackingCategory( 'cite-tracking-category-cite-error' );
		}

		$interfaceLanguage = $this->getInterfaceLanguageAndSplitCache();

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
	 * @return Language
	 */
	private function getInterfaceLanguageAndSplitCache(): Language {
		if ( $this->cachedInterfaceLanguage === null ) {
			$this->cachedInterfaceLanguage = $this->parser->getOptions()->getUserLangObj();
		}
		return $this->cachedInterfaceLanguage;
	}

}
