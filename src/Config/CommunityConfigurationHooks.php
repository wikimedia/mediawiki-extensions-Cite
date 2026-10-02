<?php

namespace Cite\Config;

use MediaWiki\Config\Config;
use MediaWiki\Extension\CommunityConfiguration\Hooks\CommunityConfigurationProvider_initListHook;

/**
 * @license GPL-2.0-or-later
 */
class CommunityConfigurationHooks implements CommunityConfigurationProvider_initListHook {

	public function __construct(
		private readonly Config $config
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function onCommunityConfigurationProvider_initList( array &$providers ) {
		if ( !$this->config->get( 'CiteBacklinkCommunityConfiguration' ) ) {
			// Do not show the Cite provider in the dashboard when disabled
			unset( $providers['Cite'] );
		}

		if ( !$this->config->get( 'CiteCitationTypeAutoNames' ) ) {
				// Do not show the Autonames provider in the dashboard when disabled
				unset( $providers['Cite-VisualEditor-Autonames'] );
		}
	}

}
