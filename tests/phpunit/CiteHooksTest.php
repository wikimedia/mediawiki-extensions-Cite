<?php

namespace Cite\Tests;

use Cite\Hooks\CiteHooks;
use Cite\Hooks\ReferencePreviewsHooks;
use Cite\ReferencePreviews\ReferencePreviewsContext;
use Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration;
use MediaWiki\Config\HashConfig;
use MediaWiki\Extension\CommunityConfiguration\CommunityConfigurationServices;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;
use MediaWiki\User\Options\StaticUserOptionsLookup;
use MediaWiki\User\User;

/**
 * @covers \Cite\Hooks\CiteHooks
 * @covers \Cite\Hooks\ReferencePreviewsHooks
 * @covers \Cite\Hooks\ResourceLoaderModules\CommunityConfigurationResourceModule
 * @covers \Cite\Hooks\ResourceLoaderModules\VisualEditorResourceModule
 * @covers \Cite\Hooks\ResourceLoaderModules\WikiEditorResourceModule
 * @license GPL-2.0-or-later
 */
class CiteHooksTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @dataProvider provideConfigVars
	 */
	public function testOnResourceLoaderGetConfigVars( $input, bool $expected ) {
		$registry = $this->getServiceContainer()->getExtensionRegistry();
		$this->overrideConfigValue(	'CiteVisualEditorOtherGroup', $input );
		$this->overrideConfigValue( 'CiteResponsiveReferences', $input, );
		$this->overrideConfigValue( 'CiteSubReferencing', $input );
		$this->overrideConfigValue( 'CiteCitationTypeAutoNames', $input );

		// When CiteCitationTypeAutoNames config is false, the 'Cite-VisualEditor-Autonames'
		// provider won't be active, leading to an error when trying to override it.
		if ( $input ) {
			if ( !$registry->isLoaded( 'CommunityConfiguration' ) ) {
				$this->markTestSkipped( 'Community Configuration Extension not loaded' );
			}
			$providerId = 'Cite-VisualEditor-Autonames';
			$providerSpec = CommunityConfigurationServices::wrap( $this->getServiceContainer() )
				->getConfigurationProviderFactory()
				->getProviderSpec( $providerId );

			$providerSpec['store'] = [
				'type' => 'static',
				'args' => [
					(object)[ 'enable' => true ],
					$providerId
				],
			];
			$this->overrideConfigValue(
				'CommunityConfigurationProviders',
				[ $providerId => $providerSpec ]
			);
		}

		$vars = [];

		( new CiteHooks(
			$registry,
			new StaticUserOptionsLookup( [] )
		) )
			->onResourceLoaderGetConfigVars( $vars, 'vector', $this->getServiceContainer()->getMainConfig() );

		$this->assertSame( [
			'wgCiteVisualEditorOtherGroup' => $expected,
			'wgCiteResponsiveReferences' => $expected,
			'wgCiteSubReferencing' => $expected,
			'wgCiteCitationTypeAutoNames' => $expected,
		], $vars );
	}

	public static function provideConfigVars() {
		yield [ true, true ];
		yield [ false, false ];
		yield [ 0, false ];
		yield [ 'FooBar', true ];
	}

	/**
	 * @dataProvider provideAutnamesConfigVars
	 */
	public function testOnResourceLoaderGetConfigVars_Autonames(
		bool $flag,
		bool $ccLoaded,
		bool $ccEnabledSetting,
		bool $expected
	) {
		$registry = $this->getServiceContainer()->getExtensionRegistry();
		if ( !$registry->isLoaded( 'CommunityConfiguration' ) ) {
			$this->markTestSkipped( 'Community Configuration Extension not loaded' );
		}

		$extensionRegistryMock = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistryMock->method( 'isLoaded' )->willReturn( $ccLoaded );

		$this->overrideConfigValue( 'CiteCitationTypeAutoNames', $flag );
		if ( $ccLoaded ) {
			$providerId = 'Cite-VisualEditor-Autonames';
			$providerSpec = CommunityConfigurationServices::wrap( $this->getServiceContainer() )
				->getConfigurationProviderFactory()
				->getProviderSpec( $providerId );

			$providerSpec['store'] = [
				'type' => 'static',
				'args' => [
					(object)[ 'enable' => $ccEnabledSetting ],
					$providerId
				],
			];
			$this->overrideConfigValue(
				'CommunityConfigurationProviders',
				[ $providerId => $providerSpec ]
			);
		}

		$vars = [];

		( new CiteHooks(
			$extensionRegistryMock,
			new StaticUserOptionsLookup( [] )
		) )
			->onResourceLoaderGetConfigVars( $vars, 'vector', $this->getServiceContainer()->getMainConfig() );

		$this->assertSame( $expected, $vars['wgCiteCitationTypeAutoNames'] );
	}

	public static function provideAutnamesConfigVars(): iterable {
		yield 'feature flag disables autonames completely' => [ false, false, false, false ];
		yield 'feature flag enables autonames when CC is not loaded' => [ true, false, false, true ];
		yield 'CC setting toggles autonames off when CC is loaded' => [ true, true, false, false ];
		yield 'CC setting toggles autonames on when CC is loaded' => [ true, true, true, true ];
	}

	/**
	 * @dataProvider provideBooleans
	 */
	public function testResourceLoaderRegistration_ReferencePreviews( bool $enabled ) {
		$extensionRegistry = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistry->method( 'isLoaded' )->willReturn( true );

		$resourceLoader = $this->createMock( ResourceLoader::class );
		$resourceLoader->method( 'getConfig' )
			->willReturn( new HashConfig( [ 'CiteReferencePreviews' => $enabled ] ) );
		$resourceLoader->expects( $this->exactly( (int)$enabled ) )
			->method( 'register' )
			->willReturnCallback( function ( array $modules ) {
				$this->assertArrayHasKey( 'ext.cite.referencePreviews', $modules );
			} );

		( new ReferencePreviewsHooks(
			$extensionRegistry,
			$this->createNoOpMock( ReferencePreviewsContext::class ),
			$this->createNoOpMock( ReferencePreviewsGadgetsIntegration::class )
		) )
			->onResourceLoaderRegisterModules( $resourceLoader );
	}

	/**
	 * @dataProvider provideBooleans
	 */
	public function testResourceLoaderRegistration_VisualAndWikiEditor( bool $loaded ) {
		$extensionRegistry = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistry->method( 'isLoaded' )->willReturnCallback(
			static function ( string $name ) use ( $loaded ) {
				if ( $name === 'VisualEditor' || $name === 'WikiEditor' ) {
					return $loaded;
				}
				return false;
			} );

		$rlModules = [];

		$resourceLoader = $this->createNoOpMock( ResourceLoader::class, [ 'register' ] );
		$resourceLoader->expects( $this->exactly( $loaded ? 2 : 0 ) )
			->method( 'register' )
			->willReturnCallback( static function ( array $modules ) use ( &$rlModules ) {
				$rlModules += $modules;
			} );

		( new CiteHooks(
			$extensionRegistry,
			new StaticUserOptionsLookup( [] )
		) )
			->onResourceLoaderRegisterModules( $resourceLoader );

		if ( $loaded ) {
			$this->assertArrayHasKey( 'ext.cite.wikiEditor', $rlModules );
			$this->assertArrayHasKey( 'ext.cite.visualEditor', $rlModules );
		} else {
			$this->assertSame( [], $rlModules );
		}
	}

	/**
	 * @dataProvider provideBooleans
	 */
	public function testResourceLoaderRegistration_CommunityConfiguration( bool $loaded ) {
		$extensionRegistry = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistry->method( 'isLoaded' )->willReturnCallback(
			static function ( string $name ) use ( $loaded ) {
				return $name === 'CommunityConfiguration' && $loaded;
			} );

		$rlModules = [];

		$resourceLoader = $this->createNoOpMock( ResourceLoader::class, [ 'register', 'getConfig' ] );
		$resourceLoader->expects( $this->exactly( $loaded ? 1 : 0 ) )
			->method( 'register' )
			->willReturnCallback( static function ( array $modules ) use ( &$rlModules ) {
				$rlModules += $modules;
			} );
		$resourceLoader->expects( $this->exactly( $loaded ? 1 : 0 ) )
			->method( 'getConfig' )
			->willReturn( new HashConfig( [
				'CiteBacklinkCommunityConfiguration' => $loaded
			] ) );

		( new CiteHooks(
			$extensionRegistry,
			new StaticUserOptionsLookup( [] )
		) )
			->onResourceLoaderRegisterModules( $resourceLoader );

		if ( $loaded ) {
			$this->assertArrayHasKey( 'ext.cite.community-configuration', $rlModules );
		} else {
			$this->assertSame( [], $rlModules );
		}
	}

	public static function provideBooleans() {
		yield [ true ];
		yield [ false ];
	}

	public function testOnGetPreferences_noConflicts() {
		$extensionRegistry = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistry->method( 'isLoaded' )->willReturn( true );

		$expected = [
			'popups-reference-previews' => [
				'type' => 'toggle',
				'label-message' => 'cite-reference-previews-preference-label',
				'help-message' => 'popups-prefs-conflicting-gadgets-info',
				'section' => 'rendering/reading'
			]
		];
		$gadgetsIntegrationMock = $this->createMock( ReferencePreviewsGadgetsIntegration::class );
		$prefs = [];
		( new ReferencePreviewsHooks(
			$extensionRegistry,
			$this->createNoOpMock( ReferencePreviewsContext::class ),
			$gadgetsIntegrationMock,
		) )
			->onGetPreferences( $this->createNoOpMock( User::class ), $prefs );
		$this->assertEquals( $expected, $prefs );
	}

	public function testOnGetPreferences_conflictingGadget() {
		$extensionRegistry = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistry->method( 'isLoaded' )->willReturn( true );

		$expected = [
			'popups-reference-previews' => [
				'type' => 'toggle',
				'label-message' => 'cite-reference-previews-preference-label',
				'help-message' => [
					'cite-reference-previews-gadget-conflict-info-navpopups',
					'Special:Preferences#mw-prefsection-gadgets',
				],
				'section' => 'rendering/reading',
				'disabled' => true
			]
		];
		$gadgetsIntegrationMock = $this->createMock( ReferencePreviewsGadgetsIntegration::class );
		$gadgetsIntegrationMock->expects( $this->once() )
			->method( 'isNavPopupsGadgetEnabled' )
			->willReturn( true );
		$prefs = [];
		( new ReferencePreviewsHooks(
			$extensionRegistry,
			$this->createNoOpMock( ReferencePreviewsContext::class ),
			$gadgetsIntegrationMock,
		) )
			->onGetPreferences( $this->createNoOpMock( User::class ), $prefs );
		$this->assertEquals( $expected, $prefs );
	}

	public function testOnGetPreferences_redundantPreference() {
		$extensionRegistry = $this->createNoOpMock( ExtensionRegistry::class, [ 'isLoaded' ] );
		$extensionRegistry->method( 'isLoaded' )->willReturn( true );

		$prefs = [
			'popups-reference-previews' => [
				'type' => 'toggle',
				'label-message' => 'from-another-extension',
			]
		];
		$expected = $prefs;
		( new ReferencePreviewsHooks(
			$extensionRegistry,
			$this->createNoOpMock( ReferencePreviewsContext::class ),
			$this->createMock( ReferencePreviewsGadgetsIntegration::class )
		) )
			->onGetPreferences( $this->createNoOpMock( User::class ), $prefs );
		$this->assertEquals( $expected, $prefs );
	}

}
