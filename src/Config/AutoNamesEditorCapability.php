<?php

namespace Cite\Config;

use MediaWiki\Extension\CommunityConfiguration\EditorCapabilities\GenericFormEditorCapability;
use MediaWiki\Extension\CommunityConfiguration\Provider\IConfigurationProvider;

/**
 * @license GPL-2.0-or-later
 */
class AutoNamesEditorCapability extends GenericFormEditorCapability {

	/**
	 * @inheritDoc
	 */
	public function execute( ?IConfigurationProvider $provider, ?string $subpage = null ): void {
		$this->getContext()->getOutput()->addModules( 'ext.cite.community-configuration-autoname' );
		parent::execute( $provider, $subpage );
	}
}
