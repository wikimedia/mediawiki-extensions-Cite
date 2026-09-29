<?php

namespace Cite\Hooks\ResourceLoaderModules;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * @license GPL-2.0-or-later
 */
class CommunityConfigurationResourceModule {
	public function __construct(
		private readonly ExtensionRegistry $extensionRegistry
	) {
	}

	public function loadModule( ResourceLoader $rl ): void {
		if ( !$this->extensionRegistry->isLoaded( 'CommunityConfiguration' ) ) {
			return;
		}

		if ( !$rl->getConfig()->get( 'CiteBacklinkCommunityConfiguration' ) ) {
			return;
		}

		$rl->register( [
			'ext.cite.community-configuration' => [
				'localBasePath' => dirname( __DIR__, 3 ) . '/modules/community-configuration',
				'remoteExtPath' => 'Cite/modules/community-configuration',
				'class' => 'MediaWiki\\ResourceLoader\\CodexModule',
				'dependencies' => [
					'vue'
				],
				'messages' => [
					'cite-configuration-title',
					'cite-configuration-submit',
					'cite-configuration-backlink-title',
					'cite-configuration-backlink-description',
					'cite-configuration-backlink-alpha-suggestion',
					'cite-configuration-backlink-marker-label',
					'cite-configuration-backlink-marker-description',
					'cite-configuration-backlink-marker-help'
				],
				'packageFiles' => [
					'init.js',
					'components/BacklinkSettings.vue',
					'components/CommunityConfiguration.vue'
				],
				'codexComponents' => [
					'CdxButton',
					'CdxField',
					'CdxTextInput',
					'CdxTextArea'
				]
			]
		] );
	}
}
