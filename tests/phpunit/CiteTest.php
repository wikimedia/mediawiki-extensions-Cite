<?php

namespace Cite\Tests;

use Cite\Cite;
use Language;
use Parser;
use ParserOptions;
use ParserOutput;
use StripState;
use Wikimedia\TestingAccessWrapper;

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

	/**
	 * @covers ::listToText
	 * @dataProvider provideLists
	 */
	public function testListToText( array $list, $expected ) {
		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$this->assertSame( $expected, $cite->listToText( $list ) );
	}

	public function provideLists() {
		return [
			[
				[],
				''
			],
			[
				// This is intentionally using numbers to test the to-string cast
				[ 1 ],
				'1'
			],
			[
				[ 1, 2 ],
				'1(cite_references_link_many_and)2'
			],
			[
				[ 1, 2, 3 ],
				'1(cite_references_link_many_sep)2(cite_references_link_many_and)3'
			],
		];
	}

}
