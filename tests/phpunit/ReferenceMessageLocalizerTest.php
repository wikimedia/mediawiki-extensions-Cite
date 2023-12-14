<?php

namespace Cite\Tests;

use Cite\ReferenceMessageLocalizer;
use LanguageQqx;

/**
 * @covers \Cite\ReferenceMessageLocalizer
 */
class ReferenceMessageLocalizerTest extends \MediaWikiIntegrationTestCase {

	public function testMsg() {
		$localizer = new ReferenceMessageLocalizer( new LanguageQqx() );
		$this->assertSame(
			'(cite_reference_link_prefix)',
			$localizer->msg( 'cite_reference_link_prefix' )->plain() );
	}

}
