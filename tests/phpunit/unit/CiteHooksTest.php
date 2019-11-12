<?php

namespace Cite\Tests\Unit;

use Cite;
use CiteHooks;
use HashConfig;
use LinksUpdate;
use ParserOutput;
use ResourceLoader;
use Title;

/**
 * @coversDefaultClass \CiteHooks
 */
class CiteHooksTest extends \MediaWikiUnitTestCase {

	protected function setUp() : void {
		global $wgCiteStoreReferencesData;

		parent::setUp();
		$wgCiteStoreReferencesData = true;
	}

	/**
	 * @covers ::onContentHandlerDefaultModelFor
	 */
	public function testOnContentHandlerDefaultModelFor() {
		$title = $this->createMock( Title::class );
		$title->method( 'inNamespace' )
			->willReturn( true );
		$title->method( 'getText' )
			->willReturn( 'Cite-tool-definition.json' );

		CiteHooks::onContentHandlerDefaultModelFor( $title, $model );

		$this->assertSame( CONTENT_MODEL_JSON, $model );
	}

	/**
	 * @covers ::onResourceLoaderTestModules
	 */
	public function testOnResourceLoaderTestModules() {
		$testModules = [];
		$resourceLoader = $this->createMock( ResourceLoader::class );
		$resourceLoader->method( 'getConfig' )
			->willReturn( new HashConfig( [
				'ResourceModules' => [ 'ext.visualEditor.mediawiki' => true ],
			] ) );

		CiteHooks::onResourceLoaderTestModules( $testModules, $resourceLoader );

		$this->assertArrayHasKey( 'ext.cite.visualEditor.test', $testModules['qunit'] );
	}

	/**
	 * @covers ::onResourceLoaderRegisterModules
	 */
	public function testOnResourceLoaderRegisterModules() {
		$resourceLoader = $this->createMock( ResourceLoader::class );
		$resourceLoader->expects( $this->atLeastOnce() )
			->method( 'register' );

		CiteHooks::onResourceLoaderRegisterModules( $resourceLoader );
	}

	/**
	 * @covers ::onLinksUpdate
	 */
	public function testOnLinksUpdate() {
		$parserOutput = $this->createMock( ParserOutput::class );
		$parserOutput->method( 'getExtensionData' )
			->willReturn( true );
		$parserOutput->expects( $this->once() )
			->method( 'setExtensionData' )
			->with( Cite::EXT_DATA_KEY, null );

		$linksUpdate = $this->createMock( LinksUpdate::class );
		$linksUpdate->method( 'getParserOutput' )
			->willReturn( $parserOutput );

		CiteHooks::onLinksUpdate( $linksUpdate );

		$this->assertArrayHasKey( 'references-1', $linksUpdate->mProperties );
	}

}
