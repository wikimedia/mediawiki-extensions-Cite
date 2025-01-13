<?php

use Cite\AlphabetsProvider;
use Cite\MarkSymbolRenderer;
use Cite\ReferenceMessageLocalizer;
use Cite\ReferencePreviews\ReferencePreviewsContext;
use Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration;
use MediaWiki\MediaWikiServices;
use MediaWiki\Registration\ExtensionRegistry;

/**
 * @codeCoverageIgnore
 */
return [
	'Cite.AlphabetsProvider' => static function (): AlphabetsProvider {
		return new AlphabetsProvider();
	},
	'Cite.GadgetsIntegration' => static function ( MediaWikiServices $services ): ReferencePreviewsGadgetsIntegration {
		return new ReferencePreviewsGadgetsIntegration(
			$services->getMainConfig(),
			ExtensionRegistry::getInstance()->isLoaded( 'Gadgets' ) ?
				$services->getService( 'GadgetsRepo' ) :
				null
		);
	},
	'Cite.MarkSymbolRenderer' => static function ( MediaWikiServices $services ): MarkSymbolRenderer {
		return new MarkSymbolRenderer(
			new ReferenceMessageLocalizer(
				$services->getContentLanguage()
			),
			$services->getService( 'Cite.AlphabetsProvider' ),
			$services->getMainConfig()
		);
	},
	'Cite.ReferencePreviewsContext' => static function ( MediaWikiServices $services ): ReferencePreviewsContext {
		return new ReferencePreviewsContext(
			$services->getMainConfig(),
			$services->getService( 'Cite.GadgetsIntegration' ),
			$services->getUserOptionsLookup()
		);
	},
];
