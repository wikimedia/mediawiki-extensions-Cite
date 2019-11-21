<?php

namespace Cite\Tests\Unit;

use Cite\Cite;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteTest extends \MediaWikiUnitTestCase {

	protected function setUp() : void {
		global $wgCiteBookReferencing, $wgFragmentMode;

		parent::setUp();
		$wgCiteBookReferencing = true;
		$wgFragmentMode = [ 'html5' ];
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
			[ [ 'dir' => 'invalid' ], [ null, null, false, false, null ] ],
			[ [ 'dir' => ' rtl ' ], [ null, null, false, 'rtl', null ] ],
			[ [ 'follow' => ' f ' ], [ null, null, 'f', null, null ] ],
			// FIXME: Unlike all other attributes, group isn't trimmed. Why?
			[ [ 'group' => ' g ' ], [ null, ' g ', null, null, null ] ],
			[ [ 'invalid' => 'i' ], [ false, false, false, null, false ] ],
			[ [ 'invalid' => null ], [ false, false, false, null, false ] ],
			[ [ 'name' => ' n ' ], [ 'n', null, null, null, null ] ],
			[ [ 'name' => null ], [ false, false, false, null, false ] ],
			[ [ 'extends' => ' e ' ], [ null, null, null, null, 'e' ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ false, false, false, null, false ] ],
			[ [ 'follow' => null, 'name' => null ], [ false, false, false, null, false ] ],
			[ [ 'follow' => 'f', 'extends' => 'e' ], [ false, false, false, null, false ] ],
			[ [ 'group' => 'g', 'name' => 'n' ], [ 'n', 'g', null, null, null ] ],

			// Combinations of 3 or more attributes
			[
				[ 'group' => 'g', 'name' => 'n', 'extends' => 'e', 'dir' => 'rtl' ],
				[ 'n', 'g', null, 'rtl', 'e' ]
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
