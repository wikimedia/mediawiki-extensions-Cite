<?php

namespace Cite\Tests\Unit;

use Cite\CiteKeyFormatter;
use MediaWikiUnitTestCase;

/**
 * @coversDefaultClass \Cite\CiteKeyFormatter
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
		$keyFormatter = new CiteKeyFormatter();
		$this->assertSame( $expected, $keyFormatter->normalizeKey( $key ) );
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
