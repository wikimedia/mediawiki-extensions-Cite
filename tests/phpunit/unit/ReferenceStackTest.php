<?php

namespace Cite\Tests\Unit;

use Cite\ReferenceStack;
use LogicException;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @covers \Cite\ReferenceStack
 * @license GPL-2.0-or-later
 */
class ReferenceStackTest extends \MediaWikiUnitTestCase {

	public function testPushInvalidRef() {
		$stack = $this->newStack();

		$stack->pushInvalidRef();

		$this->assertSame( [ false ], $stack->refCallStack );
	}

	/**
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
			$result = $stack->pushRef(
				$mockStripState,
				...$refs[$i]
			);

			$this->assertArrayHasKey( $i, $expectedOutputs,
				'Bad test, not enough expected outputs in fixture.' );
			$this->assertSame( $expectedOutputs[$i], $result );
		}

		$this->assertSame( $finalRefs, $stack->refs );
		$this->assertSame( $finalCallStack, $stack->refCallStack );
	}

	public static function providePushRef() {
		return [
			'Anonymous ref in default group' => [
				'refs' => [
					[ 'text', [], '', null, null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text',
						'number' => 1,
					]
				],
				'finalRefs' => [
					'' => [
						[
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => null,
							'text' => 'text',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, '', null, null, 'text', [] ],
				]
			],
			'Anonymous ref in named group' => [
				'refs' => [
					[ 'text', [], 'foo', null, null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text',
						'number' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						[
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => null,
							'text' => 'text',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', null, null, 'text', [] ],
				]
			],
			'Ref with text' => [
				'refs' => [
					[ 'text', [], 'foo', null, null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text',
						'number' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						[
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => null,
							'text' => 'text',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', null, null, 'text', [] ],
				]
			],
			'Named ref with text' => [
				'refs' => [
					[ 'text', [], 'foo', 'name', null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'name',
						'text' => 'text',
						'number' => 1,
					],
				],
				'finalRefs' => [
					'foo' => [
						'name' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'name',
							'text' => 'text',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'name', null, 'text', [] ],
				]
			],
			'Follow after base' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-b', [], 'foo', 'b', null, 'a', 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
					],
					null
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a text-b',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-a', [] ],
				]
			],
			'Follow with no base' => [
				'refs' => [
					[ 'text', [], 'foo', null, null, 'a', 'rtl' ]
				],
				'expectedOutputs' => [
					null
				],
				'finalRefs' => [
					'foo' => [
						[
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => null,
							'text' => 'text',
							'follow' => 'a',
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', null, null, 'text', [] ],
				]
			],
			'Follow pointing to later ref' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-b', [], 'foo', null, null, 'c', 'rtl' ],
					[ 'text-c', [], 'foo', 'c', null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
					],
					null,
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 3,
						'name' => 'c',
						'text' => 'text-c',
						'number' => 2,
					]
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
						],
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 2,
							'name' => null,
							'text' => 'text-b',
							'follow' => 'c',
						],
						'c' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 3,
							'name' => 'c',
							'text' => 'text-c',
							'number' => 2,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-a', [] ],
					[ 'new', 2, 'foo', null, null, 'text-b', [] ],
					[ 'new', 3, 'foo', 'c', null, 'text-c', [] ],
				]
			],
			'Repeated ref, text in first tag' => [
				'refs' => [
					[ 'text', [], 'foo', 'a', null, null, 'rtl' ],
					[ null, [], 'foo', 'a', null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text',
						'number' => 1,
					],
					[
						'count' => 1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text',
						'number' => 1,
					],
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text', [] ],
					[ 'increment', 1, 'foo', 'a', null, null, [] ],
				]
			],
			'Repeated ref, text in second tag' => [
				'refs' => [
					[ null, [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text', [], 'foo', 'a', null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => null,
						'number' => 1,
					],
					[
						'count' => 1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text',
						'number' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, null, [] ],
					[ 'assign', 1, 'foo', 'a', null, 'text', [] ],
				]
			],
			'Repeated ref, mismatched text' => [
				'refs' => [
					[ 'text-1', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-2', [], 'foo', 'a', null, null, 'rtl' ]
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-1',
						'number' => 1,
					],
					[
						'count' => 1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-1',
						'number' => 1,
						'warnings' => [ [ 'cite_error_references_duplicate_key', 'a' ] ],
					]
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-1',
							'number' => 1,
							'warnings' => [ [ 'cite_error_references_duplicate_key', 'a' ] ],
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-1', [] ],
					[ 'increment', 1, 'foo', 'a', null, 'text-2', [] ],
				]
			],
			'Named extends with no parent' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', 'b', null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
						'extends' => 'b',
						'extendsIndex' => 1,
					],
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
							'extends' => 'b',
							'extendsIndex' => 1,
						],
						'b' => [
							'number' => 1,
							'__placeholder__' => true,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', 'b', 'text-a', [] ],
				]
			],
			'Named extends before parent' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', 'b', null, 'rtl' ],
					[ 'text-b', [], 'foo', 'b', null, null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
						'extends' => 'b',
						'extendsIndex' => 1,
					],
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 2,
						'name' => 'b',
						'text' => 'text-b',
						'number' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
							'extends' => 'b',
							'extendsIndex' => 1,
						],
						'b' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 2,
							'name' => 'b',
							'text' => 'text-b',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', 'b', 'text-a', [] ],
					[ 'new-from-placeholder', 2, 'foo', 'b', null, 'text-b', [] ],
				]
			],
			'Named extends after parent' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-b', [], 'foo', 'b', 'a', null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
					],
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 2,
						'name' => 'b',
						'text' => 'text-b',
						'number' => 1,
						'extends' => 'a',
						'extendsIndex' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
						],
						'b' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 2,
							'name' => 'b',
							'text' => 'text-b',
							'number' => 1,
							'extends' => 'a',
							'extendsIndex' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-a', [] ],
					[ 'new', 2, 'foo', 'b', 'a', 'text-b', [] ],
				]
			],
			'Anonymous extends with no parent' => [
				'refs' => [
					[ 'text-a', [], 'foo', null, 'b', null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text-a',
						'number' => 1,
						'extends' => 'b',
						'extendsIndex' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => null,
							'text' => 'text-a',
							'number' => 1,
							'extends' => 'b',
							'extendsIndex' => 1,
						],
						'b' => [
							'number' => 1,
							'__placeholder__' => true,
						]
					],
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', null, 'b', 'text-a', [] ],
				]
			],
			'Anonymous extends before parent' => [
				'refs' => [
					[ 'text-a', [], 'foo', null, 'b', null, 'rtl' ],
					[ 'text-b', [], 'foo', 'b', null, null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text-a',
						'number' => 1,
						'extends' => 'b',
						'extendsIndex' => 1,
					],
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 2,
						'name' => 'b',
						'text' => 'text-b',
						'number' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => null,
							'text' => 'text-a',
							'number' => 1,
							'extends' => 'b',
							'extendsIndex' => 1,
						],
						'b' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 2,
							'name' => 'b',
							'text' => 'text-b',
							'number' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', null, 'b', 'text-a', [] ],
					[ 'new-from-placeholder', 2, 'foo', 'b', null, 'text-b', [] ],
				]
			],
			'Anonymous extends after parent' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-b', [], 'foo', null, 'a', null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
					],
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 2,
						'name' => null,
						'text' => 'text-b',
						'number' => 1,
						'extends' => 'a',
						'extendsIndex' => 1,
					]
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
						],
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 2,
							'name' => null,
							'text' => 'text-b',
							'number' => 1,
							'extends' => 'a',
							'extendsIndex' => 1,
						]
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-a', [] ],
					[ 'new', 2, 'foo', null, 'a', 'text-b', [] ],
				]
			],
			'Normal after extends' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-b', [], 'foo', null, 'a', null, 'rtl' ],
					[ 'text-c', [], 'foo', 'c', null, null, 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
					],
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 2,
						'name' => null,
						'text' => 'text-b',
						'number' => 1,
						'extends' => 'a',
						'extendsIndex' => 1,
					],
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 3,
						'name' => 'c',
						'text' => 'text-c',
						'number' => 2,
					],
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
						],
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 2,
							'name' => null,
							'text' => 'text-b',
							'number' => 1,
							'extends' => 'a',
							'extendsIndex' => 1,
						],
						'c' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 3,
							'name' => 'c',
							'text' => 'text-c',
							'number' => 2,
						],
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-a', [] ],
					[ 'new', 2, 'foo', null, 'a', 'text-b', [] ],
					[ 'new', 3, 'foo', 'c', null, 'text-c', [] ],
				]
			],
			'Two incomplete follows' => [
				'refs' => [
					[ 'text-a', [], 'foo', 'a', null, null, 'rtl' ],
					[ 'text-b', [], 'foo', null, null, 'd', 'rtl' ],
					[ 'text-c', [], 'foo', null, null, 'd', 'rtl' ],
				],
				'expectedOutputs' => [
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'a',
						'text' => 'text-a',
						'number' => 1,
					],
					null,
					null
				],
				'finalRefs' => [
					'foo' => [
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
						],
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 2,
							'name' => null,
							'text' => 'text-b',
							'follow' => 'd',
						],
						1 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 3,
							'name' => null,
							'text' => 'text-c',
							'follow' => 'd',
						],
					]
				],
				'finalCallStack' => [
					[ 'new', 1, 'foo', 'a', null, 'text-a', [] ],
					[ 'new', 2, 'foo', null, null, 'text-b', [] ],
					[ 'new', 3, 'foo', null, null, 'text-c', [] ],
				]
			],
		];
	}

	/**
	 * @dataProvider provideRollbackRefs
	 */
	public function testRollbackRefs(
		array $initialCallStack,
		array $initialRefs,
		int $rollbackCount,
		$expectedResult,
		array $expectedRefs = []
	) {
		$stack = $this->newStack();
		$stack->refCallStack = $initialCallStack;
		$stack->refs = $initialRefs;

		if ( is_string( $expectedResult ) ) {
			$this->expectException( LogicException::class );
			$this->expectExceptionMessage( $expectedResult );
		}
		$redoStack = $stack->rollbackRefs( $rollbackCount );
		$this->assertSame( $expectedResult, $redoStack );
		$this->assertSame( $expectedRefs, $stack->refs );
	}

