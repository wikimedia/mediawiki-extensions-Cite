<?php

namespace Cite\Tests\Unit;

use Cite;
use CiteParserTagHooks;
use Parser;
use ParserOutput;
use PPFrame;

/**
 * @coversDefaultClass \CiteParserTagHooks
 */
class CiteParserTagHooksTest extends \MediaWikiUnitTestCase {

	/**
	 * @covers ::initialize
	 */
	public function testInitialize() {
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->exactly( 2 ) )
			->method( 'setHook' )
			->withConsecutive(
				[ 'ref', $this->isType( 'callable' ) ],
				[ 'references', $this->isType( 'callable' ) ]
			);

		CiteParserTagHooks::initialize( $parser );
	}

	/**
	 * @covers ::ref
	 */
	public function testRef() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'ref' );

		$parserOutput = $this->createMock( ParserOutput::class );
		$parserOutput->expects( $this->once() )
			->method( 'addModules' );
		$parserOutput->expects( $this->once() )
			->method( 'addModuleStyles' );

		$parser = $this->createMock( Parser::class );
		$parser->method( 'getOutput' )
			->willReturn( $parserOutput );
		$parser->extCite = $cite;

		CiteParserTagHooks::ref( null, [], $parser, $this->createMock( PPFrame::class ) );
	}

	/**
	 * @covers ::references
	 */
	public function testReferences() {
		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'references' );

		$parser = $this->createMock( Parser::class );
		$parser->extCite = $cite;

		CiteParserTagHooks::references( null, [], $parser, $this->createMock( PPFrame::class ) );
	}

}
