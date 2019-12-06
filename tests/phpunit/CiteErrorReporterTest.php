<?php

namespace Cite\Tests;

use Cite\CiteErrorReporter;
use Language;
use MediaWiki\MediaWikiServices;
use Parser;
use ParserOptions;

/**
 * @covers \Cite\CiteErrorReporter
 *
 * @license GPL-2.0-or-later
 */
class CiteErrorReporterTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @var Language
	 */
	private $language;

	protected function setUp() : void {
		parent::setUp();

		$this->language = MediaWikiServices::getInstance()->getLanguageFactory()
			->getLanguage( 'qqx' );
	}

	public function testHtmlError() {
		$mockOptions = $this->createMock( ParserOptions::class );
		$mockOptions->method( 'getUserLangObj' )->willReturn( $this->language );
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->once() )
			->method( 'addTrackingCategory' );
		$parser->expects( $this->once() )
			->method( 'recursiveTagParse' )
			->willReturnArgument( 0 );
		$parser->method( 'getOptions' )->willReturn( $mockOptions );

		$reporter = new CiteErrorReporter( null, $parser );
		$html = $reporter->halfParsed( 'cite_error_example', 'first param' );
		$this->assertSame(
			'<span class="error mw-ext-cite-error" lang="qqx" '
				. 'dir="ltr">(cite_error: (cite_error_example: first param))</span>',
			$html
		);
	}

	public function testWikitextWarning() {
		$mockOptions = $this->createMock( ParserOptions::class );
		$mockOptions->method( 'getUserLangObj' )->willReturn( $this->language );
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->never() )
			->method( 'addTrackingCategory' );
		$parser->expects( $this->never() )
			->method( 'recursiveTagParse' );
		$parser->method( 'getOptions' )->willReturn( $mockOptions );

		$reporter = new CiteErrorReporter( null, $parser );
		$wikitext = $reporter->plain( 'cite_warning_example', 'first param' );
		$this->assertSame(
			'<span class="warning mw-ext-cite-warning mw-ext-cite-warning-example" lang="qqx" '
				. 'dir="ltr">(cite_warning: (cite_warning_example: first param))</span>',
			$wikitext
		);
	}

}
