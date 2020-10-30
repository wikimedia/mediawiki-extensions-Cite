<?php
declare( strict_types = 1 );

namespace Wikimedia\Parsoid\Ext\Cite;

use DOMDocumentFragment;
use DOMElement;
use DOMNode;
use stdClass;
use Wikimedia\Parsoid\Core\DomSourceRange;
use Wikimedia\Parsoid\Ext\DOMDataUtils;
use Wikimedia\Parsoid\Ext\DOMUtils;
use Wikimedia\Parsoid\Ext\ExtensionTagHandler;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\Ext\PHPUtils;
use Wikimedia\Parsoid\Ext\WTUtils;
use Wikimedia\Parsoid\Utils\DOMCompat;

class References extends ExtensionTagHandler {
	/**
	 * @param DOMNode $node
	 * @return bool
	 */
	private static function hasRef( DOMNode $node ): bool {
		$c = $node->firstChild;
		while ( $c ) {
			if ( DOMUtils::isElt( $c ) ) {
				if ( WTUtils::isSealedFragmentOfType( $c, 'ref' ) ) {
					return true;
				}
				if ( self::hasRef( $c ) ) {
					return true;
				}
			}
			$c = $c->nextSibling;
		}
		return false;
	}

	/**
	 * @param ParsoidExtensionAPI $extApi
	 * @param DOMDocumentFragment $domFragment
	 * @param array $refsOpts
	 * @param ?callable $modifyDp
	 * @param bool $autoGenerated
	 * @return DOMElement
	 */
	private static function createReferences(
		ParsoidExtensionAPI $extApi, DOMDocumentFragment $domFragment,
		array $refsOpts, ?callable $modifyDp, bool $autoGenerated = false
	): DOMElement {
		$doc = $domFragment->ownerDocument;

		$ol = $doc->createElement( 'ol' );
		DOMCompat::getClassList( $ol )->add( 'mw-references' );
		DOMCompat::getClassList( $ol )->add( 'references' );

		DOMUtils::migrateChildren( $domFragment, $ol );

		// Support the `responsive` parameter
		$rrOpts = $extApi->getSiteConfig()->responsiveReferences();
		$responsiveWrap = !empty( $rrOpts['enabled'] );
		if ( $refsOpts['responsive'] !== null ) {
			$responsiveWrap = $refsOpts['responsive'] !== '0';
		}

		if ( $responsiveWrap ) {
			$div = $doc->createElement( 'div' );
			DOMCompat::getClassList( $div )->add( 'mw-references-wrap' );
			$div->appendChild( $ol );
			$frag = $div;
		} else {
			$frag = $ol;
		}

		if ( $autoGenerated ) {
			// FIXME: This is very much trying to copy ExtensionHandler::onDocument
			DOMUtils::addAttributes( $frag, [
				'typeof' => 'mw:Extension/references',
				'about' => $extApi->newAboutId()
			] );
			$dataMw = (object)[
				'name' => 'references',
				'attrs' => new stdClass
			];
			// Dont emit empty keys
			if ( $refsOpts['group'] ) {
				$dataMw->attrs->group = $refsOpts['group'];
			}
			DOMDataUtils::setDataMw( $frag, $dataMw );
		}

		$dp = DOMDataUtils::getDataParsoid( $frag );
		if ( $refsOpts['group'] ) {  // No group for the empty string either
			$dp->group = $refsOpts['group'];
			$ol->setAttribute( 'data-mw-group', $refsOpts['group'] );
		}
		if ( $modifyDp ) {
			$modifyDp( $dp );
		}

		return $frag;
	}

