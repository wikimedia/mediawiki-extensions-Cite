<?php

declare( strict_types = 1 );

namespace Cite\Config\Schemas;

use MediaWiki\Extension\CommunityConfiguration\Schema\JsonSchema;

/**
 * @license GPL-2.0-or-later
 * phpcs:disable Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
 */
class AutoNamesSchema extends JsonSchema {
	public const VERSION = '1.0.0';

	public const array AutoNamesEnabled = [
		self::TYPE => self::TYPE_BOOLEAN,
		self::DEFAULT => false,
	];
}