	public static function provideRollbackRefs() {
		return [
			'Empty stack' => [
				'initialCallStack' => [],
				'initialRefs' => [],
				'rollbackCount' => 0,
				'expectedResult' => [],
				'expectedRefs' => [],
			],
			'Attempt to overflow stack bounds' => [
				'initialCallStack' => [],
				'initialRefs' => [],
				'rollbackCount' => 1,
				'expectedResult' => [],
				'expectedRefs' => [],
			],
			'Skip invalid refs' => [
				'initialCallStack' => [ false ],
				'initialRefs' => [],
				'rollbackCount' => 1,
				'expectedResult' => [],
				'expectedRefs' => [],
			],
			'Missing group' => [
				'initialCallStack' => [
					[ 'new', 1, 'foo', null, null, 'text', [] ],
				],
				'initialRefs' => [],
				'rollbackCount' => 1,
				'expectedResult' => 'Cannot roll back ref with unknown group "foo".',
			],
			'Find anonymous ref by key' => [
				'initialCallStack' => [
					[ 'new', 1, 'foo', null, null, 'text', [] ],
				],
				'initialRefs' => [ 'foo' => [
					[
						'key' => 1,
					],
				] ],
				'rollbackCount' => 1,
				'expectedResult' => [
					[ 'text', [] ],
				],
				'expectedRefs' => [],
			],
			'Missing anonymous ref' => [
				'initialCallStack' => [
					[ 'new', 1, 'foo', null, null, 'text', [] ],
				],
				'initialRefs' => [ 'foo' => [
					[
						'key' => 2,
					],
				] ],
				'rollbackCount' => 1,
				'expectedResult' => 'Cannot roll back unknown ref by key 1.',
			],
			'Assign text' => [
				'initialCallStack' => [
					[ 'assign', 1, 'foo', null, null, 'text-2', [] ],
				],
				'initialRefs' => [ 'foo' => [
					[
						'count' => 2,
						'key' => 1,
						'text' => 'text-1',
					],
				] ],
				'rollbackCount' => 1,
				'expectedResult' => [
					[ 'text-2', [] ],
				],
				'expectedRefs' => [ 'foo' => [
					[
						'count' => 1,
						'key' => 1,
						'text' => null,
					],
				] ],
			],
			'Increment' => [
				'initialCallStack' => [
					[ 'increment', 1, 'foo', null, null, null, [] ],
				],
				'initialRefs' => [ 'foo' => [
					[
						'count' => 2,
						'key' => 1,
					],
				] ],
				'rollbackCount' => 1,
				'expectedResult' => [
					[ null, [] ],
				],
				'expectedRefs' => [ 'foo' => [
					[
						'count' => 1,
						'key' => 1,
					],
				] ],
			],
			'Safely ignore placeholder' => [
				'initialCallStack' => [
					[ 'increment', 1, 'foo', null, null, null, [] ],
				],
				'initialRefs' => [ 'foo' => [
					[
						'placeholder' => true,
						'number' => 10,
					],
					[
						'count' => 2,
						'key' => 1,
					],
				] ],
				'rollbackCount' => 1,
				'expectedResult' => [
					[ null, [] ],
				],
				'expectedRefs' => [ 'foo' => [
					[
						'placeholder' => true,
						'number' => 10,
					],
					[
						'count' => 1,
						'key' => 1,
					],
				] ],
			],
		];
	}

