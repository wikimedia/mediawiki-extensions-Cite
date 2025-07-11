<?php
declare( strict_types = 1 );
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

namespace Cite\Parsoid;

use Closure;
use Exception;
use MediaWiki\Config\Config;
use Wikimedia\Parsoid\DOM\DocumentFragment;
use Wikimedia\Parsoid\DOM\Element;
use Wikimedia\Parsoid\Ext\DOMDataUtils;
use Wikimedia\Parsoid\Ext\DOMUtils;
use Wikimedia\Parsoid\Ext\ExtensionTagHandler;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\Utils\DOMCompat;

/**
 * Simple token transform version of the Ref extension tag.
 * @license GPL-2.0-or-later
 */
class RefTagHandler extends ExtensionTagHandler {

	private readonly bool $isSubreferenceSupported;

	public function __construct( Config $mainConfig ) {
		$this->isSubreferenceSupported = $mainConfig->get( 'CiteSubReferencing' );
	}

	/** @inheritDoc */
	public function sourceToDom(
		ParsoidExtensionAPI $extApi, string $txt, array $extArgs
	): ?DocumentFragment {
		// Drop nested refs entirely, unless we've explicitly allowed them
		$parentExtTag = $extApi->parentExtTag();
		if ( $parentExtTag === 'ref' && empty( $extApi->parentExtTagOpts()['allowNestedRef'] ) ) {
			return null;
		}

		// The one supported case for nested refs is from the {{#tag:ref}} parser
		// function.  However, we're overly permissive here since we can't
		// distinguish when that's nested in another template.
		// The php preprocessor did our expansion.
		$allowNestedRef = $extApi->inTemplate();

		return $extApi->extTagToDOM(
			$extArgs,
			$txt,
			[
				// NOTE: sup's content model requires it only contain phrasing
				// content, not flow content. However, since we are building an
				// in-memory DOM which is simply a tree data structure, we can
				// nest flow content in a <sup> tag.
				'wrapperTag' => 'sup',
				'parseOpts' => [
					'extTag' => 'ref',
					'extTagOpts' => [ 'allowNestedRef' => $allowNestedRef ],
					// Ref content doesn't need p-wrapping or indent-pres.
					// Treat this as inline-context content to get that b/c behavior.
					'context' => 'inline',
				],
			]
		);
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
	public function lintHandler(
		ParsoidExtensionAPI $extApi, Element $ref, callable $defaultHandler
	): bool {
		$dataMw = DOMDataUtils::getDataMw( $ref );

		// Only lint content pointed at by the id.  Content embedded in
		// data-mw will be traversed by linter when
		// processAttributeEmbeddedHTML is called
		if ( !isset( $dataMw->body->id ) ) {
			return true;
		}

		// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
		$bodyElt = DOMCompat::getElementById( $extApi->getTopLevelDoc(), $dataMw->body->id );
		if ( !$bodyElt ) {
			return true;
		}

		$hasRefName = (bool)$dataMw->getExtAttrib( 'name' );
		$hasFollow = (bool)$dataMw->getExtAttrib( 'follow' );

		if ( $hasFollow ) {
			$about = DOMCompat::getAttribute( $ref, 'about' );
			$followNode = $about !== null ? DOMCompat::querySelector(
				$bodyElt, "span[typeof~='mw:Cite/Follow'][about='{$about}']"
			) : null;
			if ( $followNode ) {
				$defaultHandler( $followNode );
			}
		} elseif (
			$hasRefName &&
			DOMCompat::querySelector( $bodyElt, "span[typeof~='mw:Cite/Follow']" )
		) {
			// Follow content may have been added as spans, so temporarily
			// move them out to avoid linting them redundantly
			// domToWikitext clones $bodyElt and removes the spans, which
			// works here, but is maybe less performant
			$tmpFrag = $bodyElt->ownerDocument->createDocumentFragment();
			foreach ( DOMUtils::childNodes( $bodyElt ) as $child ) {
				if ( DOMUtils::hasTypeOf( $child, 'mw:Cite/Follow' ) ) {
					$tmpFrag->appendChild( $child );
				}
			}
			$defaultHandler( $bodyElt );
			DOMUtils::migrateChildren( $tmpFrag, $bodyElt );
		} else {
			$defaultHandler( $bodyElt );
		}

		return true;
	}

	/** @inheritDoc */
	public function domToWikitext(
		ParsoidExtensionAPI $extApi, Element $node, bool $wrapperUnmodified
	) {
		$dataMw = DOMDataUtils::getDataMw( $node );
		// Drop the conversion of synthetic main refs. The content of these will be retrived
		// by the corresponding subrefs with the `details` attribute.
		if ( isset( $dataMw->isSyntheticMainRef ) ) {
			return '';
		}

		$startTagSrc = $extApi->extStartTagToWikitext( $node );
		if ( !isset( $dataMw->body ) ) {
			return $startTagSrc; // We self-closed this already.
		}

		$html2wtOpts = [
			'extName' => $dataMw->name,
			// FIXME: One-off PHP parser state leak. This needs a better solution.
			'inPHPBlock' => true
		];

		if ( isset( $dataMw->body->html ) ) {
			// First look for the extension's content in data-mw.body.html
			// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
			$src = $extApi->htmlToWikitext( $html2wtOpts, $dataMw->body->html );
		} elseif ( isset( $dataMw->body->id ) ) {
			// If the body isn't contained in data-mw.body.html, look if
			// there's an element pointed to by body->id.
			// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
			$bodyElt = DOMCompat::getElementById( $extApi->getTopLevelDoc(), $dataMw->body->id );

			// So far, this is specified for Cite and relies on the "id"
			// referring to an element in the top level dom, even though the
			// <ref> itself may be in embedded content,
			// https://www.mediawiki.org/wiki/Specs/HTML/Extensions/Cite#Ref_and_References
			// FIXME: This doesn't work if the <references> section
			// itself is in embedded content, since we aren't traversing
			// in there.

			// If we couldn't find a body element, this is a bug.
			// Add some extra debugging for the editing client (ex: VisualEditor)
			if ( !$bodyElt ) {
				$extApi->log(
					'error/' . $dataMw->name,
					'extension src id ' . $dataMw->body->id . ' points to non-existent element for:',
					DOMCompat::getOuterHTML( $node ),
					'. More debug info: ',
					$this->extraDebugInfo( $node, $extApi )
				);
				return ''; // Drop it!
			}

			$hasRefName = (bool)$dataMw->getExtAttrib( 'name' );
			$hasFollow = (bool)$dataMw->getExtAttrib( 'follow' );

			if ( $hasFollow ) {
				$about = DOMCompat::getAttribute( $node, 'about' );
				$followNode = $about !== null ? DOMCompat::querySelector(
					$bodyElt, "span[typeof~='mw:Cite/Follow'][about='{$about}']"
				) : null;
				// Re-serialize incomplete follow like normal so we don't loose the content
				$followNode ??= DOMCompat::querySelector(
					$bodyElt, "span[typeof~='mw:Cite/Follow']"
				);
				if ( $followNode ) {
					$src = $extApi->domToWikitext( $html2wtOpts, $followNode, true );
					$src = ltrim( $src, ' ' );
				} else {
					$src = '';
				}
			} else {
				// Follow content may have been added as spans, so drop it
				if (
					$hasRefName &&
					DOMCompat::querySelector( $bodyElt, "span[typeof~='mw:Cite/Follow']" )
				) {
					$bodyElt = DOMDataUtils::cloneNode( $bodyElt, true );
					foreach ( DOMUtils::childNodes( $bodyElt ) as $child ) {
						if ( DOMUtils::hasTypeOf( $child, 'mw:Cite/Follow' ) ) {
							// @phan-suppress-next-line PhanTypeMismatchArgumentSuperType
							DOMCompat::remove( $child );
						}
					}
				}
				$src = $extApi->domToWikitext( $html2wtOpts, $bodyElt, true );
			}
		} else {
			$extApi->log( 'error', 'Ref body unavailable for: ' . DOMCompat::getOuterHTML( $node ) );
			return ''; // Drop it!
		}

		if ( $this->isSubreferenceSupported &&
			 $dataMw->getExtAttrib( 'details' ) !== null &&
			 isset( $dataMw->mainRef )
		) {
			// TODO: maintain original order of attributes
			$dataMw->setExtAttrib( 'name', $dataMw->mainRef );
			// TODO: escape wikitext for attribute
			$dataMw->setExtAttrib( 'details', $src );

			if ( isset( $dataMw->isSubRefWithMainBody ) ) {
				$mainElt = DOMCompat::getElementById( $extApi->getTopLevelDoc(), $dataMw->mainBody );
				if ( $mainElt ) {
					$src = $extApi->domToWikitext( $html2wtOpts, $mainElt, true );
					DOMCompat::remove( $mainElt );
				}
			} else {
				$src = '';
				unset( $dataMw->body );
			}

			$startTagSrc = $extApi->extStartTagToWikitext( $node );
		}

		return empty( $dataMw->body ) ?
			$startTagSrc :
			$startTagSrc . $src . '</' . $dataMw->name . '>';
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
				// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
				$origDataMw->body->html, $origNode->ownerDocument,
				[ 'markNew' => true ]
			);
			$editedFragment = $extApi->htmlToDom(
				// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
				$editedDataMw->body->html, $editedNode->ownerDocument,
				[ 'markNew' => true ]
			);
			return $domDiff( $origFragment, $editedFragment );
		} elseif ( isset( $origDataMw->body->id ) && isset( $editedDataMw->body->id ) ) {
			$origId = $origDataMw->body->id;
			$editedId = $editedDataMw->body->id;

			// So far, this is specified for Cite and relies on the "id"
			// referring to an element in the top level dom, even though the
			// <ref> itself may be in embedded content,
			// https://www.mediawiki.org/wiki/Specs/HTML/Extensions/Cite#Ref_and_References
			// FIXME: This doesn't work if the <references> section
			// itself is in embedded content, since we aren't traversing
			// in there.
			// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
			$origHtml = DOMCompat::getElementById( $origNode->ownerDocument, $origId );
			// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
			$editedHtml = DOMCompat::getElementById( $editedNode->ownerDocument, $editedId );

			if ( $origHtml && $editedHtml ) {
				return $domDiff( $origHtml, $editedHtml );
			} else {
				// Log error
				if ( !$origHtml ) {
					$extApi->log(
						'error/domdiff/orig/ref',
						"extension src id {$origId} points to non-existent element for:",
						DOMCompat::getOuterHTML( $origNode )
					);
				}
				if ( !$editedHtml ) {
					$extApi->log(
						// use info level to avoid logspam for CX edits where translated
						// docs might reference nodes not copied over from orig doc.
						'info/domdiff/edited/ref',
						"extension src id {$editedId} points to non-existent element for:",
						DOMCompat::getOuterHTML( $editedNode )
					);
				}
			}
		}

		// FIXME: Similar to DOMDiff::subtreeDiffers, maybe $editNode should
		// be marked as inserted to avoid losing any edits, at the cost of
		// more normalization

		return false;
	}

	private function extraDebugInfo( Element $node, ParsoidExtensionAPI $extApi ): string {
		$firstA = DOMCompat::querySelector( $node, 'a[href]' );
		$href = $firstA ? DOMCompat::getAttribute( $firstA, 'href' ) : null;
		if ( $href && str_starts_with( $href, '#' ) ) {
			try {
				$ref = DOMCompat::querySelector( $extApi->getTopLevelDoc(), $href );
				if ( $ref ) {
					return ' [doc: ' . DOMCompat::getOuterHTML( $ref ) . ']';
				}
			} catch ( Exception ) {
				// We are just providing VE with debugging info.
				// So, ignore all exceptions / errors in this code.
			}
			return ' [reference ' . $href . ' not found]';
		}
		return '';
	}

}