	/**
	 * @param ParsoidExtensionAPI $extApi
	 * @param DOMElement $node
	 * @param ReferencesData $refsData
	 * @param ?string $referencesGroup
	 */
	private static function extractRefFromNode(
		ParsoidExtensionAPI $extApi,
		DOMElement $node, ReferencesData $refsData,
		?string $referencesGroup = ''
	): void {
		$doc = $node->ownerDocument;

		// This is data-parsoid from the dom fragment node that's gone through
		// dsr computation and template wrapping.
		$nodeDp = DOMDataUtils::getDataParsoid( $node );
		$typeOf = $node->getAttribute( 'typeof' );
		$isTplWrapper = DOMUtils::hasTypeOf( $node, 'mw:Transclusion' );
		$nodeType = preg_replace( '#mw:DOMFragment/sealed/ref#', '', $typeOf, 1 );
		$contentId = $nodeDp->html;
		$tplDmw = $isTplWrapper ? DOMDataUtils::getDataMw( $node ) : null;

		// This is the <sup> that's the meat of the sealed fragment
		$c = $extApi->getContentDOM( $contentId )->firstChild;
		DOMUtils::assertElt( $c );
		$cDp = DOMDataUtils::getDataParsoid( $c );
		$refDmw = DOMDataUtils::getDataMw( $c );
		if ( empty( $cDp->empty ) && self::hasRef( $c ) ) { // nested ref-in-ref
			$refsData->pushInEmbeddedContent();
			self::processRefs( $extApi, $refsData, $c );
			$refsData->popInEmbeddedContent();
		}

		// Use the about attribute on the wrapper with priority, since it's
		// only added when the wrapper is a template sibling.
		$about = $node->hasAttribute( 'about' )
			? $node->getAttribute( 'about' )
			: $c->getAttribute( 'about' );

		// FIXME(SSS): Need to clarify semantics here.
		// If both the containing <references> elt as well as the nested <ref>
		// elt has a group attribute, what takes precedence?
		$groupName = $refDmw->attrs->group ?? $referencesGroup ?? '';

		// NOTE: This will have been trimmed in Utils::getExtArgInfo()'s call
		// to TokenUtils::kvToHash() and ExtensionHandler::normalizeExtOptions()
		$refName = $refDmw->attrs->name ?? '';
		$followName = $refDmw->attrs->follow ?? '';
		$refDir = strtolower( $refDmw->attrs->dir ?? '' );

		// Looks like Cite.php doesn't try to fix ids that already have
		// a "_" in them. Ex: name="a b" and name="a_b" are considered
		// identical. Not sure if this is a feature or a bug.
		// It also considers entities equal to their encoding
		// (i.e. '&' === '&amp;'), which is done:
		//  in PHP: Sanitizer#decodeTagAttributes and
		//  in Parsoid: ExtensionHandler#normalizeExtOptions
		$refName = $extApi->sanitizeHTMLId( $refName );
		$followName = $extApi->sanitizeHTMLId( $followName );

		// Add ref-index linkback
		$linkBack = $doc->createElement( 'sup' );

		$ref = null;
		$errs = [];

		$hasRefName = strlen( $refName ) > 0;
		$hasFollow = strlen( $followName ) > 0;

		$validFollow = false;

		if ( $hasFollow ) {
			// Always wrap follows content so that there's no ambiguity
			// where to find it when roundtripping
			$span = $doc->createElement( 'span' );
			DOMUtils::addTypeOf( $span, 'mw:Cite/Follow' );
			$span->setAttribute( 'about', $about );
			$span->appendChild(
				$doc->createTextNode( ' ' )
			);
			DOMUtils::migrateChildren( $c, $span );
			$c->appendChild( $span );

			if ( $hasRefName ) {
				$errs[] = [ 'key' => 'cite_error_ref_too_many_keys' ];
			} else {
				// This is a follows ref, so check that a named ref has already
				// been defined
				$group = $refsData->getRefGroup( $groupName );
				if ( isset( $group->indexByName[$followName] ) ) {
					$validFollow = true;
					$ref = $group->indexByName[$followName];

					if ( $ref->contentId ) {
						$refContent = $extApi->getContentDOM( $ref->contentId )->firstChild;
						DOMUtils::migrateChildren( $c, $refContent );
					} else {
						// Otherwise, we have a follow that comes after a named
						// ref without content so use the follow fragment as
						// the content
						// This will be set below with `$ref->contentId = $contentId;`
					}
				} else {
					$errs[] = [ 'key' => 'cite_error_references_missing_key' ];
				}
			}
		}

		if ( !$ref ) {
			$ref = $refsData->add(
				$extApi, $groupName, $refName, $about, $linkBack
			);
		}

		if ( isset( $refDmw->attrs->dir ) && $refDir !== 'rtl' && $refDir !== 'ltr' ) {
			$errs[] = [ 'key' => 'cite_error_ref_invalid_dir' ];
		}

		// FIXME: At some point this error message can be changed to a warning, as Parsoid Cite now
		// supports numerals as a name without it being an actual error, but core Cite does not.
		// Follow refs do not duplicate the error which can be correlated with the original ref.
		if ( ctype_digit( $refName ) ) {
			$errs[] = [ 'key' => 'cite_error_ref_numeric_key' ];
		}

		// Check for missing content, added ?? '' to fix T259676 crasher
		// FIXME: See T260082 for a more complete description of cause and deeper fix
		$missingContent = ( !empty( $cDp->empty ) || trim( $refDmw->body->extsrc ?? '' ) === '' );

		if ( $missingContent ) {
			// Check for missing name and content to generate error code
			if ( !$hasRefName ) {
				if ( !empty( $cDp->selfClose ) ) {
					$errs[] = [ 'key' => 'cite_error_ref_no_key' ];
				} else {
					$errs[] = [ 'key' => 'cite_error_ref_no_input' ];
				}
			}

			if ( !empty( $cDp->selfClose ) ) {
				unset( $refDmw->body );
			} else {
				// Empty the <sup> since we've serialized its children and
				// removing it below asserts everything has been migrated out
				DOMCompat::replaceChildren( $c );
				$refDmw->body = (object)[ 'html' => $refDmw->body->extsrc ];
			}
		} else {
			// If there are multiple <ref>s with the same name, but different content,
			// the content of the first <ref> shows up in the <references> section.
			// in order to ensure lossless RT-ing for later <refs>, we have to record
			// HTML inline for all of them.
			$html = '';
			$contentDiffers = false;
			if ( $ref->hasMultiples ) {
				// FIXME: Strip the mw:Cite/Follow wrappers
				// See the test, "Forward-referenced ref with magical follow edge case"
				$html = $extApi->domToHtml( $c, true, true );
				// Empty the <sup> since we've serialized its children and
				// removing it below asserts everything has been migrated out
				DOMCompat::replaceChildren( $c );
				$contentDiffers = $html !== $ref->cachedHtml;
				if ( $contentDiffers ) {
					// TODO: Since this error is being placed on the ref, the
					// key should arguably be "cite_error_ref_duplicate_key"
					$errs[] = [ 'key' => 'cite_error_references_duplicate_key' ];
				}
			}
			if ( $contentDiffers ) {
				$refDmw->body = (object)[ 'html' => $html ];
			} else {
				$refDmw->body = (object)[ 'id' => 'mw-reference-text-' . $ref->target ];
			}
		}

		$lastLinkback = $ref->linkbacks[count( $ref->linkbacks ) - 1] ?? null;
		DOMUtils::addAttributes( $linkBack, [
				'about' => $about,
				'class' => 'mw-ref reference',
				'id' => ( $refsData->inEmbeddedContent() || $validFollow ) ?
					null : ( $ref->name ? $lastLinkback : $ref->id ),
				'rel' => 'dc:references',
				'typeof' => $nodeType
			]
		);
		DOMUtils::addTypeOf( $linkBack, 'mw:Extension/ref' );

		$dataParsoid = new stdClass;
		if ( isset( $nodeDp->src ) ) {
			$dataParsoid->src = $nodeDp->src;
		}
		if ( isset( $nodeDp->dsr ) ) {
			$dataParsoid->dsr = $nodeDp->dsr;
		}
		if ( isset( $nodeDp->pi ) ) {
			$dataParsoid->pi = $nodeDp->pi;
		}
		DOMDataUtils::setDataParsoid( $linkBack, $dataParsoid );

		$dmw = $isTplWrapper ? $tplDmw : $refDmw;
		DOMDataUtils::setDataMw( $linkBack, $dmw );

		if ( count( $errs ) > 0 ) {
			self::addErrorsToNode( $linkBack, $errs );
		}

		// FIXME(T263052): This should be moved to CSS
		if ( $validFollow ) {
			$linkBack->setAttribute( 'style', 'display: none;' );
		}

		// refLink is the link to the citation
		$refLink = $doc->createElement( 'a' );
		DOMUtils::addAttributes( $refLink, [
			'href' => $extApi->getPageUri() . '#' . $ref->target,
			'style' => 'counter-reset: mw-Ref ' . $ref->groupIndex . ';',
		] );
		if ( $ref->group ) {
			$refLink->setAttribute( 'data-mw-group', $ref->group );
		}

		// refLink-span which will contain a default rendering of the cite link
		// for browsers that don't support counters
		$refLinkSpan = $doc->createElement( 'span' );
		$refLinkSpan->setAttribute( 'class', 'mw-reflink-text' );
		$refLinkSpan->appendChild( $doc->createTextNode(
			'[' . ( $ref->group ? $ref->group . ' ' : '' ) . $ref->groupIndex . ']'
		) );

		$refLink->appendChild( $refLinkSpan );
		$linkBack->appendChild( $refLink );

		$node->parentNode->replaceChild( $linkBack, $node );

		// Keep the first content to compare multiple <ref>s with the same name.
		if ( $ref->contentId === null && !$missingContent ) {
			$ref->contentId = $contentId;
			$ref->dir = $refDir;
		} else {
			DOMCompat::remove( $c );
			$extApi->clearContentDOM( $contentId );
		}
	}

