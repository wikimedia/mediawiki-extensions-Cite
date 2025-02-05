<?php

namespace Cite\Tests\Integration;

use Cite\ReferenceStack;
use Cite\Validator;
use Wikimedia\TestingAccessWrapper;

/**
 * @covers \Cite\Validator
 * @license GPL-2.0-or-later
 */
class ValidatorTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @dataProvider provideValidateRef
	 */
	public function testValidateRef(
		array $referencesStack,
		?string $inReferencesGroup,
		bool $isSectionPreview,
		?string $text,
		?string $group,
		?string $name,
		?string $follow,
		?string $dir,
		?string $expected
	) {
		$stack = new ReferenceStack();
		TestingAccessWrapper::newFromObject( $stack )->refs = $referencesStack;

		$validator = new Validator( $stack, $inReferencesGroup, $isSectionPreview );

		$status = $validator->validateRef( $text, $group, $name, $follow, $dir );
		if ( $expected ) {
			$this->assertStatusError( $expected, $status );
		} else {
			$this->assertStatusGood( $status );
		}
	}

	public static function provideValidateRef() {
		return [
			// Shared <ref> validations regardless of context
			'Numeric name' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => null,
				'group' => '',
				'name' => '1',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_ref_numeric_key',
			],
			'Numeric follow' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 't',
				'group' => '',
				'name' => null,
				'follow' => '1',
				'dir' => null,
				'expected' => 'cite_error_ref_numeric_key',
			],
			'Follow with name' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 't',
				'group' => '',
				'name' => 'n',
				'follow' => 'f',
				'dir' => null,
				'expected' => 'cite_error_ref_follow_conflicts',
			],
			// Validating <ref> outside of <references>
			'text-only <ref>' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 't',
				'group' => '',
				'name' => null,
				'follow' => null,
				'dir' => null,
				'expected' => null,
			],
			'Whitespace or empty text' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => '',
				'group' => '',
				'name' => null,
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_ref_no_input',
			],
			'totally empty <ref>' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => null,
				'group' => '',
				'name' => null,
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_ref_no_key',
			],
			'empty-name <ref>' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 't',
				'group' => '',
				'name' => '',
				'follow' => null,
				'dir' => null,
				'expected' => null,
			],
			'contains <ref>-like text' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 'Foo <ref name="bar">',
				'group' => '',
				'name' => 'n',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_included_ref',
			],

			// Validating a <ref> in <references>
			'most trivial <ref> in <references>' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => 'not empty',
				'group' => 'g',
				'name' => 'n',
				'follow' => null,
				'dir' => null,
				'expected' => null,
			],
			'Different group than <references>' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g1',
				'isSectionPreview' => false,
				'text' => 't',
				'group' => 'g2',
				'name' => 'n',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_references_group_mismatch',
			],
			'Unnamed in <references>' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => 't',
				'group' => 'g',
				'name' => null,
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_references_no_key',
			],
			'Empty name in <references>' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => 't',
				'group' => 'g',
				'name' => '',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_references_no_key',
			],
			'Empty text in <references>' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => '',
				'group' => 'g',
				'name' => 'n',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_empty_references_define',
			],
			'Group never used' => [
				'referencesStack' => [ 'g2' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => 'not empty',
				'group' => 'g',
				'name' => 'n',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_references_missing_key',
			],
			'Ref never used' => [
				'referencesStack' => [ 'g' => [ 'n' => [] ] ],
				'inReferencesGroup' => 'g',
				'isSectionPreview' => false,
				'text' => 'not empty',
				'group' => 'g',
				'name' => 'n2',
				'follow' => null,
				'dir' => null,
				'expected' => 'cite_error_references_missing_key',
			],
			'Good dir' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 'not empty',
				'group' => '',
				'name' => 'n',
				'follow' => null,
				'dir' => 'rtl',
				'expected' => null,
			],
			'Bad dir' => [
				'referencesStack' => [],
				'inReferencesGroup' => null,
				'isSectionPreview' => false,
				'text' => 'not empty',
				'group' => '',
				'name' => 'n',
				'follow' => null,
				'dir' => 'foobar',
				'expected' => 'cite_error_ref_invalid_dir',
			],
		];
	}

}
