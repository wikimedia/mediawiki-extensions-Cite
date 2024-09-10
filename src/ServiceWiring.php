<?php

use Cite\ReferencePreviews\ReferencePreviewsContext;
use MediaWiki\MediaWikiServices;

/**
 * @codeCoverageIgnore
 */
return [
	'Cite.ReferencePreviewsContext' => static function ( MediaWikiServices $services ): ReferencePreviewsContext {
		return new ReferencePreviewsContext(
			$services->getMainConfig(),
			$services->getUserOptionsLookup()
		);
	},
];