	/**
	 * @param DOMElement $node
	 * @param array $errs
	 */
	private static function addErrorsToNode( DOMElement $node, array $errs ) {
		DOMUtils::addTypeOf( $node, 'mw:Error' );
		$dmw = DOMDataUtils::getDataMw( $node );
		$dmw->errors = is_array( $dmw->errors ?? null ) ?
			array_merge( $dmw->errors, $errs ) : $errs;
	}

	/**
	 * @param ParsoidExtensionAPI $extApi
	 * @param DOMElement $refsNode
	 * @param ReferencesData $refsData
	 * @param bool $autoGenerated
	 */
	private static function insertReferencesIntoDOM(
		ParsoidExtensionAPI $extApi, DOMElement $refsNode,
		ReferencesData $refsData, bool $autoGenerated = false
	): void {
		$isTplWrapper = DOMUtils::hasTypeOf( $refsNode, 'mw:Transclusion' );
		$dp = DOMDataUtils::getDataParsoid( $refsNode );
		$group = $dp->group ?? '';
		$refGroup = $refsData->getRefGroup( $group );

		// Iterate through the ref list to back-patch typeof and data-mw error
		// information into ref for errors only known at time of references
		// insertion.  Refs in the top level dom will be processed immediately,
		// whereas embedded refs will be gathered for batch processing, since
		// we need to parse embedded content to find them.
		if ( $refGroup ) {
			$autoGeneratedWithGroup = ( $autoGenerated && $group !== '' );
			foreach ( $refGroup->refs as $ref ) {
				$errs = [];
				// Mark all refs that are part of a group that is autogenerated
				if ( $autoGeneratedWithGroup ) {
					$errs[] = [ 'key' => 'cite_error_group_refs_without_references' ];
				}
				// Mark all refs that are named without content
				if ( ( $ref->name !== '' ) && $ref->contentId === null ) {
					// TODO: Since this error is being placed on the ref,
					// the key should arguably be "cite_error_ref_no_text"
					$errs[] = [ 'key' => 'cite_error_references_no_text' ];
				}
				if ( count( $errs ) > 0 ) {
					foreach ( $ref->nodes as $node ) {
						self::addErrorsToNode( $node, $errs );
					}
					foreach ( $ref->embeddedNodes as $about ) {
						$refsData->embeddedErrors[$about] = $errs;
					}
				}
			}
		}

		$nestedRefsHTML = array_map(
			function ( DOMElement $sup ) use ( $extApi ) {
				return $extApi->domToHtml( $sup, false, true ) . "\n";
			},
			DOMCompat::querySelectorAll(
				$refsNode, 'sup[typeof~=\'mw:Extension/ref\']'
			)
		);

		if ( !$isTplWrapper ) {
			$dataMw = DOMDataUtils::getDataMw( $refsNode );

			// Detect invalid parameters on the references tag
			$knownAttributes = [ 'group', 'responsive' ];
			foreach ( $dataMw->attrs as $key => $value ) {
				if ( !in_array( strtolower( $key ), $knownAttributes, true ) ) {
					self::addErrorsToNode(
						$refsNode, [ [ 'key' => 'cite_error_references_invalid_parameters' ] ]
					);
					break;
				}
			}

			// Mark this auto-generated so that we can skip this during
			// html -> wt and so that clients can strip it if necessary.
			if ( $autoGenerated ) {
				$dataMw->autoGenerated = true;
			} elseif ( count( $nestedRefsHTML ) > 0 ) {
				$dataMw->body = (object)[ 'html' => "\n" . implode( $nestedRefsHTML ) ];
			} elseif ( empty( $dp->selfClose ) ) {
				$dataMw->body = PHPUtils::arrayToObject( [ 'html' => '' ] );
			} else {
				unset( $dataMw->body );
			}
			// @phan-suppress-next-line PhanTypeObjectUnsetDeclaredProperty
			unset( $dp->selfClose );
		}

		// Deal with responsive wrapper
		if ( DOMCompat::getClassList( $refsNode )->contains( 'mw-references-wrap' ) ) {
			$rrOpts = $extApi->getSiteConfig()->responsiveReferences();
			if ( $refGroup && count( $refGroup->refs ) > $rrOpts['threshold'] ) {
				DOMCompat::getClassList( $refsNode )->add( 'mw-references-columns' );
			}
			$refsNode = $refsNode->firstChild;
		}

		// Remove all children from the references node
		//
		// Ex: When {{Reflist}} is reused from the cache, it comes with
		// a bunch of references as well. We have to remove all those cached
		// references before generating fresh references.
		DOMCompat::replaceChildren( $refsNode );

		if ( $refGroup ) {
			foreach ( $refGroup->refs as $ref ) {
				$refGroup->renderLine( $extApi, $refsNode, $ref );
			}
		}

		// Remove the group from refsData
		$refsData->removeRefGroup( $group );
	}

