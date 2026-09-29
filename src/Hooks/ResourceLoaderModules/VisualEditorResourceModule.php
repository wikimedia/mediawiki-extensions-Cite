<?php
namespace Cite\Hooks\ResourceLoaderModules;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\Context;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * @license GPL-2.0-or-later
 */
class VisualEditorResourceModule {
	public function __construct(
		private readonly ExtensionRegistry $extensionRegistry
	) {
	}

	public function loadModule( ResourceLoader $rl ): void {
		if ( !$this->extensionRegistry->isLoaded( 'VisualEditor' ) ) {
			return;
		}

		$veConfig = [
			'ext.cite.visualEditor' => [
				'localBasePath' => dirname( __DIR__, 3 ) . '/modules/ve-cite',
				'remoteExtPath' => 'Cite/modules/ve-cite',
				'packageFiles' => [
					'init.js',
					've.dm.MWDataTransitionHelper.js',
					've.dm.MWDocumentReferences.js',
					've.dm.MWGroupReferences.js',
					've.dm.MWReferenceModel.js',
					've.dm.MWReferenceKeyGenerator.js',
					've.dm.MWReferencesListNode.js',
					've.dm.MWReferenceNode.js',
					've.ce.MWReferencesListNode.js',
					've.ce.MWReferenceNode.js',
					[
						'name' => 've.ui.MWCitationTools.json',
						'callback' => 'Cite\\ResourceLoader\\MWCitationToolsDefinition::getTools',
					],
					've.ui.MWReferenceGroupInputWidget.js',
					've.ui.MWReferenceSearchWidget.js',
					've.ui.MWReferenceResultWidget.js',
					've.ui.MWEditReferenceNodeAction.js',
					've.ui.MWUseExistingReferenceCommand.js',
					've.ui.MWCitationDialog.js',
					've.ui.MWReferencesListCommand.js',
					've.ui.MWReferencesListDialog.js',
					've.ui.MWReferenceDialog.js',
					've.ui.MWReferenceDialogTool.js',
					've.ui.MWSubReferenceHelpDialog.js',
					've.ui.MWSubReferenceHelpDialogOptions.js',
					've.ui.MWUseExistingReferenceDialogTool.js',
					've.ui.MWReferencesListDialogTool.js',
					've.ui.MWReferenceEditPanel.js',
					've.ui.MWCitationDialogTool.js',
					've.ui.MWReferenceContextItem.js',
					've.ui.MWReferencesListContextItem.js',
					've.ui.MWCitationContextItem.js',
					've.ui.MWCitationAction.js',
					've.ui.MWReference.init.js',
					've.ui.MWCitationTools.init.js',
					've.ui.MWCitationNeededContextItem.js',
					[
						'name' => 've.ui.contentLanguage.json',
						'callback' => 'Cite\\ResourceLoader\\ContentLanguage::getJsData'
					],
					[
						'name' => 've.ui.citeAutonameTemplates.json',
						'callback' => 'Cite\\ResourceLoader\\MWCitationAutonameTemplateMap::getAutonameTemplateMap'
					],
					[
						'name' => 've.ui.referenceNameMessages.json',
						'callback' => self::class . '::getReferenceNameMessages'
					],
					[
						'name' => 'icons.json',
						'callback' => 'MediaWiki\\ResourceLoader\\CodexModule::getIcons',
						'callbackParam' => [
							'cdxIconNewLine'
						]
					],
				],
				'styles' => [
					've.ce.MWReferenceNode.less',
					've.ce.MWReferencesListNode.less',
					've.ui.MWReferenceDialog.less',
					've.ui.MWReferenceContextItem.less',
					've.ui.MWReferenceResultWidget.less',
					've.ui.MWCitationDialogTool.less',
				],
				'dependencies' => [
					'oojs-ui.styles.icons-alerts',
					'oojs-ui.styles.icons-editing-citation',
					'oojs-ui.styles.icons-interactions',
					'oojs-ui.styles.icons-location',
					'ext.visualEditor.mwcore',
					'ext.cite.parsoid.styles',
					'ext.cite.styles',
					'ext.visualEditor.mwtransclusion',
					'ext.visualEditor.base',
					'ext.visualEditor.mediawiki',
					'mediawiki.jqueryMsg',
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
					'cite-ve-dialog-reference-editing-edit-details',
					'cite-ve-dialog-reference-editing-add-details-placeholder',
					'cite-ve-dialog-reference-editing-reused-short',
					'cite-ve-dialog-reference-editing-reused',
					'cite-ve-dialog-reference-editing-reused-long',
					'cite-ve-dialog-reference-editing-details-placeholder',
					'cite-ve-dialog-reference-missing-parent-ref',
					'cite-ve-dialog-reference-options-group-label',
					'cite-ve-dialog-reference-options-group-placeholder',
					'cite-ve-dialog-reference-options-responsive-label',
					'cite-ve-dialog-reference-options-section',
					'cite-ve-dialog-reference-placeholder',
					'cite-ve-dialog-reference-title',
					'cite-ve-dialog-reference-add-details-button',
					'cite-ve-dialog-reference-title-details',
					'cite-ve-dialog-subreference-help-dialog-title',
					'cite-ve-dialog-subreference-help-dialog-head',
					'cite-ve-dialog-subreference-help-dialog-content',
					'cite-ve-dialog-subreference-help-dialog-link',
					'cite-ve-dialog-subreference-help-dialog-link-ve',
					'cite-ve-dialog-subreference-help-dialog-link-label',
					'cite-ve-dialog-subreference-change-all-checkbox-label',
					'cite-ve-dialog-reference-convert-all-checkbox-label',
					'cite-ve-dialog-reference-useexisting-tool',
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
					'cite-ve-referenceslist-isempty',
					'cite-ve-referenceslist-isempty-default',
					'cite-ve-referenceslist-missing-parent',
					'cite-ve-referenceslist-missingref',
					'cite-ve-referenceslist-missingref-in-list',
					'cite-ve-referenceslist-missingreflist',
					'cite-ve-toolbar-group-label',
					'cite-ve-othergroup-item',
					'parentheses',
					'word-separator',
				],
			],
		];

		$autonameMsg = wfMessage( 'cite-ve-dialogbutton-reference-title-autoname' );
		if ( $autonameMsg->exists() ) {
			$veConfig[ 'ext.cite.visualEditor' ][ 'messages' ][] = 'cite-ve-dialogbutton-reference-title-autoname';
		}

		if ( $this->extensionRegistry->isLoaded( 'TestKitchen' ) ) {
			$veConfig[ 'ext.cite.visualEditor' ][ 'dependencies' ][] = 'ext.testKitchen';
		}

		$rl->register( $veConfig );
	}

	/**
	 * Ensure default reference autoname is always in content language
	 *
	 * @param Context $context
	 * @return array
	 */
	public static function getReferenceNameMessages( Context $context ): array {
		$autonameOverride = $context->msg( 'cite-ve-dialogbutton-reference-title-autoname' )->inContentLanguage();
		if ( $autonameOverride->exists() ) {
			return [ 'referenceAutonamePrefix' => $autonameOverride->text() ];
		}
		$autoname = $context->msg( 'cite-ve-dialogbutton-reference-title' )->inContentLanguage();
		return [
			'referenceAutonamePrefix' => $autoname->exists() ? $autoname->text() . '-' : ''
		];
	}
}
