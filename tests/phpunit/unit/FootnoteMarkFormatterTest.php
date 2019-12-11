<?php

namespace Cite;

use MediaWikiUnitTestCase;
use Message;
use Parser;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\FootnoteMarkFormatter
 */
class FootnoteMarkFormatterTest extends MediaWikiUnitTestCase {
	/**
	 * @covers ::linkRef
	 * @covers ::__construct
	 * @dataProvider provideLinkRef
	 */
	public function testLinkRef( string $group, array $ref, string $expectedOutput ) {
		$fooLabels = 'a b c';

		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'plain' )->willReturnCallback(
			function ( ...$args ) {
				return implode( '|', $args );
			}
		);
		$mockKeyFormatter = $this->createMock( CiteKeyFormatter::class );
		$mockKeyFormatter->method( 'getReferencesKey' )->willReturnArgument( 0 );
		$mockKeyFormatter->method( 'refKey' )->willReturnCallback(
			function ( ...$args ) {
				return implode( '+', $args );
			}
		);
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'formatNum' )->willReturnArgument( 0 );
		$mockMessageLocalizer->method( 'localizeDigits' )->willReturnArgument( 0 );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( ...$args ) use ( $group, $fooLabels ) {
				$mockMessage = $this->createMock( Message::class );
				$mockMessage->method( 'isDisabled' )->willReturn( $group !== 'foo' );
				if ( $args[0] === 'cite_reference_link' ) {
					$mockMessage->method( 'plain' )->willReturnCallback(
						function () use ( $args ) {
							return '(' . implode( '|', $args ) . ')';
						}
					);
				} else {
					$mockMessage->method( 'plain' )->willReturn( $fooLabels );
				}
				return $mockMessage;
			}
		);
		$mockParser = $this->createMock( Parser::class );
		$mockParser->method( 'recursiveTagParse' )->willReturnArgument( 0 );
		/** @var FootnoteMarkFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteMarkFormatter(
			$mockParser,
			$mockErrorReporter,
			$mockKeyFormatter,
			$mockMessageLocalizer ) );

		$output = $formatter->linkRef( $group, $ref );
		$this->assertSame( $expectedOutput, $output );
	}

	public function provideLinkRef() {
		return [
			'Default label' => [
				'',
				[
					'name' => null,
					'number' => 3,
					'key' => 4,
					'count' => -1,
				],
				'(cite_reference_link|4+|4|3)'
			],
			'Default label, named group' => [
				'bar',
				[
					'name' => null,
					'number' => 3,
					'key' => 4,
					'count' => -1,
				],
				'(cite_reference_link|4+|4|bar 3)'
			],
			'Custom label' => [
				'foo',
				[
					'name' => null,
					'number' => 3,
					'key' => 4,
					'count' => -1,
				],
				'(cite_reference_link|4+|4|c)'
			],
			'Custom label overrun' => [
				'foo',
				[
					'name' => null,
					'number' => 10,
					'key' => 4,
					'count' => -1,
				],
				'(cite_reference_link|4+|4|' .
					'cite_error_no_link_label_group&#124;foo&#124;cite_link_label_group-foo)'
			],
			'Named ref' => [
				'',
				[
					'name' => 'a',
					'number' => 3,
					'key' => 4,
					'count' => 0,
				],
				'(cite_reference_link|a+4-0|a-4|3)'
			],
			'Named ref reused' => [
				'',
				[
					'name' => 'a',
					'number' => 3,
					'key' => 4,
					'count' => 2,
				],
				'(cite_reference_link|a+4-2|a-4|3)'
			],
			'Subreference' => [
				'',
				[
					'name' => null,
					'number' => 3,
					'key' => 4,
					'count' => -1,
					'extends' => 'b',
					'extendsIndex' => 2,
				],
				'(cite_reference_link|4+|4|3.2)'
			],
		];
	}

	/**
	 * @covers ::getLinkLabel
	 *
	 * @dataProvider provideGetLinkLabel
	 *
	 * @param string|null $expectedLabel
	 * @param int $offset
	 * @param string $group
	 * @param string $label
	 * @param string|null $labelList
	 */
	public function testGetLinkLabel( $expectedLabel, $offset, $group, $labelList ) {
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( ...$args ) use ( $labelList ) {
				$mockMessage = $this->createMock( Message::class );
				$mockMessage->method( 'isDisabled' )->willReturn( $labelList === null );
				$mockMessage->method( 'plain' )->willReturn( $labelList );
				return $mockMessage;
			}
		);
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'plain' )->willReturnCallback(
			function ( ...$args ) {
				return implode( '|', $args );
			}
		);
		/** @var FootnoteMarkFormatter $formatter */
		$formatter = TestingAccessWrapper::newFromObject( new FootnoteMarkFormatter(
			$this->createMock( Parser::class ),
			$mockErrorReporter,
			$this->createMock( CiteKeyFormatter::class ),
			$mockMessageLocalizer ) );

		$output = $formatter->getLinkLabel( $group, $offset );
		$this->assertSame( $expectedLabel, $output );
	}

	public function provideGetLinkLabel() {
		yield [ null, 1, '', null ];
		yield [ null, 2, '', null ];
		yield [ null, 1, 'foo', null ];
		yield [ null, 2, 'foo', null ];
		yield [ 'a', 1, 'foo', 'a b c' ];
		yield [ 'b', 2, 'foo', 'a b c' ];
		yield [ 'å', 1, 'foo', 'å β' ];
		yield [ 'cite_error_no_link_label_group|foo|cite_link_label_group-foo', 4, 'foo', 'a b c' ];
	}
}
