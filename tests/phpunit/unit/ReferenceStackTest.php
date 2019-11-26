<?php

namespace Cite\Tests\Unit;

use Cite\CiteErrorReporter;
use Cite\ReferenceStack;
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
		array $expectedOutputs,
		array $finalRefs,
		array $finalCallStack
	) {
		$mockStripState = $this->createMock( StripState::class );
		$mockStripState->method( 'unstripBoth' )->willReturnArgument( 0 );
		$stack = $this->newStack();

		for ( $i = 0; $i < count( $refs ); $i++ ) {
			[ $text, $name, $group, $follow, $argv, $dir ] = $refs[$i];
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
							'dir' => 'rtl',
							'key' => 1,
							'text' => null,
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
							'dir' => 'rtl',
							'key' => 1,
							'text' => null,
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
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text',
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
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text',
							'number' => 1,
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
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text-a text-b',
							'number' => 1,
						]
					]
				],
				[
					[ 'new', [], 'text-a', 'a', 'foo', 1 ]
				]
			],
			'Follow with no base' => [
				[
					[ 'text', null, 'foo', 'a', [], 'rtl' ]
				],
				[
					null
				],
				[
					'foo' => [
						[
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text',
							'follow' => 'a',
						]
					]
				],
				[
					[ 'new', [], 'text', null, 'foo', 1 ]
				]
			],
			'Follow pointing to later ref' => [
				[
					[ 'text-a', 'a', 'foo', null, [], 'rtl' ],
					[ 'text-b', null, 'foo', 'c', [], 'rtl' ],
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
							'dir' => 'rtl',
							'key' => 2,
							'text' => 'text-b',
							'follow' => 'c',
						],
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text-a',
							'number' => 1,
						],
						'c' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 3,
							'text' => 'text-c',
							'number' => 2,
						]
					]
				],
				[
					[ 'new', [], 'text-b', null, 'foo', 2 ],
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
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text',
							'number' => 1,
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
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text',
							'number' => 1,
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
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'text' => 'text-1 cite_error_references_duplicate_key',
							'number' => 1,
						]
					]
				],
				[
					[ 'new', [], 'text-1', 'a', 'foo', 1 ],
					[ 'increment', [], 'text-2', 'a', 'foo', 1 ]
				]
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
