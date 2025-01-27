<?php

namespace Cite\Tests\Unit;

use Cite\AlphabetsProvider;
use Cite\Cite;
use Cite\MarkSymbolRenderer;
use Cite\ReferenceMessageLocalizer;
use MediaWiki\Config\Config;
use MediaWiki\Config\HashConfig;
use MediaWiki\Message\Message;

/**
 * @covers \Cite\MarkSymbolRenderer
 * @license GPL-2.0-or-later
 */
class MarkSymbolRendererTest extends \MediaWikiUnitTestCase {

	/**
	 * @dataProvider provideCustomizedLinkLabels
	 */
	public function testMakeLabel( ?string $expectedLabel, int $offset, string $group, ?string $labelList ) {
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'msg' )->willReturnCallback(
			function ( ...$args ) use ( $labelList ) {
				$msg = $this->createMock( Message::class );
				$msg->method( 'isDisabled' )->willReturn( $labelList === null );
				$msg->method( 'plain' )->willReturn( $labelList );
				return $msg;
			}
		);
		$mockMessageLocalizer->method( 'localizeDigits' )->willReturnCallback(
			static function ( $number ) {
				return (string)$number;
			}
		);
		$renderer = new MarkSymbolRenderer(
			$mockMessageLocalizer,
			$this->createNoOpMock( AlphabetsProvider::class ),
			$this->createNoOpMock( Config::class )
		);

		$output = $renderer->makeLabel( $group, $offset );
		$this->assertSame( $expectedLabel, $output );
	}

	public static function provideCustomizedLinkLabels() {
		yield [ '1', 1, '', null ];
		yield [ '2', 2, '', null ];
		yield [ 'foo 1', 1, 'foo', null ];
		yield [ 'foo 2', 2, 'foo', null ];
		yield [ 'a', 1, 'foo', 'a b c' ];
		yield [ 'b', 2, 'foo', 'a b c' ];
		yield [ 'å', 1, 'foo', 'å β' ];
		yield [ 'foo 4', 4, 'foo', 'a b c' ];
	}

	public function testDefaultGroupCannotHaveCustomLinkLabels() {
		$mockMessageLocalizer = $this->createMock( ReferenceMessageLocalizer::class );
		$mockMessageLocalizer->method( 'localizeDigits' )->willReturnCallback(
			static function ( $number ) {
				return (string)$number;
			}
		);
		// Assert that ReferenceMessageLocalizer::msg( 'cite_link_label_group-' )
		// isn't called by not defining the ->msg method.

		$renderer = new MarkSymbolRenderer(
			$mockMessageLocalizer,
			$this->createNoOpMock( AlphabetsProvider::class ),
			$this->createNoOpMock( Config::class )
		);

		$this->assertSame( '1', $renderer->makeLabel( Cite::DEFAULT_GROUP, 1 ) );
	}

	/**
	 * @dataProvider provideGetBacklinkAlphabet
	 */
	public function testGetBacklinkAlphabet( $configAlpha, ?array $indexChars, array $resultAlpha ) {
		$mockAlphabets = $this->createMock( AlphabetsProvider::class );
		$mockAlphabets->method( 'getIndexCharacters' )->willReturn(
			$indexChars
		);

		$config = new HashConfig( [
			'CiteDefaultBacklinkAlphabet' => $configAlpha,
		] );

		$renderer = new MarkSymbolRenderer(
			$this->createNoOpMock( ReferenceMessageLocalizer::class ),
			$mockAlphabets,
			$config
		);

		$this->assertSame( $resultAlpha, $renderer->getBacklinkAlphabet( '' ) );
	}

	public static function provideGetBacklinkAlphabet() {
		$fallback = range( 'a', 'z' );
		yield [ null, null, $fallback ];
		yield [ false, null, $fallback ];
		yield [ '', null, $fallback ];
		yield [ ' ', null, $fallback ];
		yield [ 'abc', null, [ 'abc' ] ];
		yield [ 'b a r', null, [ 'b', 'a', 'r' ] ];
		yield [ 'b a r', [ 'f', 'o' ], [ 'b', 'a', 'r' ] ];
		yield [ null, [ 'f', 'o' ], [ 'f', 'o' ] ];
		yield [ null, [ 'F', 'O' ], [ 'f', 'o' ] ];
	}

	public function testMakeBacklinkLabel() {
		$renderer = new MarkSymbolRenderer(
			$this->createNoOpMock( ReferenceMessageLocalizer::class ),
			$this->createNoOpMock( AlphabetsProvider::class ),
			$this->createNoOpMock( Config::class )
		);

		$this->assertSame( 'ab', $renderer->makeBacklinkLabel( [ 'a', 'b', 'c' ], 5 ) );
		$this->assertSame( 'abcabcabcabcabc', $renderer->makeBacklinkLabel( [ 'abc' ], 5 ) );
		$this->assertSame( '', $renderer->makeBacklinkLabel( [], 5 ) );
	}

}
