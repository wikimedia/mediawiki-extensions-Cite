<?php

namespace Cite\Tests\Unit;

use Cite\ErrorReporter;
use Cite\ReferenceMessageLocalizer;
use Language;
use MediaWikiUnitTestCase;
use Message;
use Parser;
use ParserOptions;

/**
 * @coversDefaultClass \Cite\ErrorReporter
 *
 * @license GPL-2.0-or-later
 */
class ErrorReporterTest extends MediaWikiUnitTestCase {

	/**
	 * @covers ::plain
	 * @covers ::__construct
	 * @dataProvider provideErrors
	 */
	public function testPlain(
		string $key,
		string $expectedHtml,
		array $expectedCategories
	) {
		$reporter = $this->createReporter();
		$mockParser = $this->createParser( $expectedCategories );
		$this->assertSame(
			$expectedHtml,
			$reporter->plain( $mockParser, $key, 'first param' ) );
	}

	/**
	 * @covers ::halfParsed
	 */
	public function testHalfParsed() {
		$reporter = $this->createReporter();
		$mockParser = $this->createParser( [] );
		$expectedHtml = '';
		$this->assertSame(
			'[<span class="warning mw-ext-cite-warning mw-ext-cite-warning-example" lang="qqx" ' .
				'dir="rtl">(cite_warning|(cite_warning_example|first param))</span>]',
			$reporter->halfParsed( $mockParser, 'cite_warning_example', 'first param' ) );
	}

	public function provideErrors() {
		return [
			'Example error' => [
				'cite_error_example',
				'<span class="error mw-ext-cite-error" lang="qqx" dir="rtl">' .
					'(cite_error|(cite_error_example|first param))</span>',
				[ 'cite-tracking-category-cite-error' ]
			],
			'Warning error' => [
				'cite_warning_example',
				'<span class="warning mw-ext-cite-warning mw-ext-cite-warning-example" lang="qqx" ' .
					'dir="rtl">(cite_warning|(cite_warning_example|first param))</span>',
				[]
			],
		];
	}

	private function createReporter() : ErrorReporter {
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( ...$args ) {
				$rendered = '(' . implode( '|', $args ) . ')';
				$mockMessage = $this->createMock( Message::class );
				$mockMessage->method( 'getKey' )->willReturn( $args[0] );
				$mockMessage->method( 'plain' )->willReturn( $rendered );
				// FIXME: Doesn't prove that we've set the language correctly.
				$mockMessage->method( 'inLanguage' )->willReturnSelf();
				return $mockMessage;
			}
		);

		/** @var ReferenceMessageLocalizer $mockMessageLocalizer */
		return new ErrorReporter( $mockMessageLocalizer );
	}

	public function createParser( array $expectedCategories ) {
		$mockParser = $this->createMock( Parser::class );
		foreach ( $expectedCategories as $category ) {
			$mockParser->method( 'addTrackingCategory' )->with( $category );
		}
		$mockParser->method( 'getOptions' )->willReturnCallback(
			function () {
				$mockLanguage = $this->createMock( Language::class );
				$mockLanguage->method( 'getCode' )->willReturn( 'ui' );
				$mockLanguage->method( 'getDir' )->willReturn( 'rtl' );
				$mockLanguage->method( 'getHtmlCode' )->willReturn( 'qqx' );
				$mockOptions = $this->createMock( ParserOptions::class );
				$mockOptions->method( 'getUserLangObj' )->willReturn( $mockLanguage );
				return $mockOptions;
			}
		);
		$mockParser->method( 'recursiveTagParse' )->willReturnCallback(
			function ( $content ) {
				return '[' . $content . ']';
			}
		);
		return $mockParser;
	}

}
