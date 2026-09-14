<?php
namespace Cite\ResourceLoader;

use MediaWiki\ResourceLoader as RL;

/**
 * Visual Editor Module providing a lookup map from autoname templates to
 * citation templates.
 */
class MWCitationAutonameTemplateMap {
	public static function getAutonameTemplateMap( RL\Context $context ): array {
		$templateMapJSON = $context->msg( 'cite-autoname-template-map.json' )
			->inContentLanguage()
			->plain();
		try {
			$templateMap = json_decode( $templateMapJSON, true, 512, JSON_THROW_ON_ERROR );
		} catch ( \JsonException ) {
			return [ 'citeAutonameTemplateMap' => [] ];
		}
		if ( !is_array( $templateMap ) ) {
			return [ 'citeAutonameTemplateMap' => [] ];
		}
		$newMap = [];
		foreach ( $templateMap as $autonameTemplate => $templateList ) {
			if ( !is_array( $templateList ) ) {
				continue;
			}
			foreach ( $templateList as $citeTemplate ) {
				if ( !is_string( $citeTemplate ) ) {
					continue;
				}
				if ( !isset( $newMap[$citeTemplate] ) ) {
					$newMap[$citeTemplate] = $autonameTemplate;
				}
			}
		}
		return [ 'citeAutonameTemplateMap' => $newMap ];
	}

}
