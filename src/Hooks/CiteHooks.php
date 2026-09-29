<?php
/**
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

namespace Cite\Hooks;

use Cite\Hooks\ResourceLoaderModules\CommunityConfigurationResourceModule;
use Cite\Hooks\ResourceLoaderModules\VisualEditorResourceModule;
use Cite\Hooks\ResourceLoaderModules\WikiEditorResourceModule;
use MediaWiki\Config\Config;
use MediaWiki\EditPage\EditPage;
use MediaWiki\Hook\EditPage__showEditForm_initialHook;
use MediaWiki\Output\OutputPage;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderGetConfigVarsHook;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderRegisterModulesHook;
use MediaWiki\ResourceLoader\ResourceLoader;
use MediaWiki\Revision\Hook\ContentHandlerDefaultModelForHook;
use MediaWiki\Title\Title;
use MediaWiki\User\Options\UserOptionsLookup;

/**
 * Hook handlers for Cite's integration with editors like VisualEditor and the WikiEditor extension
 * and the CommunityConfig extension.
 *
 * @license GPL-2.0-or-later
 */
class CiteHooks implements
	ContentHandlerDefaultModelForHook,
	ResourceLoaderGetConfigVarsHook,
	ResourceLoaderRegisterModulesHook,
	EditPage__showEditForm_initialHook
{

	public function __construct(
		private readonly ExtensionRegistry $extensionRegistry,
		private readonly UserOptionsLookup $userOptionsLookup,
	) {
	}

	/**
	 * Convert the content model of a message that is actually JSON to JSON. This
	 * only affects validation and UI when saving and editing, not loading the
	 * content.
	 *
	 * @param Title $title
	 * @param string &$model
	 * @return void
	 */
	public function onContentHandlerDefaultModelFor( $title, &$model ) {
		if (
			$title->inNamespace( NS_MEDIAWIKI ) &&
			$title->getText() == 'Cite-tool-definition.json'
		) {
			$model = CONTENT_MODEL_JSON;
		}
	}

	/**
	 * Adds extra variables to the global config
	 * @param array &$vars `[ variable name => value ]`
	 * @param string $skin
	 * @param Config $config
	 */
	public function onResourceLoaderGetConfigVars( array &$vars, $skin, Config $config ): void {
		$vars['wgCiteVisualEditorOtherGroup'] = (bool)$config->get( 'CiteVisualEditorOtherGroup' );
		$vars['wgCiteResponsiveReferences'] = (bool)$config->get( 'CiteResponsiveReferences' );
		$vars['wgCiteSubReferencing'] = (bool)$config->get( 'CiteSubReferencing' );
		$vars['wgCiteCitationTypeAutoNames'] = (bool)$config->get( 'CiteCitationTypeAutoNames' );
	}

	/**
	 * Hook: EditPage::showEditForm:initial
	 *
	 * Add the module for WikiEditor
	 *
	 * @param EditPage $editPage
	 * @param OutputPage $outputPage
	 * @return void
	 */
	public function onEditPage__showEditForm_initial( $editPage, $outputPage ) {
		if ( !$this->extensionRegistry->isLoaded( 'WikiEditor' ) ) {
			return;
		}

		// Wikitext is always allowed
		if ( $editPage->contentModel !== CONTENT_MODEL_WIKITEXT ) {
			// To support compatible namespaces from extensions like ProofreadPage, see T348403
			$wikitextContentModels = $this->extensionRegistry->getAttribute( 'CiteAllowedContentModels' );
			if ( !in_array( $editPage->contentModel, $wikitextContentModels ) ) {
				return;
			}
		}

		$user = $editPage->getContext()->getUser();
		if ( $this->userOptionsLookup->getBoolOption( $user, 'usebetatoolbar' ) ) {
			$outputPage->addModules( 'ext.cite.wikiEditor' );
		}
	}

	public function onResourceLoaderRegisterModules( ResourceLoader $rl ): void {
		( new VisualEditorResourceModule( $this->extensionRegistry ) )->loadModule( $rl );
		( new WikiEditorResourceModule( $this->extensionRegistry ) )->loadModule( $rl );
		( new CommunityConfigurationResourceModule( $this->extensionRegistry ) )->loadModule( $rl );
	}

}
