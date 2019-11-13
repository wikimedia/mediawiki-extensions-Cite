<?php

namespace Cite\Tests;

use CiteCSSFileModule;
use MediaWiki\MediaWikiServices;
use ResourceLoaderContext;

/**
 * @covers \CiteCSSFileModule
 */
class CiteCSSFileModuleTest extends \MediaWikiIntegrationTestCase {

	protected function setUp() : void {
		parent::setUp();

		$this->setService(
			'ContentLanguage',
			MediaWikiServices::getInstance()->getLanguageFactory()->getLanguage( 'fa' )
		);
	}

	public function testModule() {
		$module = new CiteCSSFileModule( [], __DIR__ . '/../../modules' );
		$styles = $module->getStyleFiles( $this->createMock( ResourceLoaderContext::class ) );
		$this->assertSame( [ 'ext.cite.style.fa.css' ], $styles['all'] );
	}

}
