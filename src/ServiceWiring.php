<?php

use Cite\AlphabetsProvider;
use Cite\BacklinkMarkRenderer;
use Cite\MarkSymbolRenderer;
use Cite\ReferenceMessageLocalizer;
use Cite\ReferencePreviews\ReferencePreviewsContext;
use Cite\ReferencePreviews\ReferencePreviewsGadgetsIntegration;
use MediaWiki\Extension\CLDR\Alphabets;
use MediaWiki\MediaWikiServices;

/**
 * @codeCoverageIgnore
 * @phpcs-require-sorted-array
 */
return [

	'Cite.AlphabetsProvider' => static function ( MediaWikiServices $services ): AlphabetsProvider {
		return new AlphabetsProvider(
			$services->getExtensionRegistry()->isLoaded( 'CLDR' ) ?
				new Alphabets() : null
		);
	},

	'Cite.BacklinkMarkRenderer' => static function ( MediaWikiServices $services ): BacklinkMarkRenderer {
		$contentLanguage = $services->getContentLanguage();
		return new BacklinkMarkRenderer(
			$contentLanguage->getCode(),
			new ReferenceMessageLocalizer(
				$contentLanguage
			),
			$services->getService( 'Cite.AlphabetsProvider' ),
			$services->getExtensionRegistry()->isLoaded( 'CommunityConfiguration' ) ?
				$services->getService( 'CommunityConfiguration.ProviderFactory' ) : null,
			$services->getMainConfig()
		);
	},

	'Cite.GadgetsIntegration' => static function ( MediaWikiServices $services ): ReferencePreviewsGadgetsIntegration {
		return new ReferencePreviewsGadgetsIntegration(
			$services->getMainConfig(),
			$services->getExtensionRegistry()->isLoaded( 'Gadgets' ) ?
				$services->getService( 'GadgetsRepo' ) :
				null
		);
	},

	'Cite.MarkSymbolRenderer' => static function ( MediaWikiServices $services ): MarkSymbolRenderer {
		return new MarkSymbolRenderer(
			new ReferenceMessageLocalizer(
				$services->getContentLanguage()
			)
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
