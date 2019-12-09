<?php

namespace Cite\Tests\Unit;

use Cite\ErrorReporter;
use Cite\ReferenceStack;
use MediaWikiUnitTestCase;
use Parser;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\ReferenceStack
 *
 * @license GPL-2.0-or-later
 */
class ReferenceStackTest extends MediaWikiUnitTestCase {

	/**
	 * @covers ::__construct
	 * @covers ::pushInvalidRef
	 */
	public function testPushInvalidRef() {
		$stack = $this->newStack();

		$stack->pushInvalidRef();

		$this->assertSame( [ false ], $stack->refCallStack );
	}

	/**
	 * @covers ::pushRef
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
			[ $text, $name, $group, $extends, $follow, $argv, $dir ] = $refs[$i];
			$result = $stack->pushRef(
				$this->createMock( Parser::class ),
				$text, $name, $group, $extends, $follow, $argv, $dir, $mockStripState );

			$this->assertTrue( array_key_exists( $i, $expectedOutputs ),
				'Bad test, not enough expected outputs in fixture.' );
			$this->assertSame( $expectedOutputs[$i], $result );
		}

		$this->assertSame( $finalRefs, $stack->refs );
		$this->assertSame( $finalCallStack, $stack->refCallStack );
	}

	public function providePushRef() {
		return [
			'Anonymous ref in default group' => [
				[
					[ 'text', null, '', null, null, [], 'rtl' ]
				],
				[
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text',
						'number' => 1,
					]
				],
				[
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
				[
					[ 'new', [], 'text', null, null, '', 1 ]
				]
			],
			'Anonymous ref in named group' => [
				[
					[ 'text', null, 'foo', null, null, [], 'rtl' ]
				],
				[
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text',
						'number' => 1,
					]
				],
				[
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
				[
					[ 'new', [], 'text', null, null, 'foo', 1 ]
				]
			],
			'Ref with text' => [
				[
					[ 'text', null, 'foo', null, null, [], 'rtl' ]
				],
				[
					[
						'count' => -1,
						'dir' => 'rtl',
						'key' => 1,
						'name' => null,
						'text' => 'text',
						'number' => 1,
					]
				],
				[
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
				[
					[ 'new', [], 'text', null, null, 'foo', 1 ]
				]
			],
			'Named ref with text' => [
				[
					[ 'text', 'name', 'foo', null, null, [], 'rtl' ]
				],
				[
					[
						'count' => 0,
						'dir' => 'rtl',
						'key' => 1,
						'name' => 'name',
						'text' => 'text',
						'number' => 1,
					],
				],
				[
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
				[
					[ 'new', [], 'text', 'name', null, 'foo', 1 ]
				]
			],
			'Follow after base' => [
				[
					[ 'text-a', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-b', 'b', 'foo', null, 'a', [], 'rtl' ]
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', 'a', null, 'foo', 1 ]
				]
			],
			'Follow with no base' => [
				[
					[ 'text', null, 'foo', null, 'a', [], 'rtl' ]
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
							'name' => null,
							'text' => 'text',
							'follow' => 'a',
						]
					]
				],
				[
					[ 'new', [], 'text', null, null, 'foo', 1 ]
				]
			],
			'Follow pointing to later ref' => [
				[
					[ 'text-a', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-b', null, 'foo', null, 'c', [], 'rtl' ],
					[ 'text-c', 'c', 'foo', null, null, [], 'rtl' ]
				],
				[
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
				[
					'foo' => [
						0 => [
							'count' => -1,
							'dir' => 'rtl',
							'key' => 2,
							'name' => null,
							'text' => 'text-b',
							'follow' => 'c',
						],
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
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
				[
					[ 'new', [], 'text-b', null, null, 'foo', 2 ],
					[ 'new', [], 'text-a', 'a', null, 'foo', 1 ],
					[ 'new', [], 'text-c', 'c', null, 'foo', 3 ]
				]
			],
			'Repeated ref, text in first tag' => [
				[
					[ 'text', 'a', 'foo', null, null, [], 'rtl' ],
					[ null, 'a', 'foo', null, null, [], 'rtl' ]
				],
				[
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
				[
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
				[
					[ 'new', [], 'text', 'a', null, 'foo', 1 ],
					[ 'increment', [], null, 'a', null, 'foo', 1 ]
				]
			],
			'Repeated ref, text in second tag' => [
				[
					[ null, 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text', 'a', 'foo', null, null, [], 'rtl' ]
				],
				[
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
				[
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
				[
					[ 'new', [], null, 'a', null, 'foo', 1 ],
					[ 'assign', [], 'text', 'a', null, 'foo', 1 ]
				]
			],
			'Repeated ref, mismatched text' => [
				[
					[ 'text-1', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-2', 'a', 'foo', null, null, [], 'rtl' ]
				],
				[
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
						'text' => 'text-1 cite_error_references_duplicate_key',
						'number' => 1,
					]
				],
				[
					'foo' => [
						'a' => [
							'count' => 1,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-1 cite_error_references_duplicate_key',
							'number' => 1,
						]
					]
				],
				[
					[ 'new', [], 'text-1', 'a', null, 'foo', 1 ],
					[ 'increment', [], 'text-2', 'a', null, 'foo', 1 ]
				]
			],
			'Named extends with no parent' => [
				[
					[ 'text-a', 'a', 'foo', 'b', null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', 'a', 'b', 'foo', 1 ],
				]
			],
			'Named extends before parent' => [
				[
					[ 'text-a', 'a', 'foo', 'b', null, [], 'rtl' ],
					[ 'text-b', 'b', 'foo', null, null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', 'a', 'b', 'foo', 1 ],
					[ 'new', [], 'text-b', 'b', null, 'foo', 2 ],
				]
			],
			'Named extends after parent' => [
				[
					[ 'text-a', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-b', 'b', 'foo', 'a', null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', 'a', null, 'foo', 1 ],
					[ 'new', [], 'text-b', 'b', 'a', 'foo', 2 ],
				]
			],
			'Anonymous extends with no parent' => [
				[
					[ 'text-a', null, 'foo', 'b', null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', null, 'b', 'foo', 1 ],
				]
			],
			'Anonymous extends before parent' => [
				[
					[ 'text-a', null, 'foo', 'b', null, [], 'rtl' ],
					[ 'text-b', 'b', 'foo', null, null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', null, 'b', 'foo', 1 ],
					[ 'new', [], 'text-b', 'b', null, 'foo', 2 ],
				]
			],
			'Anonymous extends after parent' => [
				[
					[ 'text-a', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-b', null, 'foo', 'a', null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', 'a', null, 'foo', 1 ],
					[ 'new', [], 'text-b', null, 'a', 'foo', 2 ],
				]
			],
			'Normal after extends' => [
				[
					[ 'text-a', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-b', null, 'foo', 'a', null, [], 'rtl' ],
					[ 'text-c', 'c', 'foo', null, null, [], 'rtl' ],
				],
				[
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
				[
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
				[
					[ 'new', [], 'text-a', 'a', null, 'foo', 1 ],
					[ 'new', [], 'text-b', null, 'a', 'foo', 2 ],
					[ 'new', [], 'text-c', 'c', null, 'foo', 3 ],
				]
			],
			'Two broken follows' => [
				[
					[ 'text-a', 'a', 'foo', null, null, [], 'rtl' ],
					[ 'text-b', null, 'foo', null, 'd', [], 'rtl' ],
					[ 'text-c', null, 'foo', null, 'd', [], 'rtl' ],
				],
				[
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
				[
					'foo' => [
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
						'a' => [
							'count' => 0,
							'dir' => 'rtl',
							'key' => 1,
							'name' => 'a',
							'text' => 'text-a',
							'number' => 1,
						],
					]
				],
				[
					[ 'new', [], 'text-b', null, null, 'foo', 2 ],
					[ 'new', [], 'text-c', null, null, 'foo', 3 ],
					[ 'new', [], 'text-a', 'a', null, 'foo', 1 ],
				]
			],
		];
	}

	/**
	 * @covers ::rollbackRefs
	 * @covers ::rollbackRef
	 * @dataProvider provideRollbackRefs
	 *
	 * @param array $initialCallStack
	 * @param array $initialRefs
	 * @param int $count
	 * @param array $expectedRedo
	 * @param array $expectedRefs
	 */
	public function testRollbackRefs(
		array $initialCallStack,
		array $initialRefs,
		int $count,
		array $expectedRedo,
		array $expectedRefs
	) {
		$stack = $this->newStack();
		$stack->refCallStack = $initialCallStack;
		$stack->refs = $initialRefs;

		$redo = $stack->rollbackRefs( $count );
		$this->assertSame( $expectedRedo, $redo );
		$this->assertSame( $expectedRefs, $stack->refs );
	}

