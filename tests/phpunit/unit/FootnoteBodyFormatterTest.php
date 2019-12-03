<?php

namespace Cite\Tests\Unit;

use Cite\CiteErrorReporter;
use Cite\CiteKeyFormatter;
use Cite\FootnoteBodyFormatter;
use Cite\ReferenceMessageLocalizer;
use Language;
use MediaWikiUnitTestCase;
use Message;
use Parser;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\FootnoteBodyFormatter
 *
 * @license GPL-2.0-or-later
 */
class FootnoteBodyFormatterTest extends MediaWikiUnitTestCase {
	/**
	 * @covers ::referencesFormatEntryAlternateBacklinkLabel
	 * @dataProvider provideReferencesFormatEntryAlternateBacklinkLabel
	 */
	public function testReferencesFormatEntryAlternateBacklinkLabel(
		?string $expectedLabel, ?string $labelList, int $offset
	) {
		$mockMessage = $this->createMock( Message::class );
		$mockMessage->method( 'exists' )->willReturn( (bool)$labelList );
		$mockMessage->method( 'plain' )->willReturn( $labelList ?? '<missing-junk>' );
		/** @var ReferenceMessageLocalizer $mockMessageLocalizer */
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )
			->willReturn( $mockMessage );
		/** @var CiteErrorReporter $mockErrorReporter */
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		if ( $expectedLabel === null ) {
			$mockErrorReporter->expects( $this->once() )->method( 'plain' );
		} else {
			$mockErrorReporter->expects( $this->never() )->method( 'plain' );
		}
		/** @var FootnoteBodyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$this->createMock( Parser::class ),
			$mockErrorReporter,
			$this->createMock( CiteKeyFormatter::class ),
			$mockMessageLocalizer ) );

		$label = $formatter->referencesFormatEntryAlternateBacklinkLabel( $offset );
		if ( $expectedLabel !== null ) {
			$this->assertSame( $expectedLabel, $label );
		}
	}

	public function provideReferencesFormatEntryAlternateBacklinkLabel() {
		yield [ 'aa', 'aa ab ac', 0 ];
		yield [ 'ab', 'aa ab ac', 1 ];
		yield [ 'å', 'å b c', 0 ];
		yield [ null, 'a b c', 10 ];
	}

	/**
	 * @covers ::referencesFormatEntryNumericBacklinkLabel
	 * @dataProvider provideReferencesFormatEntryNumericBacklinkLabel
	 */
	public function testReferencesFormatEntryNumericBacklinkLabel(
		string $expectedLabel, int $base, int $offset, int $max
	) {
		$mockLanguage = $this->createMock( Language::class );
		$mockLanguage->method( 'formatNum' )->with( $expectedLabel )
			->willReturnArgument( 0 );
		/** @var ReferenceMessageLocalizer $mockMessageLocalizer */
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'getLanguage' )->willReturn( $mockLanguage );
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$this->createMock( Parser::class ),
			$this->createMock( CiteErrorReporter::class ),
			$this->createMock( CiteKeyFormatter::class ),
			$mockMessageLocalizer ) );

		$label = $formatter->referencesFormatEntryNumericBacklinkLabel( $base, $offset, $max );
		$this->assertSame( $expectedLabel, $label );
	}

	public function provideReferencesFormatEntryNumericBacklinkLabel() {
		yield [ '1.2', 1, 2, 9 ];
		yield [ '1.02', 1, 2, 99 ];
		yield [ '1.002', 1, 2, 100 ];
		yield [ '2.1', 2, 1, 1 ];
	}

	/**
	 * @covers ::listToText
	 * @dataProvider provideLists
	 */
	public function testListToText( array $list, $expected ) {
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( $msg ) {
				$mockMessage = $this->createMock( Message::class );
				$mockMessage->method( 'plain' )->willReturn( "({$msg})" );
				return $mockMessage;
			}
		);
		/** @var FootnoteBodyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$this->createMock( Parser::class ),
			$this->createMock( CiteErrorReporter::class ),
			$this->createMock( CiteKeyFormatter::class ),
			$mockMessageLocalizer ) );
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
