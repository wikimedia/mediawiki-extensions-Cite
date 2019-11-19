<?php

namespace Cite\Tests\Unit;

use Cite\Cite;
use Cite\Hooks\CiteParserHooks;
use Parser;
use ParserOptions;
use ParserOutput;
use StripState;

/**
 * @coversDefaultClass \Cite\Hooks\CiteParserHooks
 *
 * @license GPL-2.0-or-later
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
	 * @covers ::onParserBeforeTidy
	 */
	public function testAfterParseHooks() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->exactly( 2 ) )
			->method( 'checkRefsNoReferences' );

		$parser = $this->createMock( Parser::class );
		$parser->method( 'getOptions' )
			->willReturn( $this->createMock( ParserOptions::class ) );
		$parser->method( 'getOutput' )
			->willReturn( $this->createMock( ParserOutput::class ) );
		$parser->extCite = $cite;

		CiteParserHooks::onParserAfterParse( $parser, $text, $this->createMock( StripState::class ) );
		CiteParserHooks::onParserBeforeTidy( $parser, $text );
	}

}