	/**
	 * Process `<ref>`s left behind after the DOM is fully processed.
	 * We process them as if there was an implicit `<references />` tag at
	 * the end of the DOM.
	 *
	 * @param ParsoidExtensionAPI $extApi
	 * @param ReferencesData $refsData
	 * @param DOMNode $node
	 */
	public static function insertMissingReferencesIntoDOM(
		ParsoidExtensionAPI $extApi, ReferencesData $refsData, DOMNode $node
	): void {
		$doc = $node->ownerDocument;
		foreach ( $refsData->getRefGroups() as $groupName => $refsGroup ) {
			$domFragment = $doc->createDocumentFragment();
			$frag = self::createReferences(
				$extApi,
				$domFragment,
				[
					// Force string cast here since in the foreach above, $groupName
					// is an array key. In that context, number-like strings are
					// silently converted to a numeric value!
					// Ex: In <ref group="2" />, the "2" becomes 2 in the foreach
					'group' => (string)$groupName,
					'responsive' => null,
				],
				function ( $dp ) use ( $extApi ) {
					// The new references come out of "nowhere", so to make selser work
					// properly, add a zero-sized DSR pointing to the end of the document.
					$content = $extApi->getPageConfig()->getRevisionContent()->getContent( 'main' );
					$contentLength = strlen( $content );
					$dp->dsr = new DomSourceRange( $contentLength, $contentLength, 0, 0 );
				},
				true
			);

			// Add a \n before the <ol> so that when serialized to wikitext,
			// each <references /> tag appears on its own line.
			$node->appendChild( $doc->createTextNode( "\n" ) );
			$node->appendChild( $frag );

			self::insertReferencesIntoDOM( $extApi, $frag, $refsData, true );
		}
	}

