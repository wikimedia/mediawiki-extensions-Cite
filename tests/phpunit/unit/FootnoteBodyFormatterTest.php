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
	 * @covers ::__construct
	 * @covers ::referencesFormat
	 * @dataProvider provideReferencesFormat
	 */
	public function testReferencesFormat( array $refs, string $expectedOutput ) {
		$mockParser = $this->createMock( Parser::class );
		$mockParser->method( 'recursiveTagParse' )->willReturnArgument( 0 );
		/** @var Parser $mockParser */
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'plain' )->willReturnCallback(
			function ( ...$args ) {
				return json_encode( $args );
			}
		);
		/** @var CiteErrorReporter $mockErrorReporter */
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( ...$args ) {
				$mockMessage = $this->createMock( Message::class );
				$mockMessage->method( 'plain' )->willReturn(
					'<li>(' . implode( '|', $args ) . ')</li>' );
				return $mockMessage;
			}
		);
		/** @var ReferenceMessageLocalizer $mockMessageLocalizer */

		/** @var FootnoteBodyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$mockParser,
			$mockErrorReporter,
			$this->createMock( CiteKeyFormatter::class ),
			$mockMessageLocalizer ) );

		$output = $formatter->referencesFormat( $refs, true, false );
		$this->assertSame( $expectedOutput, $output );
	}

	public function provideReferencesFormat() {
		return [
			'Empty' => [
				[],
				''
			],
			'Minimal ref' => [
				[
					0 => [
						'key' => 1,
						'text' => 't',
					]
				],
				'<div class="mw-references-wrap"><ol class="references">' . "\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n</ol></div>"
			],
			'Ref with extends' => [
				[
					0 => [
						'extends' => 'a',
						'extendsIndex' => 1,
						'key' => 2,
						'number' => 10,
						'text' => 't2',
					],
					1 => [
						'number' => 11,
						'text' => 't3',
					],
					'a' => [
						'key' => 1,
						'number' => 9,
						'text' => 't1',
					],
				],
				'<div class="mw-references-wrap"><ol class="references">' . "\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t1</span>' . "\n" .
					'|)<ol class="mw-extended-references"><li>(cite_references_link_many|||' .
					'<span class="reference-text">t2</span>' . "\n|)</li>\n" .
					"</ol></li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t3</span>' .
					"\n|)</li>\n" .
					'</ol></div>'
			],
			'Subref of subref' => [
				[
					0 => [
						'extends' => 'a',
						'extendsIndex' => 1,
						'key' => 1,
						'number' => 1,
						'text' => 't1',
					],
					'a' => [
						'extends' => 'b',
						'extendsIndex' => 1,
						'key' => 2,
						'number' => 1,
						'text' => 't2',
					],
					'b' => [
						'key' => 3,
						'number' => 1,
						'text' => 't3',
					],
				],
				'<div class="mw-references-wrap"><ol class="references">' . "\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t3</span>' . "\n" .
					"|)</li>\n<li>(cite_references_link_many|||" .
					'<span class="reference-text">t1["cite_error_ref_too_many_keys"]</span>' .
					"\n" . '|)<ol class="mw-extended-references">' .
					'<li>(cite_references_link_many|||<span class="reference-text">t2</span>' .
					"\n|)</li>\n</ol></li>\n" .
					'</ol></div>'
			],
			'Use columns' => [
				array_map(
					function ( $i ) {
						return [ 'key' => $i, 'text' => 't' ];
					},
					range( 0, 10 )
				),
				'<div class="mw-references-wrap mw-references-columns"><ol class="references">' .
					"\n" . '<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n" .
					'<li>(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n|)</li>\n</ol></div>"
			],
		];
	}

	/**
	 * @covers ::closeIndention
	 * @dataProvider provideCloseIndention
	 */
	public function testCloseIndention( $closingLi, $expectedOutput ) {
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$this->createMock( Parser::class ),
			$this->createMock( CiteErrorReporter::class ),
			$this->createMock( CiteKeyFormatter::class ),
			$this->createMock( ReferenceMessageLocalizer::class ) ) );

		$output = $formatter->closeIndention( $closingLi );
		$this->assertSame( $expectedOutput, $output );
	}

	public function provideCloseIndention() {
		return [
			'No indention' => [ false, '' ],
			'Indention string' => [ "</li>\n", "</ol></li>\n" ],
			'Indention without string' => [ true, '</ol>' ],
		];
	}

	/**
	 * @covers ::referencesFormatEntry
	 * @dataProvider provideReferencesFormatEntry
	 */
	public function testReferencesFormatEntry(
		$key,
		array $val,
		string $expectedOutput
	) {
		/** @var CiteErrorReporter $mockErrorReporter */
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'plain' )->willReturnCallback(
			function ( ...$args ) {
				return '(' . implode( '|', $args ) . ')';
			}
		);
		$mockCiteKeyFormatter = $this->createMock( CiteKeyFormatter::class );
		$mockCiteKeyFormatter->method( 'refKey' )->willReturnCallback(
			function ( $key, $num = null ) {
				return $key . '+' . ( $num ?? 'null' );
			}
		);
		/** @var ReferenceMessageLocalizer $mockMessageLocalizer */
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'getLanguage' )->willReturnCallback(
			function () {
				$mockLanguage = $this->createMock( Language::class );
				$mockLanguage->method( 'formatNum' )->willReturnArgument( 0 );
				return $mockLanguage;
			}
		);
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( ...$args ) {
				$mockMessage = $this->createMock( Message::class );
				$mockMessage->method( 'plain' )->willReturn(
					'(' . implode( '|', $args ) . ')' );
				return $mockMessage;
			}
		);
		/** @var FootnoteBodyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$this->createMock( Parser::class ),
			$mockErrorReporter,
			$mockCiteKeyFormatter,
			$mockMessageLocalizer ) );

		$output = $formatter->referencesFormatEntry( $key, $val, false );
		$this->assertSame( $expectedOutput, $output );
	}

	public function provideReferencesFormatEntry() {
		return [
			'Success' => [
				1,
				[
					'text' => 't',
				],
				'(cite_references_link_many|||<span class="reference-text">t</span>' . "\n" . '|)'
			],
			'Good dir' => [
				1,
				[
					'dir' => 'rtl',
					'text' => 't',
				],
				'(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n" . '| class="mw-cite-dir-rtl")'
			],
			'Invalid dir' => [
				1,
				[
					'dir' => 'not',
					'text' => 't',
				],
				'(cite_references_link_many|||<span class="reference-text">t</span>' .
					"\n" . '(cite_error_ref_invalid_dir|not)' . "\n|)"
			],
			'Broken follow' => [
				1,
				[
					'follow' => 'f',
					'text' => 't',
				],
				'(cite_references_no_link||<span class="reference-text">t</span>' . "\n)"
			],
			'Count zero' => [
				1,
				[
					'count' => 0,
					'key' => 5,
					'text' => 't',
				],
				'(cite_references_link_one||1+5-0|<span class="reference-text">t</span>' . "\n|)"
			],
			'Count negative' => [
				1,
				[
					'count' => -1,
					'key' => 5,
					'number' => 3,
					'text' => 't',
				],
				'(cite_references_link_one||5+null|<span class="reference-text">t</span>' . "\n|)"
			],
			'Count positive' => [
				1,
				[
					'count' => 2,
					'key' => 5,
					'number' => 3,
					'text' => 't',
				],
				'(cite_references_link_many||(cite_references_link_many_format|1+5-0|3.0|' .
				'(cite_references_link_many_format_backlink_labels))' .
				'(cite_references_link_many_sep)(cite_references_link_many_format|1+5-1|3.1|' .
				'(cite_error_references_no_backlink_label))(cite_references_link_many_and)' .
				'(cite_references_link_many_format|1+5-2|3.2|(cite_error_references_no_backlink_label' .
				'))|<span class="reference-text">t</span>' . "\n|)"
			],
		];
	}

	/**
	 * @covers ::referenceText
	 * @dataProvider provideReferenceText
	 */
	public function testReferenceText(
		$key,
		?string $text,
		bool $isSectionPreview,
		string $expectedOutput
	) {
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'plain' )->willReturnCallback(
			function ( ...$args ) {
				return json_encode( $args );
			}
		);
		/** @var FootnoteBodyFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteBodyFormatter(
			$this->createMock( Parser::class ),
			$mockErrorReporter,
			$this->createMock( CiteKeyFormatter::class ),
			$this->createMock( ReferenceMessageLocalizer::class ) ) );

		$output = $formatter->referenceText( $key, $text, $isSectionPreview );
		$this->assertSame( $expectedOutput, $output );
	}

	public function provideReferenceText() {
		return [
			'No text, not preview' => [
				1,
				null,
				false,
				'["cite_error_references_no_text",1]'
			],
			'No text, is preview' => [
				1,
				null,
				true,
				'["cite_warning_sectionpreview_no_text",1]'
			],
			'Has text' => [
				1,
				'text',
				true,
				'<span class="reference-text">text</span>' . "\n"
			],
			'Trims text' => [
				1,
				"text\n\n",
				true,
				'<span class="reference-text">text</span>' . "\n"
			],
		];
	}

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
