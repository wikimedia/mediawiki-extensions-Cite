<?php

namespace Cite\Config;

use LogicException;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Extension\CommunityConfiguration\EditorCapabilities\AbstractEditorCapability;
use MediaWiki\Extension\CommunityConfiguration\Provider\ConfigurationProviderFactory;
use MediaWiki\Extension\CommunityConfiguration\Provider\IConfigurationProvider;
use MediaWiki\Html\Html;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Title\Title;

/**
 * @license GPL-2.0-or-later
 */
class CiteEditorCapability extends AbstractEditorCapability {

	private ConfigurationProviderFactory $providerFactory;
	private IConfigurationProvider $provider;
	private Config $config;
	private LinkRenderer $linkRenderer;

	public function __construct(
		IContextSource $ctx,
		Title $parentTitle,
		ConfigurationProviderFactory $providerFactory,
		Config $config,
		LinkRenderer $linkRenderer
	) {
		parent::__construct( $ctx, $parentTitle );

		$this->providerFactory = $providerFactory;
		$this->config = $config;
		$this->linkRenderer = $linkRenderer;
	}

	/**
	 * @inheritDoc
	 */
	public function execute( ?string $subpage ): void {
		if ( !$this->config->get( 'CiteBacklinkCommunityConfiguration' ) ) {
			throw new LogicException(
				__CLASS__ . ' should not be loaded when wgCiteBacklinkCommunityConfiguration is disabled.'
			);
		}

		if ( $subpage === null ) {
			throw new LogicException(
				__CLASS__ . ' does not support $subpage param being null'
			);
		}

		// header
		$out = $this->getContext()->getOutput();
		$out->setPageTitleMsg( $this->msg( 'cite-configuration-title' ) );
		$out->addSubtitle( '&lt; ' . $this->linkRenderer->makeLink(
				$this->getParentTitle()
			) );

		$out->addHTML( Html::rawElement(
			'div',
			[ 'class' => 'communityconfiguration-info-section' ],
			$this->msg( 'cite-configuration-info' )->parseAsBlock()
		) );

		// codex setup
		$out->addModules( 'ext.cite.community-configuration' );
		$out->addHTML(
			'<div id="ext-cite-configuration-vue-root" class="ext-cite-configuration-page" />'
		);

		// get the stored value and forward to JS
		$this->provider = $this->providerFactory->newProvider( $subpage );
		$citeProviderId = $this->provider->getId();

		$configStatusValue = $this->provider->loadValidConfiguration();
		$value = $configStatusValue->isOK() ? $configStatusValue->getValue() : null;

		$backlinkAlphabet = $value->Cite_Settings->backlinkAlphabet ?? '';

		$out->addJsConfigVars( [
			'wgCiteBacklinkAlphabet' => $backlinkAlphabet,
			'wgCiteProviderId' => $citeProviderId
		] );
	}
}
