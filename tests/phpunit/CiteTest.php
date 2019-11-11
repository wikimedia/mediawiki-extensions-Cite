<?php

use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite
 */
class CiteTest extends MediaWikiTestCase {

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
			[ [ 'refines' => 'r' ], [ null, null, null, null, 'r' ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ false, false, false, false, false ] ],
			[ [ 'follow' => null, 'name' => null ], [ false, false, false, false, false ] ],
			[ [ 'follow' => 'f', 'refines' => 'r' ], [ null, null, 'f', null, 'r' ] ],
			[ [ 'group' => 'g', 'name' => 'n' ], [ 'n', 'g', null, null, null ] ],

			// Combinations of 3 or more attributes
			[
				[ 'group' => 'g', 'name' => 'n', 'refines' => 'r', 'dir' => 'rtl' ],
				[ 'n', 'g', null, ' class="mw-cite-dir-rtl"', 'r' ]
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
	 * @covers ::ref
	 */
	public function testRef_pageProperty() {
		$mockOutput = $this->createMock( ParserOutput::class );
		$mockPPframe = $this->createMock( PPFrame::class );
		$mockParser = $this->createMock( Parser::class );
		$mockParser->method( 'getOutput' )
			->willReturn( $mockOutput );

		$mockOutput->expects( $this->once() )
			->method( 'setProperty' )
			->with( Cite::BOOK_REF_PROPERTY, true );

		$cite = new Cite();
		$cite->ref( 'contentA', [ 'name' => 'a' ], $mockParser, $mockPPframe );
		$cite->ref( 'contentB', [ Cite::REFINES_ATTRIBUTE => 'a' ], $mockParser, $mockPPframe );
	}

}
