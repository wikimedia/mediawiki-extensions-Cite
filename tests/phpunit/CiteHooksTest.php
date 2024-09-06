<?php

namespace Cite\Tests;

use ApiQuerySiteinfo;
use Cite\Hooks\CiteHooks;
use MediaWiki\Config\HashConfig;
use MediaWiki\ResourceLoader\ResourceLoader;
use MediaWiki\User\Options\StaticUserOptionsLookup;

/**
 * @covers \Cite\Hooks\CiteHooks
 * @license GPL-2.0-or-later
 */
class CiteHooksTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @dataProvider provideBooleans
	 */
	public function testOnResourceLoaderGetConfigVars( bool $enabled ) {
		$vars = [];

		$config = new HashConfig( [
			'CiteVisualEditorOtherGroup' => $enabled,
			'CiteResponsiveReferences' => $enabled,
			'CiteBookReferencing' => $enabled,
		] );

		( new CiteHooks(
			$this->getServiceContainer()->getService( 'Cite.ReferencePreviewsContext' ),
			new StaticUserOptionsLookup( [] )
		) )
			->onResourceLoaderGetConfigVars( $vars, 'vector', $config );

		$this->assertSame( [
			'wgCiteVisualEditorOtherGroup' => $enabled,
			'wgCiteResponsiveReferences' => $enabled,
			'wgCiteBookReferencing' => $enabled,
		], $vars );
	}

	/**
	 * @dataProvider provideBooleans
	 */
	public function testOnResourceLoaderRegisterModules( bool $enabled ) {
		$this->markTestSkippedIfExtensionNotLoaded( 'Popups' );

		$resourceLoader = $this->createMock( ResourceLoader::class );
		$resourceLoader->method( 'getConfig' )
			->willReturn( new HashConfig( [ 'CiteReferencePreviews' => $enabled ] ) );
		$resourceLoader->expects( $this->exactly( (int)$enabled ) )
			->method( 'register' );

		( new CiteHooks(
			$this->getServiceContainer()->getService( 'Cite.ReferencePreviewsContext' ),
			new StaticUserOptionsLookup( [] )
		) )
			->onResourceLoaderRegisterModules( $resourceLoader );
	}

	/**
	 * @dataProvider provideBooleans
	 */
	public function testOnAPIQuerySiteInfoGeneralInfo( bool $enabled ) {
		$api = $this->createMock( ApiQuerySiteinfo::class );
		$api->expects( $this->once() )
			->method( 'getConfig' )
			->willReturn( new HashConfig( [ 'CiteResponsiveReferences' => $enabled ] ) );

		$data = [];

		( new CiteHooks(
			$this->getServiceContainer()->getService( 'Cite.ReferencePreviewsContext' ),
			new StaticUserOptionsLookup( [] )
		) )
			->onAPIQuerySiteInfoGeneralInfo( $api, $data );

		$this->assertSame( [ 'citeresponsivereferences' => $enabled ], $data );
	}

	public static function provideBooleans() {
		yield [ true ];
		yield [ false ];
	}

}
