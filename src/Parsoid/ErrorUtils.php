<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Wikimedia\Message\MessageValue;
use Wikimedia\Parsoid\DOM\DocumentFragment;
use Wikimedia\Parsoid\Ext\DOMUtils;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\NodeData\DataMwError;
use Wikimedia\Parsoid\Utils\DOMCompat;

/**
 * @license GPL-2.0-or-later
 */
class ErrorUtils {

	private ParsoidExtensionAPI $extApi;

	public function __construct( ParsoidExtensionAPI $extApi ) {
		$this->extApi = $extApi;
	}

	/**
	 * Adds classes and lead on an existing Parsoid rendering of an error message, sets the tracking
	 * category and returns the completed fragment
	 *
	 * @param MessageValue|DataMwError $error
	 * @return DocumentFragment
	 */
	public function renderParsoidError( object $error ): DocumentFragment {
		if ( $error instanceof DataMwError ) {
			$error = new MessageValue( $error->key, $error->params );
		}

		$this->extApi->addTrackingCategory( 'cite-tracking-category-cite-error' );

		$fragment = $this->extApi->createInterfaceI18nFragment( 'cite_error', [ $error ] );
		$fragSpan = DOMCompat::getFirstElementChild( $fragment );
		DOMUtils::addAttributes( $fragSpan, [ 'class' => 'error mw-ext-cite-error' ] );
		return $fragment;
	}
}
