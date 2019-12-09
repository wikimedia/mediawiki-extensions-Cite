<?php

namespace Cite\Tests;

use Cite\Cite;
use Cite\CiteErrorReporter;
use Cite\ReferencesFormatter;
use Cite\ReferenceStack;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteIntegrationTest extends \MediaWikiIntegrationTestCase {

	protected function setUp() : void {
		parent::setUp();

		$this->setMwGlobals( [
			'wgLanguageCode' => 'qqx',
		] );
	}

	/**
	 * @covers ::checkRefsNoReferences
	 * @dataProvider provideCheckRefsNoReferences
	 */
	public function testCheckRefsNoReferences(
		array $initialRefs, bool $isSectionPreview, string $expectedOutput
	) {
		global $wgCiteResponsiveReferences;
		$wgCiteResponsiveReferences = true;

		$mockReferenceStack = $this->createMock( ReferenceStack::class );
		$mockReferenceStack->method( 'getGroups' )->willReturn( array_keys( $initialRefs ) );
		$mockReferenceStack->method( 'getGroupRefs' )->willReturnCallback( function ( $group ) use (
			$initialRefs
		) {
			return $initialRefs[$group];
		} );

		$mockErrorReporter = $this->createMock( CiteErrorReporter::class );
		$mockErrorReporter->method( 'halfParsed' )->willReturnCallback(
			function ( ...$args ) {
				return '(' . implode( '|', $args ) . ')';
			}
		);

		$referencesFormatter = $this->createMock( ReferencesFormatter::class );
		$referencesFormatter->method( 'formatReferences' )->willReturn( '<references />' );

		$cite = new Cite();
		/** @var Cite $spy */
		$spy = TestingAccessWrapper::newFromObject( $cite );
		$spy->referenceStack = $mockReferenceStack;
		$spy->errorReporter = $mockErrorReporter;
		$spy->referencesFormatter = $referencesFormatter;
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
				'(cite_section_preview_references)</h2><references /></div>'
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
				"\n" . '<br />(cite_error_group_refs_without_references|foo)'
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
				'(cite_section_preview_references)</h2><references /></div>'
			]
		];
	}

}
