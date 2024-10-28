<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Wikimedia\Message\MessageValue;
use Wikimedia\Parsoid\DOM\DocumentFragment;
use Wikimedia\Parsoid\Ext\DOMUtils;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\Utils\DOMCompat;

/**
 * @license GPL-2.0-or-later
 */
class ErrorUtils {
	/**
	 * This method compares the provided $catName against the Cite error message keys where there are known,
	 * expected differences in the rendering (typically: Parsoid renders errors at the bottom of the page rather
	 * than inline). It does not take into account categories where there are known, but unexpected/undesired
	 * differences (typically: one parser renders an error and the other does not).
	 */
	public static function isDiffingError( string $catName ): bool {
		static $diffingErrors = [
			'cite_error_ref_numeric_key' => true,
			'cite_error_ref_no_key' => true,
			'cite_error_ref_too_many_keys' => true,
			'cite_error_references_invalid_parameters' => true,
			'cite_error_ref_invalid_dir' => true,
			'cite_error_ref_follow_conflicts' => true,
			'cite_error_ref_no_input' => true,
			'cite_error_group_refs_without_references' => true,
		];
		return $diffingErrors[$catName] ?? false;
	}

	/**
	 * Creates a document fragment containing the Parsoid rendering of an error message
	 */
	public static function renderParsoidError(
		ParsoidExtensionAPI $extApi,
		string $errorKey,
		?array $errorParams
	): DocumentFragment {
		$error = new MessageValue( $errorKey, $errorParams ?? [] );
		return self::renderParsoidErrorSpan( $extApi, $error );
	}

	/**
	 * Adds classes and lead on an existing Parsoid rendering of an error message, sets the tracking category and
	 * returns the completed fragment
	 */
	public static function renderParsoidErrorSpan(
		ParsoidExtensionAPI $extApi, MessageValue $error
	): DocumentFragment {
		$extApi->addTrackingCategory( 'cite-tracking-category-cite-error' );
		$fragment = $extApi->createInterfaceI18nFragment( 'cite_error', [ $error ] );
		$fragSpan = DOMCompat::getLastElementChild( $fragment );
		DOMUtils::addAttributes( $fragSpan, [ 'class' => 'error mw-ext-cite-error' ] );
		return $fragment;
	}
}
