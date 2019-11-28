<?php

namespace Cite\Tests;

use Cite\Cite;
use Language;
use Parser;
use ParserOptions;
use ParserOutput;
use StripState;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteTest extends \MediaWikiIntegrationTestCase {

	protected function setUp() : void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgCiteBookReferencing' => true,
			'wgLanguageCode' => 'qqx',
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

		$parserOptions = $this->createMock( ParserOptions::class );
		$parserOptions->method( 'getUserLangObj' )
			->willReturn( $this->createMock( Language::class ) );

		$mockParser = $this->createMock( Parser::class );
		$mockParser->method( 'getOptions' )
			->willReturn( $parserOptions );
		$mockParser->method( 'getOutput' )
			->willReturn( $mockOutput );
		$mockParser->method( 'getStripState' )
			->willReturn( $this->createMock( StripState::class ) );

		$cite = new Cite();
		$cite->ref( 'contentA', [ 'name' => 'a' ], $mockParser );
		$cite->ref( 'contentB', [ Cite::BOOK_REF_ATTRIBUTE => 'a' ], $mockParser );
	}

}
