<?php

namespace Cite\Tests\Unit;

use Cite\ResourceLoader\MWCitationAutonameTemplateMap;
use MediaWiki\Message\Message;
use MediaWiki\ResourceLoader\Context;

/**
 * @covers \Cite\ResourceLoader\MWCitationAutonameTemplateMap
 * @license GPL-2.0-or-later
 */
class MWCitationAutonameTemplateMapTest extends \MediaWikiUnitTestCase {

	/**
	 * @dataProvider provideInvalidJson
	 */
	public function testWhenJsonIsEmptyOrInvalidReturnsEmptyMapFromJson( string $jsonBody ) {
		$msg = $this->createMock( Message::class );
		$msg->method( 'inContentLanguage' )
			->willReturnSelf();
		$msg->method( 'plain' )
			->willReturn( $jsonBody );
		$context = $this->createStub( Context::class );
		$context->method( 'msg' )
			->willReturn( $msg );

		$this->assertSame(
			[ 'citeAutonameTemplateMap' => [] ],
			MWCitationAutonameTemplateMap::getAutonameTemplateMap( $context )
		);
	}

	/**
	 * @return iterable<array{string}>
	 */
	public static function provideInvalidJson(): iterable {
		yield 'empty string' => [ '' ];
		yield 'empty object' => [ '{}' ];
		yield 'empty array' => [ '[]' ];
		yield 'string' => [ '"this should error"' ];
		yield 'invalid json' => [ '{"an unclosed object":""' ];
		yield 'invalid template name list' => [ json_encode( [
			'autonameTemplateBook' => 'Cite book'
		], JSON_THROW_ON_ERROR ) ];
		yield 'invalid template name in list' => [ json_encode( [
			'autonameTemplateBook' => [ 2, null, false ]
		], JSON_THROW_ON_ERROR ) ];
	}

	public function testCreatesLookupMap() {
		$templateMap = [
			"autonameTemplateBook" => [ "Cite book", "Cite journal" ],
			"autonameTemplateWeb" => [ "Cite web", "Cite insta", "Cite archive" ]
		];
		$context = $this->givenContextWithTemplateMap( $templateMap );

		$result = MWCitationAutonameTemplateMap::getAutonameTemplateMap( $context );
		$this->assertArrayHasKey( 'citeAutonameTemplateMap', $result );
		$reverseTemplateMap = $result['citeAutonameTemplateMap'];
		$this->assertCount( 5, $reverseTemplateMap, 'should turn value arrays into keys' );
		$this->assertSame( "autonameTemplateBook", $reverseTemplateMap['Cite book'] );
		$this->assertSame( "autonameTemplateBook", $reverseTemplateMap['Cite journal'] );
		$this->assertSame( "autonameTemplateWeb", $reverseTemplateMap['Cite web'] );
		$this->assertSame( "autonameTemplateWeb", $reverseTemplateMap['Cite insta'] );
		$this->assertSame( "autonameTemplateWeb", $reverseTemplateMap['Cite archive'] );
	}

	public function testLaterDuplicatesInLookupMapAreIgnored() {
		$templateMap = [
			"autonameTemplateBook" => [ "Cite book", "Cite journal" ],
			"autonameTemplateWeb" => [ "Cite book", "Cite insta", "Cite archive" ]
		];
		$context = $this->givenContextWithTemplateMap( $templateMap );

		$result = MWCitationAutonameTemplateMap::getAutonameTemplateMap( $context );
		$this->assertArrayHasKey( 'citeAutonameTemplateMap', $result );
		$reverseTemplateMap = $result['citeAutonameTemplateMap'];
		$this->assertSame(
			"autonameTemplateBook",
			$reverseTemplateMap['Cite book'],
			'Later templates should not override earlier templates'
		);
	}

	public function testArrayWithStarMarksDefault() {
		$templateMap = [
			"autonameDefault" => [ '*' ],
			"autonameIgnoredDefault" => [ '*' ],
			"autonameTemplateBook" => [ "Cite book", "Cite journal" ],
		];
		$context = $this->givenContextWithTemplateMap( $templateMap );

		$result = MWCitationAutonameTemplateMap::getAutonameTemplateMap( $context );
		$this->assertArrayHasKey( 'citeAutonameTemplateMap', $result );
		$reverseTemplateMap = $result['citeAutonameTemplateMap'];
		$this->assertSame(
			"autonameDefault",
			$reverseTemplateMap['*'],
			'First array with star gets selected as default'
		);
	}

	private function givenContextWithTemplateMap( array $templateMap ): Context {
		$msg = $this->createStub( Message::class );
		$msg->method( 'inContentLanguage' )
			->willReturnSelf();
		$msg->method( 'plain' )
			->willReturn( json_encode( $templateMap ) );

		$context = $this->createStub( Context::class );
		$context->method( 'msg' )
			->willReturn( $msg );

		return $context;
	}

}
