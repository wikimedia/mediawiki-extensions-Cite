<?php

namespace Cite\Tests\Unit;

use Cite\ErrorReporter;
use Cite\ReferenceStack;
use LogicException;
use Parser;
use StripState;
use Wikimedia\TestingAccessWrapper;

/**
 * @coversDefaultClass \Cite\ReferenceStack
 *
 * @license GPL-2.0-or-later
 */
class ReferenceStackTest extends \MediaWikiUnitTestCase {

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
					[ 'new', 1, '', null, null, [], 'text' ],
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
					[ 'new', 1, 'foo', null, null, [], 'text' ],
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
					[ 'new', 1, 'foo', null, null, [], 'text' ],
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
					[ 'new', 1, 'foo', 'name', null, [], 'text' ],
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
					[ 'new', 1, 'foo', 'a', null, [], 'text-a' ],
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
					[ 'new', 1, 'foo', null, null, [], 'text' ],
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
					[ 'new', 2, 'foo', null, null, [], 'text-b' ],
					[ 'new', 1, 'foo', 'a', null, [], 'text-a' ],
					[ 'new', 3, 'foo', 'c', null, [], 'text-c' ],
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
					[ 'new', 1, 'foo', 'a', null, [], 'text' ],
					[ 'increment', 1, 'foo', 'a', null, [], null ],
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
					[ 'new', 1, 'foo', 'a', null, [], null ],
					[ 'assign', 1, 'foo', 'a', null, [], 'text' ],
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
					[ 'new', 1, 'foo', 'a', null, [], 'text-1' ],
					[ 'increment', 1, 'foo', 'a', null, [], 'text-2' ],
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
					[ 'new', 1, 'foo', 'a', 'b', [], 'text-a' ],
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
					[ 'new', 1, 'foo', 'a', 'b', [], 'text-a' ],
					[ 'new', 2, 'foo', 'b', null, [], 'text-b' ],
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
					[ 'new', 1, 'foo', 'a', null, [], 'text-a' ],
					[ 'new', 2, 'foo', 'b', 'a', [], 'text-b' ],
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
					[ 'new', 1, 'foo', null, 'b', [], 'text-a' ],
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
					[ 'new', 1, 'foo', null, 'b', [], 'text-a' ],
					[ 'new', 2, 'foo', 'b', null, [], 'text-b' ],
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
					[ 'new', 1, 'foo', 'a', null, [], 'text-a' ],
					[ 'new', 2, 'foo', null, 'a', [], 'text-b' ],
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
					[ 'new', 1, 'foo', 'a', null, [], 'text-a' ],
					[ 'new', 2, 'foo', null, 'a', [], 'text-b' ],
					[ 'new', 3, 'foo', 'c', null, [], 'text-c' ],
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
					[ 'new', 2, 'foo', null, null, [], 'text-b' ],
					[ 'new', 3, 'foo', null, null, [], 'text-c' ],
					[ 'new', 1, 'foo', 'a', null, [], 'text-a' ],
				]
			],
		];
	}

	/**
	 * @covers ::rollbackRefs
	 * @covers ::rollbackRef
	 * @dataProvider provideRollbackRefs
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
					[ 'new', 1, 'foo', null, null, [], 'text' ],
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
					[ 'new', 1, 'foo', null, null, [], 'text' ],
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
					[ 'new', 1, 'foo', null, null, [], 'text' ],
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
					[ 'assign', 1, 'foo', null, null, [], 'text-2' ],
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
					[ 'increment', 1, 'foo', null, null, [], null ],
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
					[ 'increment', 1, 'foo', null, null, [], null ],
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
	 * @covers ::appendText
	 */
	public function testAppendText_failure() {
		$stack = $this->newStack();

		$this->expectException( LogicException::class );
		$stack->appendText( 'group', 'name', 'the-text' );
	}

	/**
	 * @covers ::appendText
	 */
	public function testAppendText() {
		$stack = $this->newStack();
		$stack->refs = [ 'group' => [ 'name' => [] ] ];

		$stack->appendText( 'group', 'name', 'set' );
		$this->assertSame( [ 'text' => 'set' ], $stack->refs['group']['name'] );

		$stack->appendText( 'group', 'name', ' and append' );
		$this->assertSame( [ 'text' => 'set and append' ], $stack->refs['group']['name'] );
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
