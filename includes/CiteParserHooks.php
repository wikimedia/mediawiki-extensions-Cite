<?php

/**
 * @license GPL-2.0-or-later
 */
class CiteParserHooks {

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 *
	 * @param Parser $parser
	 */
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->extCite = new Cite();
		CiteParserTagHooks::initialize( $parser );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserClearState
	 *
	 * @param Parser $parser
	 */
	public static function onParserClearState( Parser $parser ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		$cite->clearState();
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserCloned
	 *
	 * @param Parser $parser
	 */
	public static function onParserCloned( Parser $parser ) {
		$parser->extCite = clone $parser->extCite;

		/** @var Cite $cite */
		$cite = $parser->extCite;
		// Clear the state, making sure it will actually work.
		$cite->mInCite = false;
		$cite->mInReferences = false;
		$cite->clearState();

		CiteParserTagHooks::initialize( $parser );
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
		$cite->checkRefsNoReferences( true, $parser->getOptions(), $parser->getOutput(), $text );
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
		$cite->checkRefsNoReferences( false, $parser->getOptions(), $parser->getOutput(), $text );
	}

}
