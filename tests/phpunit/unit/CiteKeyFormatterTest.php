<?php

namespace Cite\Tests\Unit;

use Cite\CiteKeyFormatter;
use MediaWikiUnitTestCase;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\CiteKeyFormatter
 *
 * @license GPL-2.0-or-later
 */
class CiteKeyFormatterTest extends MediaWikiUnitTestCase {

	public function setUp() : void {
		parent::setUp();

		global $wgFragmentMode;
		$wgFragmentMode = [ 'html5' ];
	}

	/**
	 * @covers ::normalizeKey
	 * @dataProvider provideKeyNormalizations
	 */
	public function testNormalizeKey( $key, $expected ) {
		/** @var CiteKeyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new CiteKeyFormatter() );
		$this->assertSame( $expected, $formatter->normalizeKey( $key ) );
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
