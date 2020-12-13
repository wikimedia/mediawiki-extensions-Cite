<?php
/**
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

namespace Cite\Hooks;

use ApiQuerySiteinfo;
use ExtensionRegistry;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\MediaWikiServices;
use ResourceLoader;
use Title;

class CiteHooks {

	/**
	 * Convert the content model of a message that is actually JSON to JSON. This
	 * only affects validation and UI when saving and editing, not loading the
	 * content.
	 *
	 * @param Title $title
	 * @param string &$model
	 */
	public static function onContentHandlerDefaultModelFor( LinkTarget $title, &$model ) {
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
	 * Conditionally register resource loader modules that depend on
	 * other MediaWiki extensions.
	 *
	 * @param ResourceLoader $resourceLoader
	 */
	public static function onResourceLoaderRegisterModules( ResourceLoader $resourceLoader ) {
		$uxEnhancementsModule = [
			'localBasePath' => __DIR__ . '/../../modules',
			'remoteExtPath' => 'Cite/modules',
			'scripts' => [
				'ext.cite.a11y.js',
				'ext.cite.highlighting.js',
			],
			'styles' => [
				'ext.cite.a11y.css',
				'ext.cite.highlighting.css',
			],
			'messages' => [
				'cite_reference_link_prefix',
				'cite_references_link_accessibility_label',
				'cite_references_link_many_accessibility_label',
				'cite_references_link_accessibility_back_label',
			],
		];
		if ( ExtensionRegistry::getInstance()->isLoaded( 'EventLogging' ) ) {
			// Temporary tracking for T231529
			$uxEnhancementsModule['scripts'][] = 'ext.cite.tracking.js';
			$uxEnhancementsModule['dependencies'][] = 'ext.eventLogging';
		}
		$resourceLoader->register( 'ext.cite.ux-enhancements', $uxEnhancementsModule );
	}

	/**
	 * Adds extra variables to the global config
	 * @param array &$vars
	 */
	public static function onResourceLoaderGetConfigVars( array &$vars ) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'cite' );
		$vars['wgCiteVisualEditorOtherGroup'] = $config->get( 'CiteVisualEditorOtherGroup' );
		$vars['wgCiteResponsiveReferences'] = $config->get( 'CiteResponsiveReferences' );
	}

	/**
	 * Hook: APIQuerySiteInfoGeneralInfo
	 *
	 * Expose configs via action=query&meta=siteinfo
	 *
	 * @param ApiQuerySiteinfo $api
	 * @param array &$data
	 */
	public static function onAPIQuerySiteInfoGeneralInfo( $api, array &$data ) {
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig( 'cite' );
		$data['citeresponsivereferences'] = $config->get( 'CiteResponsiveReferences' );
	}

}