	public function provideRollbackRefs() {
		return [
			'Empty stack' => [ [], [], 0, [], [] ],
			'Attempt to overflow stack bounds' => [ [], [], 1, [], [] ],
			'Skip invalid refs' => [ [ false ], [], 1, [], [] ],
			'Missing group' => [
				[
					[ 'new', [], 'text', null, null, 'foo', 1 ],
				],
				[],
				1,
				[
					[ [], 'text' ]
				],
				[]
			],
			'Find anonymous ref by key' => [
				[
					[ 'new', [], 'text', null, null, 'foo', 1 ],
				],
				[
					'foo' => [
						[
							'key' => 1,
						]
					]
				],
				1,
				[
					[ [], 'text' ]
				],
				[]
			],
			'Missing anonymous ref' => [
				[
					[ 'new', [], 'text', null, null, 'foo', 1 ],
				],
				[
					'foo' => [
						[
							'key' => 2,
						]
					]
				],
				1,
				[
					[ [], 'text' ]
				],
				[
					'foo' => [
						[
							'key' => 2,
						]
					]
				]
			],
			'Assign text' => [
				[
					[ 'assign', [], 'text-2', null, null, 'foo', 1 ],
				],
				[
					'foo' => [
						[
							'count' => 2,
							'key' => 1,
							'text' => 'text-1',
						]
					]
				],
				1,
				[
					[ [], 'text-2' ]
				],
				[
					'foo' => [
						[
							'count' => 1,
							'key' => 1,
							'text' => null,
						]
					]
				],
			],
			'Increment' => [
				[
					[ 'increment', [], null, null, null, 'foo', 1 ],
				],
				[
					'foo' => [
						[
							'count' => 2,
							'key' => 1,
						]
					]
				],
				1,
				[
					[ [], null ]
				],
				[
					'foo' => [
						[
							'count' => 1,
							'key' => 1,
						]
					]
				],
			],
			'Safely ignore placeholder' => [
				[
					[ 'increment', [], null, null, null, 'foo', 1 ],
				],
				[
					'foo' => [
						[
							'placeholder' => true,
							'number' => 10,
						],
						[
							'count' => 2,
							'key' => 1,
						]
					]
				],
				1,
				[
					[ [], null ]
				],
				[
					'foo' => [
						[
							'placeholder' => true,
							'number' => 10,
						],
						[
							'count' => 1,
							'key' => 1,
						]
					]
				],
			],
		];
	}

