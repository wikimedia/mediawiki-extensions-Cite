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
		$fragSpan = DOMCompat::getFirstElementChild( $fragment );
		DOMUtils::addAttributes( $fragSpan, [ 'class' => 'error mw-ext-cite-error' ] );
		return $fragment;
	}
}
