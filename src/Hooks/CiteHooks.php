<?php
/**
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

namespace Cite\Hooks;

use Cite\ReferencePreviews\ReferencePreviewsContext;
use Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration;
use MediaWiki\Api\ApiQuerySiteinfo;
use MediaWiki\Api\Hook\APIQuerySiteInfoGeneralInfoHook;
use MediaWiki\Config\Config;
use MediaWiki\EditPage\EditPage;
use MediaWiki\Hook\EditPage__showEditForm_initialHook;
use MediaWiki\Output\Hook\MakeGlobalVariablesScriptHook;
use MediaWiki\Output\OutputPage;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderGetConfigVarsHook;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderRegisterModulesHook;
use MediaWiki\ResourceLoader\ResourceLoader;
use MediaWiki\Revision\Hook\ContentHandlerDefaultModelForHook;
use MediaWiki\Title\Title;
use MediaWiki\User\Options\UserOptionsLookup;
use MediaWiki\User\User;

/**
 * @license GPL-2.0-or-later
 * @phpcs:disable MediaWiki.NamingConventions.LowerCamelFunctionsName.FunctionName
 */
class CiteHooks implements
	ContentHandlerDefaultModelForHook,
	MakeGlobalVariablesScriptHook,
	ResourceLoaderGetConfigVarsHook,
	ResourceLoaderRegisterModulesHook,
	APIQuerySiteInfoGeneralInfoHook,
	EditPage__showEditForm_initialHook
{

	private ReferencePreviewsContext $referencePreviewsContext;
	private ReferencePreviewsGadgetsIntegration $gadgetsIntegration;
	private UserOptionsLookup $userOptionsLookup;

	public function __construct(
		ReferencePreviewsContext $referencePreviewsContext,
		ReferencePreviewsGadgetsIntegration $gadgetsIntegration,
		UserOptionsLookup $userOptionsLookup
	) {
		$this->referencePreviewsContext = $referencePreviewsContext;
		$this->gadgetsIntegration = $gadgetsIntegration;
		$this->userOptionsLookup = $userOptionsLookup;
	}

	/**
	 * Convert the content model of a message that is actually JSON to JSON. This
	 * only affects validation and UI when saving and editing, not loading the
	 * content.
	 *
	 * @param Title $title
	 * @param string &$model
	 */
	public function onContentHandlerDefaultModelFor( $title, &$model ) {
		if (
			$title->inNamespace( NS_MEDIAWIKI ) &&
			(
				$title->getText() == 'Visualeditor-cite-tool-definition.json' ||
				$title->getText() == 'Cite-tool-definition.json'
			)
		) {
			$model = CONTENT_MODEL_JSON;
		}
	}

	/**
	 * @param array &$vars
	 * @param OutputPage $out
	 */
	public function onMakeGlobalVariablesScript( &$vars, $out ): void {
		$vars['wgCiteReferencePreviewsActive'] = $this->referencePreviewsContext->isReferencePreviewsEnabled(
			$out->getUser(),
			$out->getSkin()
		);
	}

	/**
	 * Adds extra variables to the global config
	 * @param array &$vars `[ variable name => value ]`
	 * @param string $skin
	 * @param Config $config
	 */
	public function onResourceLoaderGetConfigVars( array &$vars, $skin, Config $config ): void {
		$vars['wgCiteVisualEditorOtherGroup'] = $config->get( 'CiteVisualEditorOtherGroup' );
		$vars['wgCiteResponsiveReferences'] = $config->get( 'CiteResponsiveReferences' );
		$vars['wgCiteBookReferencing'] = $config->get( 'CiteBookReferencing' );
	}

	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ResourceLoaderRegisterModules
	 */
	public function onResourceLoaderRegisterModules( ResourceLoader $resourceLoader ): void {
		if ( ExtensionRegistry::getInstance()->isLoaded( 'VisualEditor' ) ) {
			$resourceLoader->register( [
				'ext.cite.visualEditor.core' => [
					'localBasePath' => dirname( __DIR__, 2 ) . '/modules/ve-cite',
					'remoteExtPath' => 'Cite/modules/ve-cite',
					'scripts' => [
						've.dm.MWDocumentReferences.js',
						've.dm.MWGroupReferences.js',
						've.dm.MWReferenceModel.js',
						've.dm.MWReferencesListNode.js',
						've.dm.MWReferenceNode.js',
						've.ce.MWReferencesListNode.js',
						've.ce.MWReferenceNode.js',
						've.ui.MWReferencesListCommand.js',
					],
					'styles' => [
						've.ce.MWReferencesListNode.less',
					],
					'dependencies' => [
						'ext.visualEditor.mwcore',
						'ext.visualEditor.mwtransclusion',
					],
					'messages' => [
						'cite-ve-referenceslist-isempty',
						'cite-ve-referenceslist-isempty-default',
						'cite-ve-referenceslist-missing-parent',
						'cite-ve-referenceslist-missingref',
						'cite-ve-referenceslist-missingref-in-list',
						'cite-ve-referenceslist-missingreflist',
					]
				],
				'ext.cite.visualEditor' => [
					'localBasePath' => dirname( __DIR__, 2 ) . '/modules/ve-cite',
					'remoteExtPath' => 'Cite/modules/ve-cite',
					'scripts' => [
						[
							'name' => 've.ui.MWCitationTools.js',
							'callback' => 'Cite\\ResourceLoader\\CitationToolDefinition::makeScript',
						],
						've.ui.MWReferenceGroupInputWidget.js',
						've.ui.MWReferenceSearchWidget.js',
						've.ui.MWReferenceResultWidget.js',
						've.ui.MWUseExistingReferenceCommand.js',
						've.ui.MWCitationDialog.js',
						've.ui.MWReferencesListDialog.js',
						've.ui.MWReferenceDialog.js',
						've.ui.MWReferenceDialogTool.js',
						've.ui.MWReferenceEditPanel.js',
						've.ui.MWCitationDialogTool.js',
						've.ui.MWReferenceContextItem.js',
						've.ui.MWReferencesListContextItem.js',
						've.ui.MWCitationContextItem.js',
						've.ui.MWCitationAction.js',
						've.ui.MWReference.init.js',
						've.ui.MWCitationNeededContextItem.js',
						[
							'name' => 've.ui.contentLanguage.js',
							'callback' => 'Cite\\ResourceLoader\\ContentLanguage::makeScript',
						],
					],
					'styles' => [
						've.ui.MWReferenceDialog.less',
						've.ui.MWReferenceContextItem.less',
						've.ui.MWReferenceResultWidget.less',
						've.ui.MWCitationDialogTool.less',
					],
					'dependencies' => [
						'oojs-ui.styles.icons-alerts',
						'oojs-ui.styles.icons-editing-citation',
						'oojs-ui.styles.icons-interactions',
						'ext.cite.visualEditor.core',
						'ext.cite.parsoid.styles',
						'ext.cite.styles',
						'ext.visualEditor.mwtransclusion',
						'ext.visualEditor.base',
						'ext.visualEditor.mediawiki',
					],
					'messages' => [
						'cite-ve-changedesc-ref-group-both',
						'cite-ve-changedesc-ref-group-from',
						'cite-ve-changedesc-ref-group-to',
						'cite-ve-changedesc-reflist-group-both',
						'cite-ve-changedesc-reflist-group-from',
						'cite-ve-changedesc-reflist-group-to',
						'cite-ve-changedesc-reflist-responsive-set',
						'cite-ve-changedesc-reflist-responsive-unset',
						'cite-ve-citationneeded-button',
						'cite-ve-citationneeded-description',
						'cite-ve-citationneeded-reason',
						'cite-ve-citationneeded-title',
						'cite-ve-dialog-reference-contextitem-extends',
						'cite-ve-dialog-reference-editing-add-details',
						'cite-ve-dialog-reference-editing-add-details-placeholder',
						'cite-ve-dialog-reference-editing-reused',
						'cite-ve-dialog-reference-editing-reused-long',
						'cite-ve-dialog-reference-editing-extends',
						'cite-ve-dialog-reference-extend-long-tool',
						'cite-ve-dialog-reference-missing-parent-ref',
						'cite-ve-dialog-reference-options-group-label',
						'cite-ve-dialog-reference-options-group-placeholder',
						'cite-ve-dialog-reference-options-responsive-label',
						'cite-ve-dialog-reference-options-section',
						'cite-ve-dialog-reference-placeholder',
						'cite-ve-dialog-reference-title',
						'cite-ve-dialog-reference-title-add-details',
						'cite-ve-dialog-reference-title-edit-details',
						'cite-ve-dialog-reference-useexisting-tool',
						'cite-ve-dialog-reference-useexisting-long-tool',
						'cite-ve-dialog-referenceslist-contextitem-description-general',
						'cite-ve-dialog-referenceslist-contextitem-description-named',
						'cite-ve-dialog-referenceslist-title',
						'cite-ve-dialogbutton-citation-educationpopup-title',
						'cite-ve-dialogbutton-citation-educationpopup-text',
						'cite-ve-dialogbutton-reference-full-label',
						'cite-ve-dialogbutton-reference-tooltip',
						'cite-ve-dialogbutton-reference-title',
						'cite-ve-dialogbutton-referenceslist-tooltip',
						'cite-ve-reference-input-placeholder',
						'cite-ve-toolbar-group-label',
						'cite-ve-othergroup-item',
						'parentheses',
						'word-separator',
					]
				],
			] );

		}

		if ( ExtensionRegistry::getInstance()->isLoaded( 'WikiEditor' ) ) {
			$resourceLoader->register( [
				'ext.cite.wikiEditor' => [
					'localBasePath' => dirname( __DIR__, 2 ) . '/modules',
					'remoteExtPath' => 'Cite/modules',
					'scripts' => [
						'ext.cite.wikiEditor.js',
					],
					'dependencies' => [
						'ext.wikiEditor',
						'mediawiki.jqueryMsg',
						'mediawiki.language',
					],
					'messages' => [
						'cite-wikieditor-tool-reference',
						'cite-wikieditor-help-page-references',
						'cite-wikieditor-help-content-reference-example-text1',
						'cite-wikieditor-help-content-reference-example-text2',
						'cite-wikieditor-help-content-reference-example-text3',
						'cite-wikieditor-help-content-reference-example-ref-id',
						'cite-wikieditor-help-content-reference-example-extra-details',
						'cite-wikieditor-help-content-reference-example-ref-normal',
						'cite-wikieditor-help-content-reference-example-ref-named',
						'cite-wikieditor-help-content-reference-example-ref-reuse',
						'cite-wikieditor-help-content-reference-example-ref-extends',
						'cite-wikieditor-help-content-reference-example-ref-result',
						'cite-wikieditor-help-content-reference-example-reflist',
						'cite-wikieditor-help-content-reference-description',
						'cite-wikieditor-help-content-named-reference-description',
						'cite-wikieditor-help-content-rereference-description',
						'cite-wikieditor-help-content-extended-reference-description',
						'cite-wikieditor-help-content-showreferences-description',
						'cite_reference_backlink_symbol',
					],
				],
			] );
		}

		if ( !$resourceLoader->getConfig()->get( 'CiteReferencePreviews' ) ||
			!ExtensionRegistry::getInstance()->isLoaded( 'Popups' )
		) {
			return;
		}

		$resourceLoader->register( [
			'ext.cite.referencePreviews' => [
				'localBasePath' => dirname( __DIR__, 2 ) . '/modules/ext.cite.referencePreviews',
				'remoteExtPath' => 'Cite/modules/ext.cite.referencePreviews',
				'dependencies' => [
					'ext.popups.main',
				],
				'styles' => [
					'referencePreview.less',
				],
				'messages' => [
					'cite-reference-previews-reference',
					'cite-reference-previews-book',
					'cite-reference-previews-journal',
					'cite-reference-previews-news',
					'cite-reference-previews-note',
					'cite-reference-previews-web',
					'cite-reference-previews-collapsible-placeholder',
				],
				'packageFiles' => [
					'index.js',
					'constants.js',
					'createReferenceGateway.js',
					'createReferencePreview.js',
					'isReferencePreviewsEnabled.js',
					'referencePreviewsInstrumentation.js'
				]
			]
		] );
	}

	/**
	 * Hook: APIQuerySiteInfoGeneralInfo
	 *
	 * Expose configs via action=query&meta=siteinfo
	 *
	 * @param ApiQuerySiteinfo $module
	 * @param array &$results
	 */
	public function onAPIQuerySiteInfoGeneralInfo( $module, &$results ) {
		$results['citeresponsivereferences'] = $module->getConfig()->get( 'CiteResponsiveReferences' );
	}

	/**
	 * Hook: EditPage::showEditForm:initial
	 *
	 * Add the module for WikiEditor
	 *
	 * @param EditPage $editPage the current EditPage object.
	 * @param OutputPage $outputPage object.
	 */
	public function onEditPage__showEditForm_initial( $editPage, $outputPage ) {
		$extensionRegistry = ExtensionRegistry::getInstance();
		$allowedContentModels = array_merge(
			[ CONTENT_MODEL_WIKITEXT ],
			$extensionRegistry->getAttribute( 'CiteAllowedContentModels' )
		);
		if ( !in_array( $editPage->contentModel, $allowedContentModels ) ) {
			return;
		}

		$user = $editPage->getContext()->getUser();

		if ( $extensionRegistry->isLoaded( 'WikiEditor' ) &&
			$this->userOptionsLookup->getBoolOption( $user, 'usebetatoolbar' )
		) {
			$outputPage->addModules( 'ext.cite.wikiEditor' );
		}
	}

	/**
	 * Add options to user Preferences page
	 *
	 * @param User $user User whose preferences are being modified
	 * @param array[] &$prefs Preferences description array, to be fed to a HTMLForm object
	 */
	public function onGetPreferences( $user, &$prefs ) {
		$option = [
			'type' => 'toggle',
			'label-message' => 'popups-refpreview-user-preference-label',
			// FIXME: This message is unnecessary and unactionable since we already
			// detect specific gadget conflicts.
			'help-message' => 'popups-prefs-conflicting-gadgets-info',
			// FIXME: copied from Popups
			'section' => 'rendering/reading',
		];
		$isNavPopupsGadgetEnabled = $this->gadgetsIntegration->isNavPopupsGadgetEnabled( $user );
		$isRefTooltipsGadgetEnabled = $this->gadgetsIntegration->isRefTooltipsGadgetEnabled( $user );
		if ( $isNavPopupsGadgetEnabled && $isRefTooltipsGadgetEnabled ) {
			$option[ 'disabled' ] = true;
			$option[ 'help-message' ] = [ 'popups-prefs-reftooltips-and-navpopups-gadget-conflict-info',
				'Special:Preferences#mw-prefsection-gadgets' ];
		} elseif ( $isNavPopupsGadgetEnabled ) {
			$option[ 'disabled' ] = true;
			$option[ 'help-message' ] = [ 'popups-prefs-navpopups-gadget-conflict-info',
				'Special:Preferences#mw-prefsection-gadgets' ];
		} elseif ( $isRefTooltipsGadgetEnabled ) {
			$option[ 'disabled' ] = true;
			$option[ 'help-message' ] = [ 'popups-prefs-reftooltips-gadget-conflict-info',
				'Special:Preferences#mw-prefsection-gadgets' ];
		}

		$prefs += [
			ReferencePreviewsContext::REFERENCE_PREVIEWS_PREFERENCE_NAME => $option
		];
	}

	/**
	 * See https://www.mediawiki.org/wiki/Manual:Hooks/UserGetDefaultOptions
	 * @param array &$defaultOptions Array of preference keys and their default values.
	 */
	public static function onUserGetDefaultOptions( &$defaultOptions ) {
		// FIXME: Move to extension.json once migration is complete.  See T363162
		if ( !isset( $defaultOptions[ ReferencePreviewsContext::REFERENCE_PREVIEWS_PREFERENCE_NAME ] ) ) {
			$defaultOptions[ ReferencePreviewsContext::REFERENCE_PREVIEWS_PREFERENCE_NAME ] = '1';
		}
	}

}
