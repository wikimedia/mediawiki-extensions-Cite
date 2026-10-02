<?php

namespace Cite\Config\Schemas;

use MediaWiki\Extension\CommunityConfiguration\Schema\JsonSchema;

/**
 * @license GPL-2.0-or-later
 * phpcs:disable Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
 */
class VisualEditorAutonamesSchema extends JsonSchema {
	public const VERSION = '1.0.0';

	public const array enable = [
		self::TYPE => self::TYPE_BOOLEAN,
		self::DEFAULT => false,
	];
}
