<?php

namespace Cite\Tests\Unit;

use MediaWiki\Parser\ParserOptions;

/**
 * @covers \Cite\CiteFactory
 * @license GPL-2.0-or-later
 */
class CiteFactoryIntegrationTest extends \MediaWikiIntegrationTestCase {
	public function testNewCite() {
		$serviceContainer = $this->getServiceContainer();
		$parser = $serviceContainer->getParserFactory()->create();
		// Cite::__construct access ParserOptions
		$parser->setOptions( ParserOptions::newFromAnon() );
		$this->assertNotNull( $serviceContainer->getService( 'Cite.CiteFactory' )->newCite( $parser ) );
	}
}
