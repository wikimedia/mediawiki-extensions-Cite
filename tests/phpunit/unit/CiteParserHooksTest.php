<?php

namespace Cite\Test\Unit;

use Cite;
use CiteParserHooks;
use Parser;

/**
 * @coversDefaultClass \CiteParserHooks
 */
class CiteParserHooksTest extends \MediaWikiUnitTestCase {

	/**
	 * @covers ::onParserFirstCallInit
	 */
	public function testOnParserFirstCallInit() {
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->exactly( 2 ) )
			->method( 'setHook' )
			->withConsecutive(
				[ 'ref', $this->isType( 'callable' ) ],
				[ 'references', $this->isType( 'callable' ) ]
			);

		CiteParserHooks::onParserFirstCallInit( $parser );

		$this->assertInstanceOf( Cite::class, $parser->extCite );
	}

}
