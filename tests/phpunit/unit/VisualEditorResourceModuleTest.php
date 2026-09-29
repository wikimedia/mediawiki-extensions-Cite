<?php

namespace Cite\Tests\Unit;

use Cite\Hooks\ResourceLoaderModules\VisualEditorResourceModule;
use MediaWiki\Message\Message;
use MediaWiki\ResourceLoader\Context;

/**
 * @covers \Cite\Hooks\ResourceLoaderModules\VisualEditorResourceModule
 * @license GPL-2.0-or-later
 */
class VisualEditorResourceModuleTest extends \MediaWikiUnitTestCase {

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
			VisualEditorResourceModule::getReferenceNameMessages( $context )
		);
	}

	public static function provideReferenceMessages(): iterable {
		yield 'basic autoname prefix' => [ 'TestRef', '', 'TestRef-' ];
		yield 'broken translation of autoname prefix' => [ '', '', '' ];
		yield 'autoname prefix override' => [ 'TestRef', 'OverrideTestRef', 'OverrideTestRef' ];
	}

}
