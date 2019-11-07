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
			[ [], [ null, null, false, null ] ],

			// One attribute only
			[ [ 'dir' => 'invalid' ], [ null, null, false, '' ] ],
			[ [ 'dir' => 'rtl' ], [ null, null, false, ' class="mw-cite-dir-rtl"' ] ],
			[ [ 'follow' => 'f' ], [ null, null, 'f', null ] ],
			[ [ 'group' => 'g' ], [ null, 'g', null, null ] ],
			[ [ 'invalid' => 'i' ], [ false, false, false, false ] ],
			[ [ 'name' => 'n' ], [ 'n', null, null, null ] ],
			[ [ 'name' => null ], [ false, false, false, false ] ],
			[ [ 'refines' => 'r' ], [ null, null, null, null ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ false, false, false, false ] ],
			[ [ 'follow' => null, 'name' => null ], [ false, false, false, false ] ],
			[ [ 'follow' => 'f', 'refines' => 'r' ], [ null, null, 'f', null ] ],
			[ [ 'group' => 'r', 'name' => 'n' ], [ 'n', 'r', null, null ] ],

			// Combinations of 3 or more attributes
			[
				[ 'group' => 'r', 'name' => 'n', 'refines' => 'f', 'dir' => 'rtl' ],
				[ 'n', 'r', null, ' class="mw-cite-dir-rtl"' ]
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

}