	/**
	 * @covers ::rollbackRef
	 */
	public function testRollbackRefs_extends() {
		$stack = $this->newStack();

		$mockStripState = $this->createMock( StripState::class );
		$mockStripState->method( 'unstripBoth' )->willReturnArgument( 0 );
		/** @var StripState $mockStripState */
		$stack->pushRef(
			$this->createMock( Parser::class ),
			'text', null, 'foo', 'a', null, [], 'rtl', $mockStripState );
		$this->assertSame( 1, $stack->extendsCount['foo']['a'] );

		$redo = $stack->rollbackRefs( 1 );

		$this->assertSame( 0, $stack->extendsCount['foo']['a'] );
	}

	/**
	 * @covers ::popGroup
	 */
	public function testRemovals() {
		$stack = $this->newStack();
		$stack->refs = [ 'group1' => [], 'group2' => [] ];

		$this->assertSame( [], $stack->popGroup( 'group1' ) );
		$this->assertSame( [ 'group2' => [] ], $stack->refs );
	}

	/**
	 * @covers ::getGroups
	 */
	public function testGetGroups() {
		$stack = $this->newStack();
		$stack->refs = [ 'havenot' => [], 'have' => [ [ 'ref etc' ] ] ];

		$this->assertSame( [ 'have' ], $stack->getGroups() );
	}

	/**
	 * @covers ::hasGroup
	 */
	public function testHasGroup() {
		$stack = $this->newStack();
		$stack->refs = [ 'present' => [ [ 'ref etc' ] ], 'empty' => [] ];

		$this->assertFalse( $stack->hasGroup( 'absent' ) );
		$this->assertTrue( $stack->hasGroup( 'present' ) );
		$this->assertFalse( $stack->hasGroup( 'empty' ) );
	}

	/**
	 * @covers ::getGroupRefs
	 */
	public function testGetGroupRefs() {
		$stack = $this->newStack();
		$stack->refs = [ 'present' => [ [ 'ref etc' ] ], 'empty' => [] ];

		$this->assertSame( [], $stack->getGroupRefs( 'absent' ) );
		$this->assertSame( [ [ 'ref etc' ] ], $stack->getGroupRefs( 'present' ) );
		$this->assertSame( [], $stack->getGroupRefs( 'empty' ) );
	}

	/**
	 * @covers ::setRefText
	 */
	public function testSetRefText() {
		$stack = $this->newStack();

		$stack->setRefText( 'group', 'name', 'the-text' );

		$this->assertSame(
			[ 'group' => [ 'name' => [ 'text' => 'the-text' ] ] ],
			$stack->refs
		);
	}

	/**
	 * @return ReferenceStack
	 */
	private function newStack() {
		$errorReporter = $this->createMock( ErrorReporter::class );
		$errorReporter->method( 'plain' )->willReturnArgument( 1 );
		/** @var ErrorReporter $errorReporter */
		return TestingAccessWrapper::newFromObject( new ReferenceStack( $errorReporter ) );
	}

}