	/**
	 * @param ParsoidExtensionAPI $extApi
	 * @param ReferencesData $refsData
	 * @param string $str
	 * @return string
	 */
	private static function processEmbeddedRefs(
		ParsoidExtensionAPI $extApi, ReferencesData $refsData, string $str
	): string {
		$domFragment = $extApi->htmlToDom( $str );
		self::processRefs( $extApi, $refsData, $domFragment );
		return $extApi->domToHtml( $domFragment, true, true );
	}

	/**
	 * @param ParsoidExtensionAPI $extApi
	 * @param ReferencesData $refsData
	 * @param DOMNode $node
	 */
	public static function processRefs(
		ParsoidExtensionAPI $extApi, ReferencesData $refsData, DOMNode $node
	): void {
		$child = $node->firstChild;
		while ( $child !== null ) {
			$nextChild = $child->nextSibling;
			if ( $child instanceof DOMElement ) {
				if ( WTUtils::isSealedFragmentOfType( $child, 'ref' ) ) {
					self::extractRefFromNode( $extApi, $child, $refsData );
				} elseif ( DOMUtils::hasTypeOf( $child, 'mw:Extension/references' ) ) {
					$referencesGroup = DOMDataUtils::getDataParsoid( $child )->group ?? null;
					$refsData->pushInEmbeddedContent();
					self::processRefsInReferences(
						$extApi,
						$refsData,
						$child,
						$referencesGroup
					);
					$refsData->popInEmbeddedContent();
					self::insertReferencesIntoDOM( $extApi, $child, $refsData, false );
				} else {
					$refsData->pushInEmbeddedContent();
					// Look for <ref>s embedded in data attributes
					$extApi->processHiddenHTMLInDataAttributes( $child,
						function ( string $html ) use ( $extApi, $refsData ) {
							return self::processEmbeddedRefs( $extApi, $refsData, $html );
						}
					);
					$refsData->popInEmbeddedContent();

					if ( $child->hasChildNodes() ) {
						self::processRefs( $extApi, $refsData, $child );
					}
				}
			}
			$child = $nextChild;
		}
	}

