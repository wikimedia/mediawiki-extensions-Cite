<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Wikimedia\Parsoid\NodeData\DataMwError;

/**
 * @license GPL-2.0-or-later
 */
class ParsoidValidator {

	private bool $isSubreferenceSupported;

	public function __construct( bool $isSubreferenceSupported ) {
		$this->isSubreferenceSupported = $isSubreferenceSupported;
	}

	public function validateGroup( string $groupName, ReferencesData $referencesData ): ?DataMwError {
		if (
			$referencesData->inReferencesContent() &&
			!$referencesData->inRefContent() &&
			$groupName !== $referencesData->referencesGroup
		) {
			return new DataMwError(
				'cite_error_references_group_mismatch',
				[ $groupName ]
			);
		}

		return null;
	}

	public function validateDir( string $refDir, RefGroupItem $ref ): ?DataMwError {
		if ( $refDir !== 'rtl' && $refDir !== 'ltr' ) {
			return new DataMwError( 'cite_error_ref_invalid_dir', [ $refDir ] );
		} elseif ( $ref->dir !== '' && $ref->dir !== $refDir ) {
			return new DataMwError( 'cite_error_ref_conflicting_dir', [ $ref->name ] );
		}
		return null;
	}

	public function validateName( string $name, ?RefGroup $refGroup, ReferencesData $referencesData ): ?DataMwError {
		if ( !$refGroup->lookupRefByName( $name ) && $referencesData->inReferencesContent() ) {
			return new DataMwError(
				'cite_error_references_missing_key',
				[ $name ]
			);
		}

		return null;
	}

	public function validateFollow( string $followName, ?RefGroup $refGroup ): ?DataMwError {
		if ( !$refGroup->lookupRefByName( $followName ) ) {
			// FIXME: This key isn't exactly appropriate since this
			// is more general than just being in a <references>
			// section and it's the $followName we care about, but the
			// extension to the legacy parser doesn't have an
			// equivalent key and just outputs something wacky.
			return new DataMwError(
				'cite_error_references_missing_key',
				[ $followName ]
			);
		}

		return null;
	}

}
