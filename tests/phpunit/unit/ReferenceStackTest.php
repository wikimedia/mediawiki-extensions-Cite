<?php

namespace Cite\Tests\Unit;

use Cite\CiteErrorReporter;
use Cite\ReferenceStack;
use InvalidArgumentException;
use MediaWikiUnitTestCase;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\ReferenceStack
 */
class ReferenceStackTest extends MediaWikiUnitTestCase {

	/**
	 * @covers ::pushInvalidRef
	 */
	public function testPushInvalidRef() {
		$stack = $this->newStack();

		$stack->pushInvalidRef();

		$spy = TestingAccessWrapper::newFromObject( $stack );
		$this->assertSame( [ false ], $spy->refCallStack );
	}

	// TODO: testRollbackRefs()
	// TODO: testGetGroupRefs()

	/**
	 * @covers ::pushRef
	 *
	 * @dataProvider providePushRef
	 */
	public function testPushRefs(
		array $refs,
		$expectedOutputs,
		array $finalRefs,
		array $finalCallStack
	) {
		$mockStripState = $this->createMock( StripState::class );
		$mockStripState->method( 'unstripBoth' )->willReturnArgument( 0 );
		$stack = $this->newStack();

		for ( $i = 0; $i < count( $refs ); $i++ ) {
			[ $text, $name, $group, $follow, $argv, $dir ] = $refs[$i];
			if ( is_string( $expectedOutputs ) ) {
				$this->expectException( $expectedOutputs );
			}
			$result = $stack->pushRef(
				$text, $name, $group, $follow, $argv, $dir, $mockStripState );

			$this->assertSame( $expectedOutputs[$i], $result );
		}

		$spy = TestingAccessWrapper::newFromObject( $stack );
		$this->assertSame( $finalRefs, $spy->refs );
		$this->assertSame( $finalCallStack, $spy->refCallStack );
	}

