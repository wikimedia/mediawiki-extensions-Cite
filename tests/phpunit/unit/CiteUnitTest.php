<?php

namespace Cite\Tests\Unit;

use Cite\Cite;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteUnitTest extends \MediaWikiUnitTestCase {

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
			[ [], [ null, null, null, null, null ] ],

			// One attribute only
			[ [ 'dir' => 'invalid' ], [ 'invalid', null, null, null, null ] ],
			[ [ 'dir' => ' rtl ' ], [ 'rtl', null, null, null, null ] ],
			[ [ 'follow' => ' f ' ], [ null, null, 'f', null, null ] ],
			// FIXME: Unlike all other attributes, group isn't trimmed. Why?
			[ [ 'group' => ' g ' ], [ null, null, null, ' g ', null ] ],
			[ [ 'invalid' => 'i' ], [ false, false, false, false, false ] ],
			[ [ 'invalid' => null ], [ false, false, false, false, false ] ],
			[ [ 'name' => ' n ' ], [ null, null, null, null, 'n' ] ],
			[ [ 'name' => null ], [ false, false, false, false, false ] ],
			[ [ 'extends' => ' e ' ], [ null, 'e', null, null, null ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ null, null, 'f', null, 'n' ] ],
			[ [ 'follow' => null, 'name' => null ], [ false, false, false, false, false ] ],
			[ [ 'follow' => 'f', 'extends' => 'e' ], [ null, 'e', 'f', null, null ] ],
			[ [ 'group' => 'g', 'name' => 'n' ], [ null, null, null, 'g', 'n' ] ],

			// Combinations of 3 or more attributes
			[
				[ 'group' => 'g', 'name' => 'n', 'extends' => 'e', 'dir' => 'rtl' ],
				[ 'rtl', 'e', null, 'g', 'n' ]
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