	/**
	 * This handles wikitext like this:
	 * ```
	 *   <references> <ref>foo</ref> </references>
	 *   <references> <ref>bar</ref> </references>
	 * ```
	 *
	 * @param ParsoidExtensionAPI $extApi
	 * @param ReferencesData $refsData
	 * @param DOMElement $node
	 * @param ?string $referencesGroup
	 */
	private static function processRefsInReferences(
		ParsoidExtensionAPI $extApi, ReferencesData $refsData,
		DOMElement $node, ?string $referencesGroup
	): void {
		$child = $node->firstChild;
		while ( $child !== null ) {
			$nextChild = $child->nextSibling;
			if ( $child instanceof DOMElement ) {
				if ( WTUtils::isSealedFragmentOfType( $child, 'ref' ) ) {
					self::extractRefFromNode(
						$extApi,
						$child,
						$refsData,
						$referencesGroup
					);
				} elseif ( $child->hasChildNodes() ) {
					self::processRefsInReferences(
						$extApi,
						$refsData,
						$child,
						$referencesGroup
					);
				}
			}
			$child = $nextChild;
		}
	}

	/**
	 * Traverse into all the embedded content and mark up the refs in there
	 * that have errors that weren't known before the content was serialized.
	 *
	 * Some errors are only known at the time when we're inserting the
	 * references lists, at which point, embedded content has already been
	 * serialized and stored, so we no longer have live access to it.  We
	 * therefore map about ids to errors for a ref at that time, and then do
	 * one final walk of the dom to peak into all the embedded content and
	 * mark up the errors where necessary.
	 *
	 * @param ParsoidExtensionAPI $extApi
	 * @param ReferencesData $refsData
	 * @param DOMNode $node
	 */
	public static function addEmbeddedErrors(
		ParsoidExtensionAPI $extApi, ReferencesData $refsData, DOMNode $node
	): void {
		$processEmbeddedErrors = function ( string $html ) use ( $extApi, $refsData ) {
			// Similar to processEmbeddedRefs
			$domFragment = $extApi->htmlToDom( $html );
			self::addEmbeddedErrors( $extApi, $refsData, $domFragment );
			return $extApi->domToHtml( $domFragment, true, true );
		};
		$processBodyHtml = function ( DOMElement $n ) use ( $processEmbeddedErrors ) {
			$dataMw = DOMDataUtils::getDataMw( $n );
			if ( is_string( $dataMw->body->html ?? null ) ) {
				$dataMw->body->html = $processEmbeddedErrors(
					$dataMw->body->html
				);
			}
		};
		$child = $node->firstChild;
		while ( $child !== null ) {
			$nextChild = $child->nextSibling;
			if ( $child instanceof DOMElement ) {
				if ( DOMUtils::hasTypeOf( $child, 'mw:Extension/ref' ) ) {
					$processBodyHtml( $child );
					$about = $child->getAttribute( 'about' ) ?? '';
					$errs = $refsData->embeddedErrors[$about] ?? null;
					if ( $errs ) {
						self::addErrorsToNode( $child, $errs );
					}
				} elseif ( DOMUtils::hasTypeOf( $child, 'mw:Extension/references' ) ) {
					$processBodyHtml( $child );
				} else {
					$extApi->processHiddenHTMLInDataAttributes(
						$child, $processEmbeddedErrors
					);
				}
				if ( $child->hasChildNodes() ) {
					self::addEmbeddedErrors( $extApi, $refsData, $child );
				}
			}
			$child = $nextChild;
		}
	}

