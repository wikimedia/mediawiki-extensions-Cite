<?php

namespace Cite\Tests\Unit;

use Cite;
use CiteParserTagHooks;
use Parser;
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
		$parser = $this->createMock( Parser::class );
		$frame = $this->createMock( PPFrame::class );

		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'ref' )
			->with( null, [], $parser, $frame );

		$parser->extCite = $cite;

		CiteParserTagHooks::ref( null, [], $parser, $frame );
	}

	/**
	 * @covers ::references
	 */
	public function testReferences() {
		$parser = $this->createMock( Parser::class );
		$frame = $this->createMock( PPFrame::class );

		$cite = $this->createMock( Cite::class );
		$cite->expects( $this->once() )
			->method( 'references' )
			->with( null, [], $parser, $frame );

		$parser->extCite = $cite;

		CiteParserTagHooks::references( null, [], $parser, $frame );
	}

}
