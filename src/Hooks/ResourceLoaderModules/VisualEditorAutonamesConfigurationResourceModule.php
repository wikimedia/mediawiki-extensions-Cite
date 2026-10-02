<?php

namespace Cite\Hooks\ResourceLoaderModules;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * @license GPL-2.0-or-later
 */
class VisualEditorAutonamesConfigurationResourceModule {
	public function __construct(
		private readonly ExtensionRegistry $extensionRegistry
	) {
	}

	public function loadModule( ResourceLoader $rl ): void {
		if ( !$this->extensionRegistry->isLoaded( 'VisualEditor' ) ||
			!$this->extensionRegistry->isLoaded( 'CommunityConfiguration' ) ) {
			return;
		}

		if ( !$rl->getConfig()->get( 'CiteCitationTypeAutoNames' ) ) {
			return;
		}

		$rl->register( [
			'ext.cite.community-configuration-autoname' => [
				'localBasePath' => dirname( __DIR__, 3 ) . '/modules/community-configuration',
				'remoteExtPath' => 'Cite/modules/community-configuration',
				'messages' => [
					'cite-configuration-autoname-heading',
					'cite-configuration-autoname-checkbox-label',
					'cite-configuration-autoname-desc-intro-1',
					'cite-configuration-autoname-desc-intro-2',
				],
				'packageFiles' => [
					'init-autonames.js',
				],

			]
		] );
	}
}
