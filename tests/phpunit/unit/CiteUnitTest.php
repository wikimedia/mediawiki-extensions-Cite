<?php

namespace Cite\Tests\Unit;

use Cite\Cite;
use Cite\CiteErrorReporter;
use Cite\ReferenceStack;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\Cite
 *
 * @license GPL-2.0-or-later
 */
class CiteUnitTest extends \MediaWikiUnitTestCase {

	protected function setUp() : void {
		global $wgCiteBookReferencing, $wgFragmentMode;

		parent::setUp();
		$wgCiteBookReferencing = true;
		$wgFragmentMode = [ 'html5' ];
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
	 * @covers ::refArg
	 * @dataProvider provideRefAttributes
	 */
	public function testRefArg( array $attributes, array $expected ) {
		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$this->assertSame( $expected, $cite->refArg( $attributes ) );
	}

	public function provideRefAttributes() {
		// Note: Values are guaranteed to be trimmed by the parser, see
		// Sanitizer::decodeTagAttributes()
		return [
			[ [], [ null, null, null, null, null ] ],

			// One attribute only
			[ [ 'dir' => 'invalid' ], [ 'invalid', null, null, null, null ] ],
			[ [ 'dir' => 'rtl' ], [ 'rtl', null, null, null, null ] ],
			[ [ 'follow' => 'f' ], [ null, null, 'f', null, null ] ],
			[ [ 'group' => 'g' ], [ null, null, null, 'g', null ] ],
			[ [ 'invalid' => 'i' ], [ false, false, false, false, false ] ],
			[ [ 'invalid' => null ], [ false, false, false, false, false ] ],
			[ [ 'name' => 'n' ], [ null, null, null, null, 'n' ] ],
			[ [ 'name' => null ], [ false, false, false, false, false ] ],
			[ [ 'extends' => 'e' ], [ null, 'e', null, null, null ] ],

			// Pairs
			[ [ 'follow' => 'f', 'name' => 'n' ], [ null, null, 'f', null, 'n' ] ],
			[ [ 'follow' => null, 'name' => null ], [ false, false, false, false, false ] ],
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
	 * @covers ::normalizeKey
	 * @dataProvider provideKeyNormalizations
	 */
	public function testNormalizeKey( $key, $expected ) {
		/** @var Cite $cite */
		$cite = TestingAccessWrapper::newFromObject( new Cite() );
		$this->assertSame( $expected, $cite->normalizeKey( $key ) );
	}

	public function provideKeyNormalizations() {
		return [
			[ 'a b', 'a_b' ],
			[ 'a  __  b', 'a_b' ],
			[ ':', ':' ],
			[ "\t\n", '&#9;&#10;' ],
			[ "'", '&#039;' ],
			[ "''", '&#039;&#039;' ],
			[ '"%&/<>?[]{|}', '&quot;%&amp;/&lt;&gt;?&#91;&#93;&#123;&#124;&#125;' ],
			[ 'ISBN', '&#73;SBN' ],
		];
	}

}