	public function providePushRef() {
		return [
			'Anonymous ref in default group' => [
				[
					[ null, null, '', null, [], 'rtl' ]
				],
				[
					[ 1, null, 1, null ]
				],
				[
					'' => [
						[
							'count' => -1,
							'text' => null,
							'key' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], null, null, '', 1 ]
				]
			],
			'Anonymous ref in named group' => [
				[
					[ null, null, 'foo', null, [], 'rtl' ]
				],
				[
					[ 1, null, 1, null ]
				],
				[
					'foo' => [
						[
							'count' => -1,
							'text' => null,
							'key' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], null, null, 'foo', 1 ]
				]
			],
			'Ref with text' => [
				[
					[ 'text', null, 'foo', null, [], 'rtl' ]
				],
				[
					[ 1, null, 1, null ]
				],
				[
					'foo' => [
						[
							'count' => -1,
							'text' => 'text',
							'key' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text', null, 'foo', 1 ]
				]
			],
			'Named ref with text' => [
				[
					[ 'text', 'name', 'foo', null, [], 'rtl' ]
				],
				[
					[ 'name', '1-0', 1, '-1' ]
				],
				[
					'foo' => [
						'name' => [
							'text' => 'text',
							'count' => 0,
							'key' => 1,
							'number' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text', 'name', 'foo', 1 ]
				]
			],
			'Follow after base' => [
				[
					[ 'text-a', 'a', 'foo', null, [], 'rtl' ],
					[ 'text-b', 'b', 'foo', 'a', [], 'rtl' ]
				],
				[
					[ 'a', '1-0', 1, '-1' ],
					null
				],
				[
					'foo' => [
						'a' => [
							'text' => 'text-a text-b',
							'count' => 0,
							'key' => 1,
							'number' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text-a', 'a', 'foo', 1 ]
				]
			],
			'Follow with no base' => [
				[
					[ 'text', 'b', 'foo', 'a', [], 'rtl' ]
				],
				[
					null
				],
				[
					'foo' => [
						[
							'count' => -1,
							'text' => 'text',
							'key' => 1,
							'follow' => 'a',
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text', 'b', 'foo', 1 ]
				]
			],
			'Follow pointing to later ref' => [
				[
					[ 'text-a', 'a', 'foo', null, [], 'rtl' ],
					[ 'text-b', 'b', 'foo', 'c', [], 'rtl' ],
					[ 'text-c', 'c', 'foo', null, [], 'rtl' ]
				],
				[
					[ 'a', '1-0', 1, '-1' ],
					null,
					[ 'c', '3-0', 2, '-3' ],
				],
				[
					'foo' => [
						0 => [
							'count' => -1,
							'text' => 'text-b',
							'key' => 2,
							'follow' => 'c',
							'dir' => 'rtl',
						],
						'a' => [
							'text' => 'text-a',
							'count' => 0,
							'key' => 1,
							'number' => 1,
							'dir' => 'rtl',
						],
						'c' => [
							'text' => 'text-c',
							'count' => 0,
							'key' => 3,
							'number' => 2,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text-b', 'b', 'foo', 2 ],
					[ 'new', [], 'text-a', 'a', 'foo', 1 ],
					[ 'new', [], 'text-c', 'c', 'foo', 3 ]
				]
			],
			'Repeated ref, text in first tag' => [
				[
					[ 'text', 'a', 'foo', null, [], 'rtl' ],
					[ null, 'a', 'foo', null, [], 'rtl' ]
				],
				[
					[ 'a', '1-0', 1, '-1' ],
					[ 'a', '1-1', 1, '-1' ],
				],
				[
					'foo' => [
						'a' => [
							'text' => 'text',
							'count' => 1,
							'key' => 1,
							'number' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text', 'a', 'foo', 1 ],
					[ 'increment', [], null, 'a', 'foo', 1 ]
				]
			],
			'Repeated ref, text in second tag' => [
				[
					[ null, 'a', 'foo', null, [], 'rtl' ],
					[ 'text', 'a', 'foo', null, [], 'rtl' ]
				],
				[
					[ 'a', '1-0', 1, '-1' ],
					[ 'a', '1-1', 1, '-1' ],
				],
				[
					'foo' => [
						'a' => [
							'text' => 'text',
							'count' => 1,
							'key' => 1,
							'number' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], null, 'a', 'foo', 1 ],
					[ 'assign', [], 'text', 'a', 'foo', 1 ]
				]
			],
			'Repeated ref, mismatched text' => [
				[
					[ 'text-1', 'a', 'foo', null, [], 'rtl' ],
					[ 'text-2', 'a', 'foo', null, [], 'rtl' ]
				],
				[
					[ 'a', '1-0', 1, '-1' ],
					[ 'a', '1-1', 1, '-1' ],
				],
				[
					'foo' => [
						'a' => [
							'text' => 'text-1 cite_error_references_duplicate_key',
							'count' => 1,
							'key' => 1,
							'number' => 1,
							'dir' => 'rtl',
						]
					]
				],
				[
					[ 'new', [], 'text-1', 'a', 'foo', 1 ],
					[ 'increment', [], 'text-2', 'a', 'foo', 1 ]
				]
			],

			// FIXME: Split this off into a separate test method
			'Illegal value for name' => [
				[
					[ null, 123, '', null, [], 'rtl' ]
				],
				InvalidArgumentException::class,
				[],
				[]
			],
		];
	}

	/**
	 * @covers ::getGroups
	 */
	public function testGetGroups() {
		$stack = $this->newStack();
		$spy = TestingAccessWrapper::newFromObject( $stack );
		$spy->refs = [ 'havenot' => [], 'have' => [ [ 'ref etc' ] ] ];

		$this->assertSame( [ 'have' ], $stack->getGroups() );
	}

	/**
	 * @covers ::hasGroup
	 */
	public function testHasGroup() {
		$stack = $this->newStack();
		$spy = TestingAccessWrapper::newFromObject( $stack );
		$spy->refs = [ 'present' => [ [ 'ref etc' ] ] ];

		$this->assertFalse( $stack->hasGroup( 'absent' ) );
		$this->assertTrue( $stack->hasGroup( 'present' ) );
	}

	/**
	 * @covers ::setRefText
	 */
	public function testSetRefText() {
		$stack = $this->newStack();

		$stack->setRefText( 'group', 'name', 'the-text' );

		$spy = TestingAccessWrapper::newFromObject( $stack );
		$this->assertSame(
			[ 'group' => [ 'name' => [ 'text' => 'the-text' ] ] ], $spy->refs );
	}

	private function newStack() {
		$errorReporter = $this->createMock( CiteErrorReporter::class );
		$errorReporter->method( 'plain' )->willReturnArgument( 0 );
		return new ReferenceStack( $errorReporter );
	}

}
