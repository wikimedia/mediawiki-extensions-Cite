<?php

namespace Cite\Tests;

use CiteErrorReporter;
use Language;
use MediaWiki\MediaWikiServices;
use Parser;

/**
 * @covers \CiteErrorReporter
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
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->once() )
			->method( 'addTrackingCategory' );
		$parser->expects( $this->once() )
			->method( 'recursiveTagParse' )
			->willReturnArgument( 0 );

		$reporter = new CiteErrorReporter( $this->language, $parser );
		$html = $reporter->html( 'cite_error_example', 'first param' );
		$this->assertSame(
			'<span class="error mw-ext-cite-error" lang="qqx" '
				. 'dir="ltr">(cite_error: (cite_error_example: first param))</span>',
			$html
		);
	}

	public function testWikitextWarning() {
		$parser = $this->createMock( Parser::class );
		$parser->expects( $this->never() )
			->method( 'addTrackingCategory' );
		$parser->expects( $this->never() )
			->method( 'recursiveTagParse' );

		$reporter = new CiteErrorReporter( $this->language, $parser );
		$wikitext = $reporter->wikitext( 'cite_warning_example', 'first param' );
		$this->assertSame(
			'<span class="warning mw-ext-cite-warning mw-ext-cite-warning-example" lang="qqx" '
				. 'dir="ltr">(cite_warning: (cite_warning_example: first param))</span>',
			$wikitext
		);
	}

}
