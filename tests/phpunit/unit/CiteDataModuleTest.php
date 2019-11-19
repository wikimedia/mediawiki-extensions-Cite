<?php

namespace Cite\Tests\Unit;

use Cite\ResourceLoader\CiteDataModule;
use Message;
use ResourceLoaderContext;
use WebRequest;

/**
 * @covers \Cite\ResourceLoader\CiteDataModule
 *
 * @license GPL-2.0-or-later
 */
class CiteDataModuleTest extends \MediaWikiUnitTestCase {

	protected function setUp() : void {
		global $wgRequest;

		parent::setUp();
		$wgRequest = $this->createMock( WebRequest::class );
	}

	public function testGetScript() {
		$module = new CiteDataModule();
		$context = $this->createResourceLoaderContext();

		$this->assertSame(
			've.init.platform.addMessages({"cite-tool-definition.json":"[{\"title\":\"\"}]"});',
			$module->getScript( $context )
		);
	}

	public function testGetDependencies() {
		$module = new CiteDataModule();

		$this->assertContainsOnly( 'string', $module->getDependencies() );
	}

	public function testGetDefinitionSummary() {
		$module = new CiteDataModule();
		$context = $this->createResourceLoaderContext();

		$this->assertSame(
			$module->getScript( $context ),
			$module->getDefinitionSummary( $context )[0]['script']
		);
	}

	/**
	 * @return ResourceLoaderContext
	 */
	private function createResourceLoaderContext() {
		$msg = $this->createMock( Message::class );
		$msg->method( 'inContentLanguage' )
			->willReturnSelf();
		$msg->method( 'plain' )
			->willReturn( '[{"title":""}]' );

		$context = $this->createMock( ResourceLoaderContext::class );
		$context->method( 'msg' )
			->willReturn( $msg );
		return $context;
	}

}
