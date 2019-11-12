<?php

namespace Cite\Tests\Unit;

use Cite;
use CiteParserHooks;
use Parser;
use ParserOptions;
use StripState;

/**
 * @coversDefaultClass \CiteParserHooks
 */
class CiteParserHooksTest extends \MediaWikiUnitTestCase {

	/**
	 * @covers ::onParserFirstCallInit
	 */
	public function testOnParserFirstCallInit() {
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->exactly( 2 ) )
			->method( 'setHook' )
			->withConsecutive(
				[ 'ref', $this->isType( 'callable' ) ],
				[ 'references', $this->isType( 'callable' ) ]
			);

		CiteParserHooks::onParserFirstCallInit( $parser );

		$this->assertInstanceOf( Cite::class, $parser->extCite );
	}

	/**
	 * @covers ::onParserClearState
	 */
	public function testOnParserClearState() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'clearState' );

		$parser = $this->createMock( Parser::class );
		$parser->extCite = $cite;

		CiteParserHooks::onParserClearState( $parser );
	}

	/**
	 * @covers ::onParserCloned
	 */
	public function testOnParserCloned() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'clearState' );

		$parser = $this->createMock( Parser::class );
		$parser->extCite = $cite;

		CiteParserHooks::onParserCloned( $parser );

		$this->assertNotSame( $cite, $parser->extCite );
	}

	/**
	 * @covers ::onParserAfterParse
	 */
	public function testOnParserAfterParse() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'checkRefsNoReferences' );

		$parser = $this->createMock( Parser::class );
		$parser->method( 'getOptions' )
			->willReturn( $this->createMock( ParserOptions::class ) );
		$parser->extCite = $cite;

		CiteParserHooks::onParserAfterParse( $parser, $text, $this->createMock( StripState::class ) );
	}

	/**
	 * @covers ::onParserBeforeTidy
	 */
	public function testOnParserBeforeTidy() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'checkRefsNoReferences' );

		$parser = $this->createMock( Parser::class );
		$parser->method( 'getOptions' )
			->willReturn( $this->createMock( ParserOptions::class ) );
		$parser->extCite = $cite;

		CiteParserHooks::onParserBeforeTidy( $parser, $text );
	}

}
