<?php

class CiteParserHooks {

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserClearState
	 *
	 * @param Parser $parser
	 */
	public static function onParserClearState( Parser $parser ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		$cite->clearState( $parser );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserCloned
	 *
	 * @param Parser $parser
	 */
	public static function onParserCloned( Parser $parser ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		$cite->cloneState( $parser );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserAfterParse
	 *
	 * @param Parser $parser
	 * @param string &$text
	 * @param StripState $stripState
	 */
	public static function onParserAfterParse( Parser $parser, &$text, $stripState ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		$cite->checkRefsNoReferences( true, $parser, $text );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserBeforeTidy
	 *
	 * @param Parser $parser
	 * @param string &$text
	 */
	public static function onParserBeforeTidy( Parser $parser, &$text ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		$cite->checkRefsNoReferences( false, $parser, $text );
	}

}
