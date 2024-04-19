<?php

namespace Cite\ReferencePreviews;

use InvalidArgumentException;
use MediaWiki\Config\Config;
use MediaWiki\Extension\Gadgets\GadgetRepo;
use MediaWiki\MediaWikiServices;
use MediaWiki\User\User;
use MediaWiki\User\UserIdentity;
use Wikimedia\Services\NoSuchServiceException;

/**
 * Gadgets integration
 *
 * @package ReferencePreviews
 * @license GPL-2.0-or-later
 */
class ReferencePreviewsGadgetsIntegration {

	public const CONFIG_NAVIGATION_POPUPS_NAME = 'CiteReferencePreviewsConflictingNavPopupsGadgetName';

	public const CONFIG_REFERENCE_TOOLTIPS_NAME = 'CiteReferencePreviewsConflictingRefTooltipsGadgetName';

	private ?GadgetRepo $gadgetRepo;

	private string $navPopupsGadgetName;

	private string $refTooltipsGadgetName;

	public function __construct( Config $config ) {
		$this->navPopupsGadgetName = $this->sanitizeGadgetName(
			$config->get( self::CONFIG_NAVIGATION_POPUPS_NAME ) );
		$this->refTooltipsGadgetName = $this->sanitizeGadgetName(
			$config->get( self::CONFIG_REFERENCE_TOOLTIPS_NAME ) );

		try {
			$this->gadgetRepo = MediaWikiServices::getInstance()->getService( 'GadgetsRepo' );
		} catch ( NoSuchServiceException $e ) {
			$this->gadgetRepo = null;
		}
	}

	private function sanitizeGadgetName( string $gadgetName ): string {
		return str_replace( ' ', '_', trim( $gadgetName ) );
	}

	private function isGadgetEnabled( UserIdentity $user, string $gadgetName ): bool {
		if ( $this->gadgetRepo ) {
			if ( in_array( $gadgetName, $this->gadgetRepo->getGadgetIds() ) ) {
				try {
					return $this->gadgetRepo->getGadget( $gadgetName )
						->isEnabled( $user );
				} catch ( InvalidArgumentException $e ) {
					return false;
				}
			}
		}
		return false;
	}

	public function isNavPopupsGadgetEnabled( User $user ): bool {
		return $this->isGadgetEnabled( $user, $this->navPopupsGadgetName );
	}

	public function isRefTooltipsGadgetEnabled( User $user ): bool {
		return $this->isGadgetEnabled( $user, $this->refTooltipsGadgetName );
	}

}
