<?php

namespace Cite\Tests;

use ApiQuerySiteinfo;
use CiteHooks;
use HashBagOStuff;
use LinksUpdate;
use Title;
use WANObjectCache;

/**
 * @coversDefaultClass \CiteHooks
 */
class CiteHooksTest extends \MediaWikiIntegrationTestCase {

	protected function setUp() : void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgCiteStoreReferencesData' => true,
		] );
	}

	/**
	 * @covers ::onLinksUpdateComplete
	 */
	public function testOnLinksUpdateComplete() {
		$cache = $this->getMockBuilder( WANObjectCache::class )
			->setConstructorArgs( [ [ 'cache' => new HashBagOStuff() ] ] )
			->setMethods( [ 'makeKey', 'relayPurge' ] )
			->getMock();
		$cache->method( 'makeKey' )
			->willReturn( '<KEY>' );
		// What we actually want to check here is if WANObjectCache::delete() is called, but it's
		// final and can't be mocked.
		$cache->expects( $this->once() )
			->method( 'relayPurge' )
			->with(
				'WANCache:v:<KEY>',
				WANObjectCache::MAX_COMMIT_DELAY,
				WANObjectCache::HOLDOFF_TTL_NONE
			);
		$this->setService( 'MainWANObjectCache', $cache );

		$linksUpdate = $this->createMock( LinksUpdate::class );
		$linksUpdate->method( 'getAddedProperties' )
			->willReturn( [ 'references-1' => true ] );
		$linksUpdate->method( 'getTitle' )
			->willReturn( $this->createMock( Title::class ) );

		CiteHooks::onLinksUpdateComplete( $linksUpdate );
	}

	/**
	 * @covers ::onResourceLoaderGetConfigVars
	 */
	public function testOnResourceLoaderGetConfigVars() {
		$vars = [];

		CiteHooks::onResourceLoaderGetConfigVars( $vars );

		$this->assertArrayHasKey( 'wgCiteVisualEditorOtherGroup', $vars );
		$this->assertArrayHasKey( 'wgCiteResponsiveReferences', $vars );
	}

	/**
	 * @covers ::onAPIQuerySiteInfoGeneralInfo
	 */
	public function testOnAPIQuerySiteInfoGeneralInfo() {
		$api = $this->createMock( ApiQuerySiteinfo::class );
		$data = [];

		CiteHooks::onAPIQuerySiteInfoGeneralInfo( $api, $data );

		$this->assertArrayHasKey( 'citeresponsivereferences', $data );
	}

}