	public function testRollbackRefs_extends() {
		$stack = $this->newStack();

		$mockStripState = $this->createMock( StripState::class );
		$mockStripState->method( 'unstripBoth' )->willReturnArgument( 0 );
		$stack->pushRef(
			$mockStripState,
			'text', [],
			'foo', null, 'a', null, 'rtl'
		);
		$this->assertSame( 1, $stack->extendsCount['foo']['a'] );

		$stack->rollbackRefs( 1 );

		$this->assertSame( 0, $stack->extendsCount['foo']['a'] );
	}

	public function testRemovals() {
		$stack = $this->newStack();
		$stack->refs = [ 'group1' => [], 'group2' => [] ];

		$this->assertSame( [], $stack->popGroup( 'group1' ) );
		$this->assertSame( [ 'group2' => [] ], $stack->refs );
	}

	public function testGetGroups() {
		$stack = $this->newStack();
		$stack->refs = [ 'havenot' => [], 'have' => [ [ 'ref etc' ] ] ];

		$this->assertSame( [ 'have' ], $stack->getGroups() );
	}

	public function testHasGroup() {
		$stack = $this->newStack();
		$stack->refs = [ 'present' => [ [ 'ref etc' ] ], 'empty' => [] ];

		$this->assertFalse( $stack->hasGroup( 'absent' ) );
		$this->assertTrue( $stack->hasGroup( 'present' ) );
		$this->assertFalse( $stack->hasGroup( 'empty' ) );
	}

	public function testGetGroupRefs() {
		$stack = $this->newStack();
		$stack->refs = [ 'present' => [ [ 'ref etc' ] ], 'empty' => [] ];

		$this->assertSame( [], $stack->getGroupRefs( 'absent' ) );
		$this->assertSame( [ [ 'ref etc' ] ], $stack->getGroupRefs( 'present' ) );
		$this->assertSame( [], $stack->getGroupRefs( 'empty' ) );
	}

	/**
	 * @return ReferenceStack
	 */
	private function newStack() {
		return TestingAccessWrapper::newFromObject( new ReferenceStack() );
	}

}
