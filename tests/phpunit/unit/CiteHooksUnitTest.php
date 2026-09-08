<?php

namespace Cite\Tests\Unit;

use Cite\Hooks\CiteHooks;
use MediaWiki\Message\Message;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\Context;
use MediaWiki\Title\Title;
use MediaWiki\User\Options\StaticUserOptionsLookup;

/**
 * @covers \Cite\Hooks\CiteHooks
 * @license GPL-2.0-or-later
 */
class CiteHooksUnitTest extends \MediaWikiUnitTestCase {

	public function testOnContentHandlerDefaultModelFor() {
		$title = $this->createMock( Title::class );
		$title->method( 'inNamespace' )
			->willReturn( true );
		$title->method( 'getText' )
			->willReturn( 'Cite-tool-definition.json' );

		( new CiteHooks(
			$this->createNoOpMock( ExtensionRegistry::class ),
			new StaticUserOptionsLookup( [] )
		) )
			->onContentHandlerDefaultModelFor( $title, $model );

		$this->assertSame( CONTENT_MODEL_JSON, $model );
	}

	/**
	 * @dataProvider provideReferenceMessages
	 */
	public function testGetReferenceNameMessages(
		string $autonameText,
		string $autonameOverrideText,
		string $expectedPrefix
	) {
		$autoname = $this->createMock( Message::class );
		$autoname->expects( $this->atMost( 1 ) )
			->method( 'inContentLanguage' )
			->willReturnSelf();
		$autoname->method( 'exists' )->willReturn( (bool)$autonameText );
		$autoname->method( 'text' )->willReturn( $autonameText );

		$autonameOverride = $this->createMock( Message::class );
		$autonameOverride->expects( $this->once() )
			->method( 'inContentLanguage' )
			->willReturnSelf();
		$autonameOverride->method( 'exists' )->willReturn( (bool)$autonameOverrideText );
		$autonameOverride->method( 'text' )->willReturn( $autonameOverrideText );

		$context = $this->createStub( Context::class );
		$context->method( 'msg' )->willReturnMap( [
			[ 'cite-ve-dialogbutton-reference-title', $autoname ],
			[ 'cite-ve-dialogbutton-reference-title-autoname', $autonameOverride ]
		] );

		$this->assertSame(
			[ 'referenceAutonamePrefix' => $expectedPrefix ],
			CiteHooks::getReferenceNameMessages( $context )
		);
	}

	public static function provideReferenceMessages(): iterable {
		yield 'basic autoname prefix' => [ 'TestRef', '', 'TestRef-' ];
		yield 'broken translation of autoname prefix' => [ '', '', '' ];
		yield 'autoname prefix override' => [ 'TestRef', 'OverrideTestRef', 'OverrideTestRef' ];
	}

}
