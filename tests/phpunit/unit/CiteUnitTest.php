<?php

namespace Cite\Tests\Unit;

use Cite\Cite;
use Cite\CiteErrorReporter;
use Cite\ReferenceStack;
use Parser;
use ParserOutput;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteUnitTest extends \MediaWikiUnitTestCase {

	protected function setUp() : void {
		global $wgCiteBookReferencing;

		parent::setUp();
		$wgCiteBookReferencing = true;
	}

	/**
	 * @covers ::validateRef
	 * @dataProvider provideValidations
	 */
	public function testValidateRef(
		array $referencesStack,
		?string $inReferencesGroup,
		bool $isSectionPreview,
		?string $text,
		?string $name,
		?string $group,
		?string $follow,
		?string $extends,
		$expected
	) {
		/** @var CiteErrorReporter $errorReporter */
		$errorReporter = $this->createMock( CiteErrorReporter::class );
		/** @var ReferenceStack $stack */
		$stack = TestingAccessWrapper::newFromObject( new ReferenceStack( $errorReporter ) );
		$stack->refs = $referencesStack;

		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$cite->referenceStack = $stack;
		$cite->inReferencesGroup = $inReferencesGroup;
		$cite->isSectionPreview = $isSectionPreview;

		$status = $cite->validateRef( $text, $name, $group, $follow, $extends );
		if ( is_string( $expected ) ) {
			$this->assertSame( $expected, $status->getErrors()[0]['message'] );
		} else {
			$this->assertSame( $expected, $status->isOK(), $status->getErrors()[0]['message'] ?? '' );
		}
	}

	public function provideValidations() {
		return [
			// Validating <ref> outside of <references>
			'text-only <ref>' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 't',
				'name' => null,
				'group' => null,
				'follow' => null,
				'extends' => null,
				'expected' => true,
			],
			'totally empty <ref>' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => null,
				'name' => null,
				'group' => null,
				'follow' => null,
				'extends' => null,
				'expected' => 'cite_error_ref_no_key',
			],
			// TODO: Add test cases that trigger all possible code paths

			// Validating a <ref> in <references>
			'most trivial <ref> in <references>' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => 'not empty',
				'name' => 'n',
				'group' => 'g',
				'follow' => null,
				'extends' => null,
				'expected' => true,
			],
			// TODO: Add test cases that trigger all possible code paths
		];
	}

	/**
	 * @covers ::parseArguments
	 * @dataProvider provideParseArguments
	 */
	public function testParseArguments(
		array $attributes,
		array $expectedValue,
		string $expectedError = null
	) {
		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$status = $cite->parseArguments(
			$attributes,
			[ 'dir', 'extends', 'follow', 'group', 'name' ]
		);
		$this->assertSame( $expectedValue, array_values( $status->getValue() ) );
		$this->assertSame( !$expectedError, $status->isOK() );
		if ( $expectedError ) {
			$this->assertSame( $expectedError, $status->getErrors()[0]['message'] );
		}
	}

	public function provideParseArguments() {
		// Note: Values are guaranteed to be trimmed by the parser, see
		// Sanitizer::decodeTagAttributes()
		return [
			[ [], [ null, null, null, null, null ] ],

			// One attribute only
			[ [ 'dir' => 'invalid' ], [ 'invalid', null, null, null, null ] ],
			[ [ 'dir' => 'rtl' ], [ 'rtl', null, null, null, null ] ],
			[ [ 'follow' => 'f' ], [ null, null, 'f', null, null ] ],
			[ [ 'group' => 'g' ], [ null, null, null, 'g', null ] ],
			[
				[ 'invalid' => 'i' ],
				[ null, null, null, null, null ],
				'cite_error_ref_too_many_keys'
			],
			[
				[ 'invalid' => null ],
				[ null, null, null, null, null ],
				'cite_error_ref_too_many_keys'
			],
			[ [ 'name' => 'n' ], [ null, null, null, null, 'n' ] ],
			[ [ 'name' => null ], [ null, null, null, null, null ] ],
			[ [ 'extends' => 'e' ], [ null, 'e', null, null, null ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ null, null, 'f', null, 'n' ] ],
			[ [ 'follow' => null, 'name' => null ], [ null, null, null, null, null ] ],
			[ [ 'follow' => 'f', 'extends' => 'e' ], [ null, 'e', 'f', null, null ] ],
			[ [ 'group' => 'g', 'name' => 'n' ], [ null, null, null, 'g', 'n' ] ],

			// Combinations of 3 or more attributes
			[
				[ 'group' => 'g', 'name' => 'n', 'extends' => 'e', 'dir' => 'rtl' ],
				[ 'rtl', 'e', null, 'g', 'n' ]
			],
		];
	}

	/**
	 * @covers ::guardedRef
	 */
	public function testGuardedRef_extendsProperty() {
		$mockOutput = $this->createMock( ParserOutput::class );
		// This will be our most important assertion.
		$mockOutput->expects( $this->once() )
			->method( 'setProperty' )
			->with( Cite::BOOK_REF_PROPERTY, true );

		$mockParser = $this->createMock( Parser::class );
		$mockParser->method( 'getOutput' )->willReturn( $mockOutput );
		$mockParser->method( 'getStripState' )
			->willReturn( $this->createMock( StripState::class ) );
		/** @var Parser $mockParser */

		$cite = new Cite();
		$spy = TestingAccessWrapper::newFromObject( $cite );
		$spy->referenceStack = $this->createMock( ReferenceStack::class );

		$spy->guardedRef( 'text', [ Cite::BOOK_REF_ATTRIBUTE => 'a' ], $mockParser );
	}

}
