<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Cite\Validator;
use Closure;
use MediaWiki\Config\Config;
use Wikimedia\Message\MessageValue;
use Wikimedia\Parsoid\DOM\DocumentFragment;
use Wikimedia\Parsoid\DOM\Element;
use Wikimedia\Parsoid\Ext\DOMDataUtils;
use Wikimedia\Parsoid\Ext\ExtensionTagHandler;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\Utils\DOMCompat;

/**
 * @license GPL-2.0-or-later
 */
class ReferenceListTagHandler extends ExtensionTagHandler {

	private Config $mainConfig;

	public function __construct( Config $mainConfig ) {
		$this->mainConfig = $mainConfig;
	}

	/** @inheritDoc */
	public function sourceToDom(
		ParsoidExtensionAPI $extApi, string $txt, array $extArgs
	): DocumentFragment {
		$domFragment = $extApi->extTagToDOM(
			$extArgs,
			$txt,
			[
				'parseOpts' => [ 'extTag' => 'references' ],
			]
		);

		// Detect invalid parameters on the references tag
		$status = Validator::filterReferenceListArguments( $extApi->extArgsToArray( $extArgs ) );
		$refsOpts = $status->getValue();
		if ( !$status->isGood() ) {
			$extApi->pushError( 'cite_error_references_invalid_parameters' );
			$error = new MessageValue( 'cite_error_references_invalid_parameters' );
		}

		$referenceList = new References( $this->mainConfig );
		$frag = $referenceList->createEmptyReferenceListFragment(
			$extApi,
			$domFragment,
			$refsOpts,
			static function ( $dp ) use ( $extApi ) {
				$dp->src = $extApi->extTag->getSource();
				// Setting redundant info on fragment.
				// $docBody->firstChild info feels cumbersome to use downstream.
				if ( $extApi->extTag->isSelfClosed() ) {
					$dp->selfClose = true;
				}
			}
		);
		$domFragment->appendChild( $frag );

		if ( isset( $error ) ) {
			$errorFragment = ( new ErrorUtils( $extApi ) )->renderParsoidError( $error );
			$frag->appendChild( $errorFragment );
		}

		return $domFragment;
	}

	/** @inheritDoc */
	public function processAttributeEmbeddedHTML(
		ParsoidExtensionAPI $extApi, Element $elt, Closure $proc
	): void {
		$dataMw = DOMDataUtils::getDataMw( $elt );
		if ( isset( $dataMw->body->html ) ) {
			$dataMw->body->html = $proc( $dataMw->body->html );
		}
	}

	/** @inheritDoc */
	public function domToWikitext(
		ParsoidExtensionAPI $extApi, Element $node, bool $wrapperUnmodified
	) {
		$dataMw = DOMDataUtils::getDataMw( $node );
		// Autogenerated references aren't considered erroneous (the extension to the legacy
		// parser also generates them) and are not suppressed when serializing because apparently
		// that's the behaviour Parsoid clients want.  However, autogenerated references *with
		// group attributes* are errors (the legacy extension doesn't generate them at all) and
		// are suppressed when serialized since we considered them an error while parsing and
		// don't want them to persist in the content.
		if ( !empty( $dataMw->autoGenerated ) && ( $dataMw->attrs->group ?? '' ) !== '' ) {
			return '';
		} else {
			$startTagSrc = $extApi->extStartTagToWikitext( $node );
			if ( empty( $dataMw->body ) ) {
				// We self-closed this already
				return $startTagSrc;
			} else {
				if ( isset( $dataMw->body->html ) ) {
					$src = $extApi->htmlToWikitext(
						[ 'extName' => $dataMw->name ],
						$dataMw->body->html
					);
					return $startTagSrc . $src . '</' . $dataMw->name . '>';
				} else {
					$extApi->log( 'error',
						'References body unavailable for: ' . DOMCompat::getOuterHTML( $node )
					);
					// Drop it!
					return '';
				}
			}
		}
	}

	/** @inheritDoc */
	public function lintHandler(
		ParsoidExtensionAPI $extApi, Element $refs, callable $defaultHandler
	): bool {
		$dataMw = DOMDataUtils::getDataMw( $refs );
		if ( isset( $dataMw->body->html ) ) {
			$fragment = $extApi->htmlToDom( $dataMw->body->html );
			$defaultHandler( $fragment );
		}
		return true;
	}

	/** @inheritDoc */
	public function diffHandler(
		ParsoidExtensionAPI $extApi, callable $domDiff, Element $origNode,
		Element $editedNode
	): bool {
		$origDataMw = DOMDataUtils::getDataMw( $origNode );
		$editedDataMw = DOMDataUtils::getDataMw( $editedNode );

		if ( isset( $origDataMw->body->html ) && isset( $editedDataMw->body->html ) ) {
			$origFragment = $extApi->htmlToDom(
				$origDataMw->body->html, $origNode->ownerDocument,
				[ 'markNew' => true ]
			);
			$editedFragment = $extApi->htmlToDom(
				$editedDataMw->body->html, $editedNode->ownerDocument,
				[ 'markNew' => true ]
			);
			return $domDiff( $origFragment, $editedFragment );
		}

		// FIXME: Similar to DOMDiff::subtreeDiffers, maybe $editNode should
		// be marked as inserted to avoid losing any edits, at the cost of
		// more normalization

		return false;
	}

}
