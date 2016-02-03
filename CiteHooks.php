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
}
