<?php

namespace Cite\Tests;

use Cite\CiteErrorReporter;
use Cite\CiteKeyFormatter;
use Cite\FootnoteBodyFormatter;
use MediaWikiIntegrationTestCase;
use Parser;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\FootnoteBodyFormatter
 */
class FootnoteBodyFormatterTest extends MediaWikiIntegrationTestCase {

	public function setUp() : void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgLanguageCode' => 'qqx',
		] );
	}

	/**
	 * @covers ::listToText
	 * @dataProvider provideLists
	 */
	public function testListToText( array $list, $expected ) {
		/** @var Parser $mockErrorReporter */
		$mockParser = $this->createMock( Parser::class );
		/** @var CiteErrorReporter $mockErrorReporter */
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		/** @var CiteKeyFormatter $mockKeyFormatter */
		$mockKeyFormatter = $this->createMock( CiteKeyFormatter::class );
		/** @var FootnoteBodyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$mockParser, $mockErrorReporter, $mockKeyFormatter ) );
		$this->assertSame( $expected, $formatter->listToText( $list ) );
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
