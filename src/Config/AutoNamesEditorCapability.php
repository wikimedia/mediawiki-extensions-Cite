<?php

namespace Cite\Config;

use LogicException;
use MediaWiki\Context\IContextSource;
use MediaWiki\Extension\CommunityConfiguration\EditorCapabilities\AbstractEditorCapability;
use MediaWiki\Extension\CommunityConfiguration\Provider\IConfigurationProvider;
use MediaWiki\Html\Html;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Title\Title;

/**
 * @license GPL-2.0-or-later
 */
class AutoNamesEditorCapability extends AbstractEditorCapability {

	public function __construct(
		IContextSource $ctx,
		Title $parentTitle,
		private readonly LinkRenderer $linkRenderer,
	) {
		parent::__construct( $ctx, $parentTitle );
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ?IConfigurationProvider $provider, ?string $subpage = null ): void {
		if ( $provider === null ) {
			throw new LogicException( __CLASS__ . ' does not support $provider being null' );
		}
		$out = $this->getContext()->getOutput();
		$out->setPageTitleMsg( $this->msg( 'cite-configuration-autoname-title' ) );
		$out->addSubtitle( '&lt; ' . $this->linkRenderer->makeLink( $this->getParentTitle() ) );
		$helpURL = $provider->getOptionValue( 'helpURL' );
		if ( $helpURL ) {
			$out->addHelpLink( $helpURL, true );
		}

		$configStatusValue = $provider->loadValidConfiguration();
		$value = $configStatusValue->isOK() ? $configStatusValue->getValue() : null;
		$enabled = $value->AutoNamesEnabled ?? false;

		$canEdit = $provider->getStore()->definitelyCanEdit( $this->getContext()->getAuthority() );

		$out->addJsConfigVars( [
			'wgCiteAutoNamesEnabled' => $enabled,
			'wgCiteAutoNamesCanEdit' => $canEdit,
			'wgCiteAutoNamesProviderId' => $provider->getId(),
		] );

		$out->addModules( 'ext.cite.community-configuration-autoname' );
		$out->addHTML( Html::element( 'div', [ 'id' => 'ext-cite-autoname-vue-root' ] ) );
		// $out->addHTML( $this->msg( 'cite-configuration-autoname-desc-templates' )->parseAsBlock() );
	}
}
