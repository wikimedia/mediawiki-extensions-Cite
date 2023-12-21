<?php

namespace Cite\Tests\Integration;

use Cite\Cite;
use Cite\ErrorReporter;
use Cite\FootnoteMarkFormatter;
use Cite\ReferencesFormatter;
use Cite\ReferenceStack;
use Language;
use LogicException;
use Parser;
use ParserOptions;
use ParserOutput;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 * @license GPL-2.0-or-later
 */
class CiteTest extends \MediaWikiIntegrationTestCase {

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
		$cite = TestingAccessWrapper::newFromObject( $this->newCite() );
		$status = $cite->parseArguments(
			$attributes,
			[ 'dir', 'extends', 'follow', 'group', 'name' ]
		);
		$this->assertSame( $expectedValue, array_values( $status->getValue() ) );
		if ( $expectedError ) {
			$this->assertStatusError( $expectedError, $status );
		} else {
			$this->assertStatusGood( $status );
		}
	}

	public static function provideParseArguments() {
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
	 * @covers ::references
	 * @dataProvider provideGuardedReferences
	 */
	public function testGuardedReferences(
		?string $text,
		array $argv,
		int $expectedRollbackCount,
		string $expectedInReferencesGroup,
		bool $expectedResponsive,
		string $expectedOutput
	) {
		$this->overrideConfigValue( 'CiteResponsiveReferences', false );

		$parser = $this->createNoOpMock( Parser::class, [ 'recursiveTagParse' ] );

		$cite = $this->newCite();
		/** @var Cite $spy */
		$spy = TestingAccessWrapper::newFromObject( $cite );
		$spy->errorReporter = $this->createPartialMock( ErrorReporter::class, [ 'halfParsed' ] );
		$spy->errorReporter->method( 'halfParsed' )->willReturnCallback(
			static fn ( $parser, ...$args ) => '(' . implode( '|', $args ) . ')'
		);
		$spy->referencesFormatter = $this->createMock( ReferencesFormatter::class );
		$spy->referencesFormatter->method( 'formatReferences' )
			->with( $parser, [], $expectedResponsive, false )
			->willReturn( 'references!' );
		$spy->isSectionPreview = false;
		$spy->referenceStack = $this->createMock( ReferenceStack::class );
		$spy->referenceStack->method( 'popGroup' )
			->with( $expectedInReferencesGroup )->willReturn( [] );
		$spy->referenceStack->expects( $expectedRollbackCount ? $this->once() : $this->never() )
			->method( 'rollbackRefs' )
			->with( $expectedRollbackCount )
			->willReturn( [ [ 't', [] ] ] );

		$output = $cite->references( $parser, $text, $argv );
		$this->assertSame( $expectedOutput, $output );
	}

	public static function provideGuardedReferences() {
		return [
			'Bare references tag' => [
				null,
				[],
				0,
				'',
				false,
				'references!'
			],
			'References with group' => [
				null,
				[ 'group' => 'g' ],
				0,
				'g',
				false,
				'references!'
			],
			'Empty references tag' => [
				'',
				[],
				0,
				'',
				false,
				'references!'
			],
			'Set responsive' => [
				'',
				[ 'responsive' => '1' ],
				0,
				'',
				true,
				'references!'
			],
			'Unknown attribute' => [
				'',
				[ 'blargh' => '0' ],
				0,
				'',
				false,
				'(cite_error_references_invalid_parameters)',
			],
			'Contains refs (which are broken)' => [
				Parser::MARKER_PREFIX . '-ref- and ' . Parser::MARKER_PREFIX . '-notref-',
				[],
				1,
				'',
				false,
				"references!\n(cite_error_references_no_key)"
			],
		];
	}

	/**
	 * @covers ::guardedRef
	 * @dataProvider provideGuardedRef
	 */
	public function testGuardedRef(
		string $text,
		array $argv,
		?string $inReferencesGroup,
		array $initialRefs,
		string $expectOutput,
		array $expectedErrors,
		array $expectedRefs,
		bool $isSectionPreview = false
	) {
		$mockParser = $this->createNoOpMock( Parser::class, [ 'getStripState' ] );
		$mockParser->method( 'getStripState' )
			->willReturn( $this->createMock( StripState::class ) );

		$errorReporter = $this->createPartialMock( ErrorReporter::class, [ 'halfParsed', 'plain' ] );
		$errorReporter->method( $this->logicalOr( 'halfParsed', 'plain' ) )->willReturnCallback(
			static fn ( $parser, ...$args ) => '(' . implode( '|', $args ) . ')'
		);

		$referenceStack = new ReferenceStack();
		/** @var ReferenceStack $stackSpy */
		$stackSpy = TestingAccessWrapper::newFromObject( $referenceStack );
		$stackSpy->refs = $initialRefs;

		$mockFootnoteMarkFormatter = $this->createMock( FootnoteMarkFormatter::class );
		$mockFootnoteMarkFormatter->method( 'linkRef' )->willReturn( '<foot />' );

		$cite = $this->newCite( $isSectionPreview );
		/** @var Cite $spy */
		$spy = TestingAccessWrapper::newFromObject( $cite );
		$spy->errorReporter = $errorReporter;
		$spy->footnoteMarkFormatter = $mockFootnoteMarkFormatter;
		$spy->inReferencesGroup = $inReferencesGroup;
		$spy->referenceStack = $referenceStack;

		$result = $spy->guardedRef( $mockParser, $text, $argv );
		$this->assertSame( $expectOutput, $result );
		$this->assertSame( $expectedErrors, $spy->mReferencesErrors );
		$this->assertSame( $expectedRefs, $stackSpy->refs );
	}

	public static function provideGuardedRef() {
		return [
			'Whitespace text' => [
				'text' => ' ',
				'argv' => [ 'name' => 'a' ],
				'inReferencesGroup' => null,
				'initialRefs' => [],
				'expectedOutput' => '<foot />',
				'expectedErrors' => [],
				'expectedRefs' => [
					'' => [
						'a' => [
							'count' => 0,
							'dir' => null,
							'key' => 1,
							'name' => 'a',
							'text' => null,
							'number' => 1,
						],
					],
				]
			],
			'Empty in default references' => [
				'text' => '',
				'argv' => [],
				'inReferencesGroup' => '',
				'initialRefs' => [ '' => [] ],
				'expectedOutput' => '',
				'expectedErrors' => [ [ 'cite_error_references_no_key' ] ],
				'expectedRefs' => [ '' => [] ]
			],
			'Fallback to references group' => [
				'text' => 'text',
				'argv' => [ 'name' => 'a' ],
				'inReferencesGroup' => 'foo',
				'initialRefs' => [
					'foo' => [ 'a' => [] ],
				],
				'expectedOutput' => '',
				'expectedErrors' => [],
				'expectedRefs' => [
					'foo' => [
						'a' => [ 'text' => 'text' ],
					],
				]
			],
			'Successful ref' => [
				'text' => 'text',
				'argv' => [ 'name' => 'a' ],
				'inReferencesGroup' => null,
				'initialRefs' => [],
				'expectedOutput' => '<foot />',
				'expectedErrors' => [],
				'expectedRefs' => [
					'' => [
						'a' => [
							'count' => 0,
							'dir' => null,
							'key' => 1,
							'name' => 'a',
							'text' => 'text',
							'number' => 1,
						],
					],
				]
			],
			'Invalid ref' => [
				'text' => 'text',
				'argv' => [
					'name' => 'a',
					'badkey' => 'b',
				],
				'inReferencesGroup' => null,
				'initialRefs' => [],
				'expectedOutput' => '(cite_error_ref_too_many_keys)',
				'expectedErrors' => [],
				'expectedRefs' => []
			],
			'Successful references ref' => [
				'text' => 'text',
				'argv' => [ 'name' => 'a' ],
				'inReferencesGroup' => '',
				'initialRefs' => [
					'' => [
						'a' => []
					]
				],
				'expectedOutput' => '',
				'expectedErrors' => [],
				'expectedRefs' => [
					'' => [
						'a' => [ 'text' => 'text' ],
					],
				]
			],
			'T245376: Preview a list-defined ref that was never used' => [
				'text' => 'T245376',
				'argv' => [ 'name' => 'a' ],
				'inReferencesGroup' => '',
				'initialRefs' => [],
				'expectOutput' => '',
				'expectedErrors' => [],
				'expectedRefs' => [
					'' => [
						'a' => [ 'text' => 'T245376' ],
					],
				],
				'isSectionPreview' => true,
			],
			'Mismatched text in references' => [
				'text' => 'text-2',
				'argv' => [ 'name' => 'a' ],
				'inReferencesGroup' => '',
				'initialRefs' => [
					'' => [
						'a' => [ 'text' => 'text-1' ],
					]
				],
				'expectedOutput' => '',
				'expectedErrors' => [],
				'expectedRefs' => [
					'' => [
						'a' => [
							'text' => 'text-1',
							'warnings' => [ [ 'cite_error_references_duplicate_key', 'a' ] ],
						],
					],
				]
			],
		];
	}

	/**
	 * @covers ::guardedRef
	 */
	public function testGuardedRef_extendsProperty() {
		$this->overrideConfigValue( 'CiteBookReferencing', false );

		$mockOutput = $this->createMock( ParserOutput::class );
		// This will be our most important assertion.
		$mockOutput->expects( $this->once() )
			->method( 'setPageProperty' )
			->with( Cite::BOOK_REF_PROPERTY, '' );

		$mockParser = $this->createNoOpMock( Parser::class, [ 'getOutput' ] );
		$mockParser->method( 'getOutput' )->willReturn( $mockOutput );

		$cite = $this->newCite();
		/** @var Cite $spy */
		$spy = TestingAccessWrapper::newFromObject( $cite );
		$spy->errorReporter = $this->createMock( ErrorReporter::class );

		$spy->guardedRef( $mockParser, 'text', [ Cite::BOOK_REF_ATTRIBUTE => 'a' ] );
	}

	/**
	 * @coversNothing
	 */
	public function testReferencesSectionPreview() {
		$language = $this->createNoOpMock( Language::class );

		$parserOptions = $this->createMock( ParserOptions::class );
		$parserOptions->method( 'getIsSectionPreview' )->willReturn( true );

		$parser = $this->createNoOpMock( Parser::class, [ 'getOptions', 'getContentLanguage' ] );
		$parser->method( 'getOptions' )->willReturn( $parserOptions );
		$parser->method( 'getContentLanguage' )->willReturn( $language );

		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite( $parser ) );
		// Assume the currently parsed <ref> is wrapped in <references>
		$cite->inReferencesGroup = '';

		$html = $cite->guardedRef( $parser, 'a', [ 'name' => 'a' ] );
		$this->assertSame( '', $html );
	}

	/**
	 * @covers ::__clone
	 * @covers ::__construct
	 */
	public function testClone() {
		$cite = $this->newCite();

		$this->expectException( LogicException::class );
		clone $cite;
	}

	private function newCite( bool $isSectionPreview = false ): Cite {
		$language = $this->createNoOpMock( Language::class, [ '__debugInfo' ] );

		$mockOptions = $this->createMock( ParserOptions::class );
		$mockOptions->method( 'getIsSectionPreview' )->willReturn( $isSectionPreview );

		$mockParser = $this->createNoOpMock( Parser::class, [ 'getOptions', 'getContentLanguage' ] );
		$mockParser->method( 'getOptions' )->willReturn( $mockOptions );
		$mockParser->method( 'getContentLanguage' )->willReturn( $language );
		return new Cite( $mockParser );
	}

}
