<?php

declare( strict_types = 1 );

namespace Cite\Tests\Integration\Config;

use MediaWiki\Extension\CommunityConfiguration\Tests\SchemaProviderTestCase;

/**
 * @coversNothing
 * @license GPL-2.0-or-later
 */
class CiteSchemaProviderTest extends SchemaProviderTestCase {
	protected function setUp(): void {
		parent::setUp();

		$this->overrideConfigValue( 'CiteBacklinkCommunityConfiguration', true );
	}

	protected function getExtensionName(): string {
		return 'Cite';
	}

	protected function getProviderId(): string {
		return 'Cite';
	}

}
