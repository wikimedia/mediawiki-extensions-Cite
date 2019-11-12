<?php

namespace Cite\Tests;

use Cite;
use Parser;
use ParserOutput;
use StripState;

/**
 * @coversDefaultClass \Cite
 */
class CiteTest extends \MediaWikiIntegrationTestCase {

	protected function setUp() : void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgCiteBookReferencing' => true,
		] );
	}

	/**
	 * @covers ::guardedRef
	 */
	public function testBookExtendsPageProperty() {
		$mockOutput = $this->createMock( ParserOutput::class );
		$mockOutput->expects( $this->once() )
			->method( 'setProperty' )
			->with( Cite::BOOK_REF_PROPERTY, true );

		$mockParser = $this->createMock( Parser::class );
		$mockParser->method( 'getOutput' )
			->willReturn( $mockOutput );
		$mockParser->method( 'getStripState' )
			->willReturn( $this->createMock( StripState::class ) );

		$cite = new Cite();
		$cite->ref( 'contentA', [ 'name' => 'a' ], $mockParser );
		$cite->ref( 'contentB', [ Cite::BOOK_REF_ATTRIBUTE => 'a' ], $mockParser );
	}

}
