<?php

namespace Cite\Tests\Unit;

use Cite\ResourceLoader\MWCitationToolsDefinition;
use MediaWiki\Message\Message;
use MediaWiki\ResourceLoader\Context;

/**
 * @covers \Cite\ResourceLoader\MWCitationToolsDefinition
 * @license GPL-2.0-or-later
 */
class MWCitationToolsDefinitionTest extends \MediaWikiUnitTestCase {

	/**
	 * @dataProvider provideToolDefinitions
	 */
	public function testGetScript( array $toolDefinition, array $expected, array $messages ) {
		$context = $this->createResourceLoaderContext( $toolDefinition, $messages );

		$this->assertSame( [ $expected ], MWCitationToolsDefinition::getTools( $context ) );
	}

	/**
	 * @dataProvider provideEmptyToolDefinitions
	 */
	public function testGetScriptWithMalformedDefinition( array $toolDefinition ) {
		$context = $this->createResourceLoaderContext( $toolDefinition, [] );

		$this->assertSame( [], MWCitationToolsDefinition::getTools( $context ) );
	}

	public static function provideEmptyToolDefinitions(): iterable {
		yield 'skips an empty cite tool definition' => [ [] ];
		yield 'skips a tool definition with an empty name' => [ [ 'name' => '' ] ];
	}

	public static function provideToolDefinitions(): iterable {
		yield 'uses a hard-coded title and translates deprecated icons' => [
			[ 'name' => 'with-title', 'title' => 'Hard-coded title', 'icon' => 'ref-cite-web' ],
			[
				'name' => 'with-title',
				'title' => 'Hard-coded title',
				'icon' => 'browser',
				'autoname' => 'Hard-coded title-',
			],
			[
				'visualeditor-cite-tool-name-with-title' => null,
				'visualeditor-cite-tool-name-with-title-autoname' => null,
			]
		];
		yield 'falls back to the tool name when its title message is disabled' => [
			[ 'name' => 'missing-message', 'icon' => null ],
			[
				'name' => 'missing-message',
				'title' => 'missing-message',
				'autoname' => null,
			],
			[
				'visualeditor-cite-tool-name-missing-message' => null,
				'visualeditor-cite-tool-name-missing-message-autoname' => null,
			]
		];
		yield 'uses the title message and default icon for a supported tool' => [
			[ 'name' => 'web' ],
			[
				'name' => 'web',
				'title' => 'Website',
				'autoname' => 'Website-',
				'icon' => 'browser',
			],
			[
				'visualeditor-cite-tool-name-web' => 'Website',
				'visualeditor-cite-tool-name-web-autoname' => null,
			]
		];
		yield 'uses an autoname override message and allows icon overrides' => [
			[ 'name' => 'web-overwritten', 'icon' => 'some-icon' ],
			[
				'name' => 'web-overwritten',
				'icon' => 'some-icon',
				'title' => 'Website',
				'autoname' => 'netzseite',
			],
			[
				'visualeditor-cite-tool-name-web-overwritten' => 'Website',
				'visualeditor-cite-tool-name-web-overwritten-autoname' => 'netzseite',
			]
		];
	}

	private function createResourceLoaderContext( array $definition, array $messages ): Context {
		$messageObjects = [];
		foreach ( $messages as $key => $text ) {
			$messageObjects[$key] = $this->newMockMessage( $text );
		}
		$disabledMessage = $this->newMockMessage( null );
		$definitionMessage = $this->newToolDefinitionMessage( [ $definition ] );

		$context = $this->createStub( Context::class );
		$context->method( 'msg' )
			->willReturnCallback( static function ( string $key ) use (
				$disabledMessage,
				$definitionMessage,
				$messageObjects
			) {
				return $key === 'cite-tool-definition.json' ?
					$definitionMessage :
					$messageObjects[$key] ?? $disabledMessage;
			} );
		return $context;
	}

	private function newMockMessage( ?string $msgText ): Message {
		$msg = $this->createMock( Message::class );
		$msg->method( 'inContentLanguage' )->willReturnSelf();
		$msg->method( 'isDisabled' )->willReturn( $msgText === null );
		$msg->method( 'text' )->willReturn( $msgText ?: '' );
		return $msg;
	}

	private function newToolDefinitionMessage( array $definition ): Message {
		$msg = $this->createMock( Message::class );
		$msg->method( 'inContentLanguage' )
			->willReturnSelf();
		$msg->method( 'plain' )
			->willReturn( json_encode( $definition ) );
		return $msg;
	}

}