	/** @inheritDoc */
	public function sourceToDom(
		ParsoidExtensionAPI $extApi, string $txt, array $extArgs
	): DOMDocumentFragment {
		$domFragment = $extApi->extTagToDOM(
			$extArgs,
			'',
			$txt,
			[
				'wrapperTag' => 'div',
				'parseOpts' => [ 'extTag' => 'references' ],
			]
		);

		$refsOpts = $extApi->extArgsToArray( $extArgs ) + [
			'group' => null,
			'responsive' => null,
		];

		$frag = self::createReferences(
			$extApi,
			$domFragment,
			$refsOpts,
			function ( $dp ) use ( $extApi ) {
				$dp->src = $extApi->extTag->getSource();
				// Setting redundant info on fragment.
				// $docBody->firstChild info feels cumbersome to use downstream.
				if ( $extApi->extTag->isSelfClosed() ) {
					$dp->selfClose = true;
				}
			}
		);
		$domFragment->appendChild( $frag );
		return $domFragment;
	}

	/** @inheritDoc */
	public function domToWikitext(
		ParsoidExtensionAPI $extApi, DOMElement $node, bool $wrapperUnmodified
	) {
		$dataMw = DOMDataUtils::getDataMw( $node );
		if ( !empty( $dataMw->autoGenerated ) && ( ( $dataMw->attrs->group ?? '' ) !== ''
			|| $extApi->rtTestMode() ) ) {
			// Eliminate auto-inserted <references /> noise in rt-testing
			return '';
		} else {
			$startTagSrc = $extApi->extStartTagToWikitext( $node );
			if ( empty( $dataMw->body ) ) {
				return $startTagSrc; // We self-closed this already.
			} else {
				if ( is_string( $dataMw->body->html ) ) {
					$src = $extApi->htmlToWikitext(
						[ 'extName' => $dataMw->name ],
						$dataMw->body->html
					);
					return $startTagSrc . $src . '</' . $dataMw->name . '>';
				} else {
					$extApi->log( 'error',
						'References body unavailable for: ' . DOMCompat::getOuterHTML( $node )
					);
					return ''; // Drop it!
				}
			}
		}
	}

	/** @inheritDoc */
	public function lintHandler(
		ParsoidExtensionAPI $extApi, DOMElement $refs, callable $defaultHandler
	): ?DOMNode {
		// Nothing to do
		//
		// FIXME: Not entirely true for scenarios where the <ref> tags
		// are defined in the references section that is itself templated.
		//
		// {{1x|<references>\n<ref name='x'><b>foo</ref>\n</references>}}
		//
		// In this example, the references tag has the right tplInfo and
		// when the <ref> tag is processed in the body of the article where
		// it is accessed, there is no relevant template or dsr info available.
		//
		// Ignoring for now.
		return $refs->nextSibling;
	}
}
