<?php

namespace Cite\Tests;

use Cite\Cite;
use Cite\CiteErrorReporter;
use Cite\FootnoteBodyFormatter;
use Cite\ReferenceStack;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteTest extends \MediaWikiIntegrationTestCase {

	protected function setUp(): void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgCiteBookReferencing' => true,
		] );
	}

	/**
	 * @covers ::checkRefsNoReferences
	 * @dataProvider provideCheckRefsNoReferences
	 */
	public function testCheckRefsNoReferences(
		array $initialRefs, bool $isSectionPreview, string $expectedOutput
	) {
		$cite = new Cite();
		$spy = TestingAccessWrapper::newFromObject( $cite );
		$mockReferenceStack = $this->createMock( ReferenceStack::class );
		$mockReferenceStack->method( 'getGroups' )->willReturn( array_keys( $initialRefs ) );
		$mockReferenceStack->method( 'getGroupRefs' )->willReturnCallback( function ( $group ) use (
			$initialRefs
		) {
			return $initialRefs[$group];
		} );
		$spy->referenceStack = $mockReferenceStack;
		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'halfParsed' )->willReturnCallback(
			function ( ...$args ) {
				return json_encode( $args );
			}
		);
		/** @var CiteErrorReporter $mockErrorReporter */
		$spy->errorReporter = $mockErrorReporter;
		$mockFootnoteBodyFormatter = $this->createMock( FootnoteBodyFormatter::class );
		$mockFootnoteBodyFormatter->method( 'referencesFormat' )->willReturn( '<references />' );
		$spy->footnoteBodyFormatter = $mockFootnoteBodyFormatter;
		$spy->isSectionPreview = $isSectionPreview;

		$output = $cite->checkRefsNoReferences( $isSectionPreview );
		$this->assertSame( $expectedOutput, $output );
	}

	public function provideCheckRefsNoReferences() {
		return [
			'Default group' => [
				[
					'' => [
						[
							'name' => 'a',
						]
					]
				],
				false,
				'<references />'
			],
			'Default group in preview' => [
				[
					'' => [
						[
							'name' => 'a',
						]
					]
				],
				true,
				"\n" . '<div class="mw-ext-cite-cite_section_preview_references" >' .
					'<h2 id="mw-ext-cite-cite_section_preview_references_header" >' .
					'Preview of references</h2><references /></div>'
			],
			'Named group' => [
				[
					'foo' => [
						[
							'name' => 'a',
						]
					]
				],
				false,
				"\n" . '<br />["cite_error_group_refs_without_references","foo"]'
			],
			'Named group in preview' => [
				[
					'foo' => [
						[
							'name' => 'a',
						]
					]
				],
				true,
				"\n" . '<div class="mw-ext-cite-cite_section_preview_references" >' .
					'<h2 id="mw-ext-cite-cite_section_preview_references_header" >' .
					'Preview of references</h2><references /></div>'
			]
		];
	}
}
