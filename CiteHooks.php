<?php
/**
 * Cite extension hooks
 *
 * @file
 * @ingroup Extensions
 * @copyright 2011-2016 Cite VisualEditor Team and others; see AUTHORS.txt
 * @license The MIT License (MIT); see MIT-LICENSE.txt
 */

class CiteHooks {
	/**
	 * Convert the content model of a message that is actually JSON to JSON. This
	 * only affects validation and UI when saving and editing, not loading the
	 * content.
	 *
	 * @param Title $title
	 * @param string $model
	 * @return bool
	 */
	public static function onContentHandlerDefaultModelFor( Title $title, &$model ) {
		if (
			$title->inNamespace( NS_MEDIAWIKI ) &&
			$title->getText() == 'Visualeditor-cite-tool-definition.json'
		) {
			$model = CONTENT_MODEL_JSON;
		}

		return true;
	}

	/**
	 * Conditionally register the unit testing module for the ext.cite.visualEditor module
	 * only if that module is loaded
	 *
	 * @param array $testModules The array of registered test modules
	 * @param ResourceLoader $resourceLoader The reference to the resource loader
	 * @return true
	 */
	public static function onResourceLoaderTestModules(
		array &$testModules,
		ResourceLoader &$resourceLoader
	) {
		$resourceModules = $resourceLoader->getConfig()->get( 'ResourceModules' );

		if (
			isset( $resourceModules[ 'ext.cite.visualEditor' ] ) ||
			$resourceLoader->isModuleRegistered( 'ext.cite.visualEditor' )
		) {
			$testModules['qunit']['ext.cite.visualEditor.test'] = array(
				'scripts' => array(
					'modules/ve-cite/tests/ve.dm.citeExample.js',
					'modules/ve-cite/tests/ve.dm.Converter.test.js',
					'modules/ve-cite/tests/ve.dm.InternalList.test.js',
					'modules/ve-cite/tests/ve.dm.Transaction.test.js',
				),
				'dependencies' => array(
					'ext.cite.visualEditor',
					'ext.visualEditor.test'
				),
				'localBasePath' => __DIR__,
				'remoteExtPath' => 'Cite',
			);
		}

		return true;
	}
}
