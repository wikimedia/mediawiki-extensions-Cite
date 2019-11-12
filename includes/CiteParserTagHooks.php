<?php

class CiteParserTagHooks {

	/**
	 * Enables the two <ref> and <references> tags.
	 *
	 * @param Parser $parser
	 */
	public static function initialize( Parser $parser ) {
		$parser->setHook( 'ref', __CLASS__ . '::ref' );
		$parser->setHook( 'references', __CLASS__ . '::references' );
	}

	/**
	 * Parser hook for the <ref> tag.
	 *
	 * @param string|null $content Raw wikitext content of the <ref> tag.
	 * @param string[] $attributes
	 * @param Parser $parser
	 * @param PPFrame $frame
	 *
	 * @return string HTML
	 */
	public static function ref( $content, array $attributes, Parser $parser, PPFrame $frame ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		// @phan-suppress-next-line SecurityCheck-XSS False positive
		$result = $cite->ref( $content, $attributes, $parser );

		if ( $result === false ) {
			return htmlspecialchars( "<ref>$content</ref>" );
		}

		$parserOutput = $parser->getOutput();
		$parserOutput->addModules( 'ext.cite.ux-enhancements' );
		$parserOutput->addModuleStyles( 'ext.cite.styles' );

		$frame->setVolatile();
		return $result;
	}

	/**
	 * Parser hook for the <references> tag.
	 *
	 * @param string|null $content Raw wikitext content of the <references> tag.
	 * @param string[] $attributes
	 * @param Parser $parser
	 * @param PPFrame $frame
	 *
	 * @return string HTML
	 */
	public static function references( $content, array $attributes, Parser $parser, PPFrame $frame ) {
		/** @var Cite $cite */
		$cite = $parser->extCite;
		// @phan-suppress-next-line SecurityCheck-XSS False positive
		$result = $cite->references( $content, $attributes, $parser );

		if ( $result === false ) {
			return htmlspecialchars( $content === null
				? "<references/>"
				: "<references>$content</references>"
			);
		}

		$frame->setVolatile();
		return $result;
	}

}
