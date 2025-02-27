<?php

namespace Cite;

use MediaWiki\Parser\Sanitizer;
use StatusValue;

/**
 * Context-aware, detailed validation of the arguments and content of a <ref> tag.
 *
 * @license GPL-2.0-or-later
 */
class Validator {

	private ReferenceStack $referenceStack;
	private ?string $inReferencesGroup;
	private bool $isSectionPreview;

	/**
	 * @param ReferenceStack $referenceStack
	 * @param string|null $inReferencesGroup Group name of the <references> context to consider
	 *  during validation. Null if we are currently not in a <references> context.
	 * @param bool $isSectionPreview Validation is relaxed when previewing parts of a page
	 */
	public function __construct(
		ReferenceStack $referenceStack,
		?string $inReferencesGroup = null,
		bool $isSectionPreview = false
	) {
		$this->referenceStack = $referenceStack;
		$this->inReferencesGroup = $inReferencesGroup;
		$this->isSectionPreview = $isSectionPreview;
	}

	public function validateRef(
		?string $text,
		string $group,
		?string $name,
		?string $follow,
		?string $dir,
		?string $details
	): StatusValue {
		if ( ctype_digit( (string)$name )
			|| ctype_digit( (string)$follow )
		) {
			// Numeric names mess up the resulting id's, potentially producing
			// duplicate id's in the XHTML.  The Right Thing To Do
			// would be to mangle them, but it's not really high-priority
			// (and would produce weird id's anyway).
			return StatusValue::newFatal( 'cite_error_ref_numeric_key' );
		}

		if ( $follow && $name ) {
			return StatusValue::newFatal( 'cite_error_ref_follow_conflicts' );
		}

		if ( $dir !== null && $dir !== 'rtl' && $dir !== 'ltr' ) {
			return StatusValue::newFatal( 'cite_error_ref_invalid_dir', $dir );
		}

		return $this->inReferencesGroup === null ?
			$this->validateRefOutsideOfReferenceList( $text, $name, $details ) :
			$this->validateRefInReferenceList( $text, $group, $name, $details );
	}

	private function validateRefOutsideOfReferenceList(
		?string $text,
		?string $name,
		?string $details
	): StatusValue {
		if ( !$name ) {
			$isSelfClosingTag = $text === null;
			$containsText = trim( $text ?? '' ) !== '';
			if ( $details !== null && !$containsText ) {
				return StatusValue::newFatal( 'cite_error_details_missing_parent' );
			} elseif ( $isSelfClosingTag ) {
				// Completely empty ref like <ref /> is forbidden.
				return StatusValue::newFatal( 'cite_error_ref_no_key' );
			} elseif ( !$containsText ) {
				// Must have content or reuse another ref by name.
				return StatusValue::newFatal( 'cite_error_ref_no_input' );
			}
		}

		if ( $text !== null && preg_match(
				'/<ref(erences)?\b[^>]*+>/i',
				preg_replace( '#<(\w++)[^>]*+>.*?</\1\s*>|<!--.*?-->#s', '', $text )
			) ) {
			// (bug T8199) This most likely implies that someone left off the
			// closing </ref> tag, which will cause the entire article to be
			// eaten up until the next <ref>.  So we bail out early instead.
			// The fancy regex above first tries chopping out anything that
			// looks like a comment or SGML tag, which is a crude way to avoid
			// false alarms for <nowiki>, <pre>, etc.
			//
			// Possible improvement: print the warning, followed by the contents
			// of the <ref> tag.  This way no part of the article will be eaten
			// even temporarily.
			return StatusValue::newFatal( 'cite_error_included_ref' );
		}

		return StatusValue::newGood();
	}

	private function validateRefInReferenceList(
		?string $text,
		string $group,
		?string $name,
		?string $details
	): StatusValue {
		if ( $group !== $this->inReferencesGroup ) {
			// <ref> and <references> have conflicting group attributes.
			return StatusValue::newFatal( 'cite_error_references_group_mismatch',
				Sanitizer::safeEncodeAttribute( $group ) );
		}

		if ( !$name ) {
			// <ref> calls inside <references> must be named
			return StatusValue::newFatal( 'cite_error_references_no_key' );
		}

		if ( $details !== null && $details !== '' ) {
			$status = StatusValue::newGood();
			$status->warning( 'cite_error_details_unsupported_context',
				Sanitizer::safeEncodeAttribute( $name ) );
			return $status;
		}

		if ( $text === null || trim( $text ) === '' ) {
			// <ref> called in <references> has no content.
			return StatusValue::newFatal(
				'cite_error_empty_references_define',
				Sanitizer::safeEncodeAttribute( $name ),
				Sanitizer::safeEncodeAttribute( $group )
			);
		}

		// Section previews are exempt from some rules.
		if ( !$this->isSectionPreview ) {
			$groupRefs = $this->referenceStack->getGroupRefs( $group );

			if ( !isset( $groupRefs[$name] ) ) {
				// No such named ref exists in this group.
				return StatusValue::newFatal( 'cite_error_references_missing_key',
					Sanitizer::safeEncodeAttribute( $name ) );
			}
		}

		return StatusValue::newGood();
	}

}
