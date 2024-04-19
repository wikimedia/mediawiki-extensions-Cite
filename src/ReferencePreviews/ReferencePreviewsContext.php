<?php

namespace Cite\ReferencePreviews;

use MediaWiki\Config\Config;
use MediaWiki\User\Options\UserOptionsLookup;
use MediaWiki\User\User;
use Skin;

/**
 * @license GPL-2.0-or-later
 */
class ReferencePreviewsContext {

	private Config $config;

	private ReferencePreviewsGadgetsIntegration $gadgetsIntegration;

	private UserOptionsLookup $userOptionsLookup;

	public function __construct(
		Config $config,
		UserOptionsLookup $userOptionsLookup
	) {
		$this->gadgetsIntegration = new ReferencePreviewsGadgetsIntegration( $config );
		$this->userOptionsLookup = $userOptionsLookup;

		$this->config = $config;
	}

	/**
	 * User preference key to enable/disable Reference Previews. Named
	 * "mwe-popups-referencePreviews-enabled" in localStorage for anonymous users.
	 */
	public const REFERENCE_PREVIEWS_PREFERENCE_NAME = 'popups-reference-previews';

	public function isReferencePreviewsEnabled( User $user, Skin $skin ): bool {
		if (
			// T243822: Temporarily disabled in the mobile skin
			$skin->getSkinName() === 'minerva' ||
			!$this->config->get( 'CiteReferencePreviews' ) ||
			$this->gadgetsIntegration->isRefToolTipsGadgetEnabled( $user ) ||
			$this->gadgetsIntegration->isNavPopupsGadgetEnabled( $user )
		) {
			return false;
		}

		return !$user->isNamed() || $this->userOptionsLookup->getBoolOption(
				$user, self::REFERENCE_PREVIEWS_PREFERENCE_NAME
			);
	}
}
