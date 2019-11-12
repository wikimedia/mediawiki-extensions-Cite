<?php

namespace Cite\Tests;

use Cite;
use Parser;
use ParserOutput;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite
 */
class CiteTest extends \MediaWikiIntegrationTestCase {

	protected function setUp() : void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgCiteBookReferencing' => true,
			'wgFragmentMode' => [ 'html5' ],
		] );
	}

	/**
	 * @covers ::refArg
	 * @dataProvider provideRefAttributes
	 */
	public function testRefArg( array $attributes, array $expected ) {
		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$this->assertSame( $expected, $cite->refArg( $attributes ) );
	}

	public function provideRefAttributes() {
		return [
			[ [], [ null, null, false, null, null ] ],

			// One attribute only
			[ [ 'dir' => 'invalid' ], [ null, null, false, '', null ] ],
			[ [ 'dir' => 'rtl' ], [ null, null, false, ' class="mw-cite-dir-rtl"', null ] ],
			[ [ 'follow' => 'f' ], [ null, null, 'f', null, null ] ],
			[ [ 'group' => 'g' ], [ null, 'g', null, null, null ] ],
			[ [ 'invalid' => 'i' ], [ false, false, false, false, false ] ],
			[ [ 'name' => 'n' ], [ 'n', null, null, null, null ] ],
			[ [ 'name' => null ], [ false, false, false, false, false ] ],
			[ [ 'extends' => 'e' ], [ null, null, null, null, 'e' ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ false, false, false, false, false ] ],
			[ [ 'follow' => null, 'name' => null ], [ false, false, false, false, false ] ],
			[ [ 'follow' => 'f', 'extends' => 'e' ], [ false, false, false, false, false ] ],
			[ [ 'group' => 'g', 'name' => 'n' ], [ 'n', 'g', null, null, null ] ],

			// Combinations of 3 or more attributes
			[
				[ 'group' => 'g', 'name' => 'n', 'extends' => 'e', 'dir' => 'rtl' ],
				[ 'n', 'g', null, ' class="mw-cite-dir-rtl"', 'e' ]
			],
		];
	}

	/**
	 * @covers ::normalizeKey
	 * @dataProvider provideKeyNormalizations
	 */
	public function testNormalizeKey( $key, $expected ) {
		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$this->assertSame( $expected, $cite->normalizeKey( $key ) );
	}

	public function provideKeyNormalizations() {
		return [
			[ 'a b', 'a_b' ],
			[ 'a  __  b', 'a_b' ],
			[ ':', ':' ],
			[ "\t\n", '&#9;&#10;' ],
			[ "'", '&#039;' ],
			[ "''", '&#039;&#039;' ],
			[ '"%&/<>?[]{|}', '&quot;%&amp;/&lt;&gt;?&#91;&#93;&#123;&#124;&#125;' ],
			[ 'ISBN', '&#73;SBN' ],
		];
	}

	/**
	 * @covers ::guardedRef
	 */
	public function testRef_pageProperty() {
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
