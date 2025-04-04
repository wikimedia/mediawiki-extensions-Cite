<?php
declare( strict_types = 1 );
// phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

namespace Cite\Parsoid;

use Cite\MarkSymbolRenderer;
use Cite\Validator;
use MediaWiki\Config\Config;
use MediaWiki\Html\HtmlHelper;
use MediaWiki\MediaWikiServices;
use stdClass;
use Wikimedia\Message\MessageValue;
use Wikimedia\Parsoid\Core\DomSourceRange;
use Wikimedia\Parsoid\DOM\Document;
use Wikimedia\Parsoid\DOM\DocumentFragment;
use Wikimedia\Parsoid\DOM\Element;
use Wikimedia\Parsoid\DOM\Node;
use Wikimedia\Parsoid\Ext\DOMDataUtils;
use Wikimedia\Parsoid\Ext\DOMUtils;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\Ext\PHPUtils;
use Wikimedia\Parsoid\Ext\WTUtils;
use Wikimedia\Parsoid\NodeData\DataMw;
use Wikimedia\Parsoid\NodeData\DataMwBody;
use Wikimedia\Parsoid\NodeData\DataMwError;
use Wikimedia\Parsoid\NodeData\DataParsoid;
use Wikimedia\Parsoid\Utils\DOMCompat;
use Wikimedia\RemexHtml\HTMLData;
use Wikimedia\RemexHtml\Serializer\SerializerNode;

/**
 * @license GPL-2.0-or-later
 */
class References {
	private Config $mainConfig;
	private bool $isSubreferenceSupported;
	private MarkSymbolRenderer $markSymbolRenderer;
	private ParsoidValidator $validator;

	public function __construct( Config $mainConfig ) {
		$this->mainConfig = $mainConfig;
		$this->isSubreferenceSupported = $mainConfig->get( 'CiteSubReferencing' );

		$this->markSymbolRenderer = MediaWikiServices::getInstance()->getService( 'Cite.MarkSymbolRenderer' );
		$this->validator = new ParsoidValidator( $this->isSubreferenceSupported );
	}

	private static function hasRef( ParsoidExtensionAPI $extApi, Node $node ): bool {
		$c = $node->firstChild;
		while ( $c ) {
			if ( $c instanceof Element ) {
				if ( WTUtils::isSealedFragmentOfType( $c, 'ref' ) ) {
					return true;
				}
				$hasEmbeddedRefs = false;
				$extApi->processAttributeEmbeddedDom( $c,
					function ( DocumentFragment $df ) use ( $extApi, &$hasEmbeddedRefs ) {
						$hasEmbeddedRefs = $hasEmbeddedRefs || self::hasRef( $extApi, $df );
						return true;
					}
				);
				if ( $hasEmbeddedRefs ) {
					return true;
				}
				if ( self::hasRef( $extApi, $c ) ) {
					return true;
				}
			}
			$c = $c->nextSibling;
		}
		return false;
	}

	public function createEmptyReferenceListFragment(
		ParsoidExtensionAPI $extApi, DocumentFragment $domFragment,
		array $refsOpts, ?callable $modifyDp, bool $autoGenerated = false
	): Element {
		$doc = $domFragment->ownerDocument;

		$ol = $doc->createElement( 'ol' );
		DOMCompat::getClassList( $ol )->add( 'mw-references', 'references' );

		DOMUtils::migrateChildren( $domFragment, $ol );

		// Support the `responsive` parameter
		if ( $refsOpts['responsive'] !== null ) {
			$responsiveWrap = $refsOpts['responsive'] !== '0';
		} else {
			$responsiveWrap = (bool)$this->mainConfig->get( 'CiteResponsiveReferences' );
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
			$dataMw = new DataMw( [
				'name' => 'references',
				'attrs' => new stdClass
			] );
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

		// These module namess are copied from Cite extension.
		// They are hardcoded there as well.
		$metadata = $extApi->getMetadata();
		$metadata->addModules( [ 'ext.cite.ux-enhancements' ] );
		$metadata->addModuleStyles( [ 'ext.cite.parsoid.styles', 'ext.cite.styles' ] );

		return $frag;
	}

	private function processNestedRefInRef(
		ParsoidExtensionAPI $extApi,
		Element $refFragment,
		ReferencesData $referencesData,
		bool $hasDifferingHtml
	): ?string {
		$refFragmentDp = DOMDataUtils::getDataParsoid( $refFragment );
		if ( !empty( $refFragmentDp->empty ) || !self::hasRef( $extApi, $refFragment ) ) {
			return null;
		}

		if ( $hasDifferingHtml ) {
			$referencesData->pushEmbeddedContentFlag();
		}

		// This prevents nested list-defined references from erroneously giving "group mismatch"
		// errors.
		$referencesData->incrementRefDepth();
		$this->processRefs( $extApi, $referencesData, $refFragment );
		$referencesData->decrementRefDepth();

		if ( $hasDifferingHtml ) {
			$referencesData->popEmbeddedContentFlag();
			// If we have refs and the content differs, we need to reserialize now that we processed
			// the refs.  Unfortunately, the cachedHtml we compared against already had its refs
			// processed so that would presumably never match and this will always be considered a
			// redefinition.  The implementation for the legacy parser also considers this a
			// redefinition so there is likely little content out there like this :)
			return $extApi->domToHtml( $refFragment, true, true );
		}
		return null;
	}

	private function extractRefFromNode(
		ParsoidExtensionAPI $extApi, Element $node, ReferencesData $referencesData
	): void {
		$doc = $node->ownerDocument;
		$errs = [];

		// This is data-parsoid from the dom fragment node that's gone through
		// DomSourceRange computation and template wrapping.
		$nodeDp = DOMDataUtils::getDataParsoid( $node );
		$contentId = $nodeDp->html;

		// Extract the ref fragment and ensure it's valid
		$refFragment = $extApi->getContentDOM( $contentId )->firstChild;
		DOMUtils::assertElt( $refFragment );
		$refFragmentDp = DOMDataUtils::getDataParsoid( $refFragment );
		$refDataMw = DOMDataUtils::getDataMw( $refFragment );

		// Validate attribute keys
		$status = Validator::filterRefArguments( (array)$refDataMw->attrs, $this->isSubreferenceSupported );
		$arguments = $status->getValue();
		if ( !$status->isGood() ) {
			$errs[] = new DataMwError( 'cite_error_ref_too_many_keys' );
		}

		// Extract and validate attribute values
		$refName = (string)$arguments['name'];
		$followName = (string)$arguments['follow'];
		$refDir = strtolower( (string)$arguments['dir'] );
		$details = $arguments['details'] ?? '';

		// Validate the reference group
		$groupName = $arguments['group'] ?? ( $referencesData->inRefContent() ? '' : $referencesData->referencesGroup );
		$groupErrorMessage = $this->validator->validateGroup( $groupName, $referencesData );
		if ( $groupErrorMessage ) {
			$errs[] = $groupErrorMessage;
		}
		$refGroup = $referencesData->getOrCreateRefGroup( $groupName );

		// Handle 'about' attribute with priority since it's
		// only added when the wrapper is a template sibling.
		$about = DOMCompat::getAttribute( $node, 'about' ) ??
			DOMCompat::getAttribute( $refFragment, 'about' );
		'@phan-var string $about'; // assert that $about is non-null

		$hasDetails = $details !== '';

		// Handle error cases for the attributes 'name' and 'follow'
		if ( $refName && $followName ) {
			$errs[] = new DataMwError( 'cite_error_ref_follow_conflicts' );
		}
		if (
			!$followName &&
			!$refName &&
			!$referencesData->inRefContent() &&
			$referencesData->inReferencesContent()
		) {
			$errs[] = new DataMwError( 'cite_error_references_no_key' );
		}

		$refFragmentHtml = '';
		$hasDifferingHtml = false;
		$hasDifferingContent = false;
		$hasValidFollow = false;

		// Wrap the attribute 'follow'
		if ( $followName ) {
			$followSpan = $this->wrapFollower( $doc, $refFragment );
			$followSpan->setAttribute( 'about', $about );
			$refFragment->appendChild( $followSpan );
		}

		$ref = $referencesData->lookupRefByName( $refGroup, $followName ) ??
			$referencesData->lookupRefByName( $refGroup, $refName );
		// Handle the attributes 'name' and 'follow'
		if ( $refName ) {
			$nameErrorMessage = $this->validator->validateName( $refName, $refGroup, $referencesData );
			if ( $nameErrorMessage ) {
				$errs[] = $nameErrorMessage;
			} elseif ( $ref ) {
				// If there are multiple <ref>s with the same name, but different content,
				// the content of the first <ref> shows up in the <references> section.
				// in order to ensure lossless RT-ing for later <refs>, we have to record
				// HTML inline for all of them.
				if ( $ref->contentId ) {
					if ( $ref->cachedHtml === null ) {
						// @phan-suppress-next-line PhanTypeMismatchArgumentNullable False positive
						$refContent = $extApi->getContentDOM( $ref->contentId )->firstChild;
						$ref->cachedHtml = $this->normalizeRef( $extApi->domToHtml( $refContent, true, false ) );
					}
					$refFragmentHtml = $extApi->domToHtml( $refFragment, true, false );
					$hasDifferingHtml = ( $this->normalizeRef( $refFragmentHtml ) !== $ref->cachedHtml );
					if ( $hasDifferingHtml ) {
						$hasDifferingContent = $this->normalizeRef( $refFragmentHtml, true ) !==
							$this->normalizeRef( $ref->cachedHtml, true );
					}
				}
			}
		} else {
			if ( $followName ) {
				// Check that the followed ref exists
				$followErrorMessage = $this->validator->validateFollow( $followName, $refGroup );
				if ( $followErrorMessage ) {
					$errs[] = $followErrorMessage;
				} else {
					$hasValidFollow = true;
					$ref = $refGroup->indexByName[$followName];
				}
			}
		}

		// Split subref and main ref; add main ref as a list-defined reference
		if ( $hasDetails && $refName ) {
			if ( isset( $refDataMw->body ) ) {
				// Main + details.

				if ( $ref ) {
					// Already have the main ref.
					// TODO in T390992: Parsoid should detect conflicting main ref content in main+details

					// @phan-suppress-next-line PhanUndeclaredProperty
					$refDataMw->isMainRefBodyWithDetails = '1';
				} else {
					// Create a main ref and transfer the tag body to it,
					$mainRef = $referencesData->add( $groupName, $refName, $refDir );
					$mainRef->isMainWithDetails = true;

					// @phan-suppress-next-line PhanUndeclaredProperty
					$refDataMw->mainBody = $mainRef->noteId;

					if ( $contentId ) {
						$mainRef->contentId = $contentId;
						// Flag to help reserialize main ref content into the subref when saving.
						// @phan-suppress-next-line PhanUndeclaredProperty
						$refDataMw->isMainRefBodyWithDetails = '1';
					}
				}
			} else {
				// Standalone subref

				if ( !$ref ) {
					// Create new, empty main ref
					$referencesData->add( $groupName, $refName, $refDir );
				}

				// FIXME: Shouldn't have been set to true above.
				$hasDifferingHtml = false;
			}

			// Switch $ref to a newly-created subref
			$ref = $referencesData->addSubref( $groupName, $refName, $refDir );

			// Move content to main ref.
			$contentId = null;
			$ref->contentId = null;

			// Move details attribute into subref content.
			$ref->externalFragment = $extApi->wikitextToDOM( $details, [
				'processInNewFrame' => true,
				'parseOpts' => [ 'context' => 'inline' ]
			], true );
			$refFragmentHtml = '';
			// Subref points to the main ref by name.
			// FIXME: should have already asserted that refName exists for all details, see T387193
			unset( $refDataMw->attrs->name );
			// @phan-suppress-next-line PhanUndeclaredProperty
			$refDataMw->mainRef = $refName;
			$refName = '';
		}

		$refFragmentHtml = $this->processNestedRefInRef( $extApi, $refFragment, $referencesData,
			$hasDifferingHtml ) ?? $refFragmentHtml;

		if ( $this->isNestedInSupWithSameGroupAndName( $node, $groupName, $refName ) ) {
			$errs[] = new DataMwError( 'cite_error_included_ref' );
			ErrorUtils::addErrorsToNode( $node, $errs );
		}

		// Add ref-index linkback
		$linkBackSup = $doc->createElement( 'sup' );

		$isVisibleNode = false;

		if ( $hasValidFollow ) {
			// Migrate content from the follow to the ref
			if ( $ref->contentId ) {
				$refContent = $extApi->getContentDOM( $ref->contentId )->firstChild;
				DOMUtils::migrateChildren( $refFragment, $refContent );
			} else {
				// Otherwise, we have a follow that comes after a named
				// ref without content so use the follow fragment as
				// the content
				// This will be set below with `$ref->contentId = $contentId;`
			}
		} else {
			// If we have !$ref, one might have been added in the call to
			// processRefs, ie. a self-referential ref.  We could try to look
			// it up again, but Parsoid is choosing not to support that.
			// Even worse would be if it tried to redefine itself!

			if ( !$ref ) {
				$ref = $referencesData->add( $groupName, $refName, $refDir );
			}

			// Handle linkbacks
			if ( $referencesData->inEmbeddedContent() ) {
				$ref->embeddedNodes[] = $about;
				// While indicator content is embedded throughout the parsing
				// pipeline, it gets added back into the page in a post-processing
				// step, so consider it visible for the sake of linkbacks.
				if ( $referencesData->peekEmbeddedContentFlag() === 'indicator' ) {
					$ref->visibleNodes++;
					$isVisibleNode = true;
				}
			} else {
				$ref->nodes[] = $linkBackSup;
				$ref->visibleNodes++;
				$isVisibleNode = true;
			}
		}
		// Guaranteed from this point on
		'@phan-var RefGroupItem $ref';

		if ( isset( $arguments['dir'] ) ) {
			$dirError = $this->validator->validateDir( $refDir, $ref );
			if ( $dirError ) {
				$errs[] = $dirError;
			}
		}

		// FIXME: At some point this error message can be changed to a warning, as Parsoid Cite now
		// supports numerals as a name without it being an actual error, but core Cite does not.
		// Follow refs do not duplicate the error which can be correlated with the original ref.
		if ( ctype_digit( $refName ) ) {
			$errs[] = new DataMwError( 'cite_error_ref_numeric_key' );
		}

		// Check for missing content, added ?? '' to fix T259676 crasher
		// FIXME: See T260082 for a more complete description of cause and deeper fix
		$hasMissingContent = ( !empty( $refFragmentDp->empty ) || trim( $refDataMw->body->extsrc ?? '' ) === '' ) &&
			 $ref->externalFragment === null;

		if ( $hasMissingContent ) {
			// Check for missing name and content to generate error code
			//
			// In references content, refs should be used for definition so missing content
			// is an error.  It's possible that no name is present (!hasRefName), which also
			// gets the error "cite_error_references_no_key" above, so protect against that.
			if ( $referencesData->inReferencesContent() ) {
				$errs[] = new DataMwError(
					'cite_error_empty_references_define',
					[ $refName, $groupName ]
				);
			} elseif ( !$refName ) {
				if ( !empty( $refFragmentDp->selfClose ) ) {
					$errs[] = new DataMwError( 'cite_error_ref_no_key' );
				} else {
					$errs[] = new DataMwError( 'cite_error_ref_no_input' );
				}
			}

			if ( !empty( $refFragmentDp->selfClose ) ) {
				unset( $refDataMw->body );
			} else {
				// Empty the <sup> since we've serialized its children and
				// removing it below asserts everything has been migrated out
				DOMCompat::replaceChildren( $refFragment );
				$refDataMw->body = DataMwBody::new( [
					'html' => $refDataMw->body->extsrc ?? '',
				] );
			}
		} else {
			if ( $ref->contentId && !$hasValidFollow ) {
				// Empty the <sup> since we've serialized its children and
				// removing it below asserts everything has been migrated out
				DOMCompat::replaceChildren( $refFragment );
			}
			if ( $hasDifferingHtml ) {
				if ( $refFragmentHtml != '' && $hasDifferingContent ) {
					$errs[] = new DataMwError( 'cite_error_references_duplicate_key', [ $refName ] );
				}
				$refDataMw->body = DataMwBody::new( [
					'html' => $refFragmentHtml,
				] );
			} else {
				$refDataMw->body = DataMwBody::new( [
					'id' => 'mw-reference-text-' . $ref->noteId,
				] );
			}
		}

		$this->addLinkBackAttributes(
			$linkBackSup,
			$isVisibleNode ? $this->getLinkbackId( $ref ) : null,
			DOMCompat::getAttribute( $node, 'typeof' ),
			$about,
			$hasValidFollow
		);

		$this->addLinkBackData(
			$linkBackSup,
			$nodeDp,
			DOMUtils::hasTypeOf( $node, 'mw:Transclusion' ) ?
				DOMDataUtils::getDataMw( $node ) :
				$refDataMw
		);

		// FIXME(T214241): Should the errors be added to data-mw if
		// $isTplWrapper?  Here and other calls to addErrorsToNode.
		ErrorUtils::addErrorsToNode( $linkBackSup, $errs );

		// refLink is the link to the citation
		$refLink = $doc->createElement( 'a' );
		DOMUtils::addAttributes( $refLink, [
			'href' => $extApi->getPageUri() . '#' . $ref->noteId,
			'style' => 'counter-reset: mw-Ref ' . $ref->numberInGroup . ';',
		] );
		if ( $ref->group ) {
			$refLink->setAttribute( 'data-mw-group', $ref->group );
		}

		// refLink-span which will contain a default rendering of the cite link
		// for browsers that don't support counters
		$refLinkSpan = $doc->createElement( 'span' );
		$refLinkSpan->setAttribute( 'class', 'mw-reflink-text' );
		$openBracket = $doc->createElement( 'span', '[' );
		$openBracket->setAttribute( 'class', 'cite-bracket' );
		$refLinkSpan->appendChild( $openBracket );
		$refLinkSpan->appendChild( $doc->createTextNode(
			$this->markSymbolRenderer->makeLabel( $ref->group, $ref->numberInGroup, $ref->subrefIndex )
		) );
		$closeBracket = $doc->createElement( 'span', ']' );
		$closeBracket->setAttribute( 'class', 'cite-bracket' );
		$refLinkSpan->appendChild( $closeBracket );

		$refLink->appendChild( $refLinkSpan );
		$linkBackSup->appendChild( $refLink );

		// Checking if the <ref> is nested in a link
		$aParent = DOMUtils::findAncestorOfName( $node, 'a' );
		if ( $aParent ) {
			$this->insertChildOutsideOfLink( $linkBackSup, $aParent );
			// set misnested to true and DSR to zero-sized to avoid round-tripping issues
			$dsrOffset = DOMDataUtils::getDataParsoid( $aParent )->dsr->end ?? null;
			// we created that node hierarchy above, so we know that it only contains these nodes,
			// hence there's no need for a visitor
			self::setMisnested( $linkBackSup, $dsrOffset );
			self::setMisnested( $refLink, $dsrOffset );
			self::setMisnested( $refLinkSpan, $dsrOffset );
			$node->parentNode->removeChild( $node );
		} else {
			// if not, we insert it where we planned in the first place
			$node->parentNode->replaceChild( $linkBackSup, $node );
		}

		// Keep the first content to compare multiple <ref>s with the same name.
		if ( $ref->contentId === null && !$hasMissingContent ) {
			$ref->contentId = $contentId;
			// Use the dir parameter only from the full definition of a named ref tag
			$ref->dir = $refDir;
		} elseif ( $contentId ) {
			DOMCompat::remove( $refFragment );
			$extApi->clearContentDOM( $contentId );
		}
	}

	private function insertChildOutsideOfLink( Element $linkBackSup, Element $aParent ): void {
		// If we find a parent link, we hoist the reference up, just after the link
		// But if there's multiple references in a single link, we want to insert in order -
		// so we look for other misnested references before inserting
		$insertionPoint = $aParent->nextSibling;
		while ( $insertionPoint instanceof Element &&
			DOMCompat::nodeName( $insertionPoint ) === 'sup' &&
			!empty( DOMDataUtils::getDataParsoid( $insertionPoint )->misnested )
		) {
			$insertionPoint = $insertionPoint->nextSibling;
		}
		$aParent->parentNode->insertBefore( $linkBackSup, $insertionPoint );

		// Mark the two now un-nested elements as a DOM forest that belongs together
		$parentAbout = DOMCompat::getAttribute( $aParent, 'about' );
		if ( $parentAbout !== null ) {
			$linkBackSup->setAttribute( 'about', $parentAbout );
		}
	}

	private function isNestedInSupWithSameGroupAndName( Element $node, string $groupName, string $refName ): bool {
		// Nothing to compare with
		if ( !$refName ) {
			return false;
		}

		$supNode = DOMUtils::findAncestorOfName( $node, 'sup' );
		while ( $supNode ) {
			$dataMw = DOMDataUtils::getDataMw( $supNode );
			if ( $dataMw &&
				( $dataMw->attrs->group ?? '' ) === $groupName &&
				( $dataMw->attrs->name ?? '' ) === $refName
			) {
				return true;
			}
			$supNode = DOMUtils::findAncestorOfName( $supNode, 'sup' );
		}
		return false;
	}

	/**
	 * wrap the content of the follow attribute
	 * so that there is no ambiguity
	 * where to find it when round tripping
	 */
	private function wrapFollower( Document $doc, Node $refFragment ): Element {
		$followSpan = $doc->createElement( 'span' );
		DOMUtils::addTypeOf( $followSpan, 'mw:Cite/Follow' );

		$followSpan->appendChild(
			$doc->createTextNode( ' ' )
		);
		DOMUtils::migrateChildren( $refFragment, $followSpan );

		return $followSpan;
	}

	/**
	 * Sets a node as misnested and its DSR as zero-width.
	 */
	private static function setMisnested( Element $node, ?int $offset ) {
		$dataParsoid = DOMDataUtils::getDataParsoid( $node );
		$dataParsoid->misnested = true;
		$dataParsoid->dsr = new DomSourceRange( $offset, $offset, null, null );
	}

	public function insertReferencesIntoDOM(
		ParsoidExtensionAPI $extApi, Element $refsNode,
		ReferencesData $refsData, bool $autoGenerated = false
	): void {
		$isTemplateWrapper = DOMUtils::hasTypeOf( $refsNode, 'mw:Transclusion' );
		$nodeDp = DOMDataUtils::getDataParsoid( $refsNode );
		$groupName = $nodeDp->group ?? '';
		$refGroup = $refsData->lookupRefGroup( $groupName );

		// Iterate through the ref list to back-patch typeof and data-mw error
		// information into ref for errors only known at time of references
		// insertion.  Refs in the top level dom will be processed immediately,
		// whereas embedded refs will be gathered for batch processing, since
		// we need to parse embedded content to find them.
		if ( $refGroup ) {
			foreach ( $refGroup->refs as $ref ) {
				$errs = [];
				// Mark all refs that are named without content
				if ( $ref->name !== null && $ref->contentId === null ) {
					// TODO: Since this error is being placed on the ref,
					// the key should arguably be "cite_error_ref_no_text"
					$errs[] = new DataMwError(
						'cite_error_references_no_text',
						[ $ref->name ]
					);
				}
				if ( $errs ) {
					foreach ( $ref->nodes as $node ) {
						ErrorUtils::addErrorsToNode( $node, $errs );
					}
					foreach ( $ref->embeddedNodes as $about ) {
						$refsData->embeddedErrors[$about] = $errs;
					}
				}
			}
		}

		// Note that `$sup`s here are probably all we really need to check for
		// errors caught with `$refsData->inReferencesContent()` but it's
		// probably easier to just know that state while they're being
		// constructed.
		$nestedRefsHTML = array_map(
			static fn ( Element $sup ) => $extApi->domToHtml( $sup, false, true ) . "\n",
			PHPUtils::iterable_to_array( DOMCompat::querySelectorAll(
				$refsNode, 'sup[typeof~=\'mw:Extension/ref\']'
			) )
		);

		// Any main+details refs should have the main part appear in the
		// reference list.
		// TODO: clean up the various use cases for this: eg. don't add
		// the main ref if it's already present.
		if ( $refGroup ) {
			$doc = $refsNode->ownerDocument;
			foreach ( $refGroup->refs as $ref ) {
				if ( $ref->isMainWithDetails && $ref->contentId ) {
					$sup = $doc->createElement( 'sup' );
					DOMUtils::addAttributes( $sup, [
						'typeof' => 'mw:Extension/ref',
					] );
					$dataMw = new DataMw( [
						'name' => 'ref',
						'attrs' => (object)[
							'name' => $ref->name,
							'group' => $ref->group,
						],
						'body' => (object)[
							'id' => 'mw-reference-text-' . $ref->noteId,
						],
						'isMainWithDetails' => '1',
					] );
					DOMDataUtils::setDataMw( $sup, $dataMw );
					$refFragment = $extApi->getContentDOM( $ref->contentId )->firstChild->firstChild;
					if ( $refFragment ) {
						DOMCompat::setInnerHTML( $sup, $extApi->domToHtml( $refFragment, false, false ) );
						$nestedRefsHTML[] = $extApi->domToHtml( $sup, false, false );
					}
				}
			}
		}

		if ( !$isTemplateWrapper ) {
			$dataMw = DOMDataUtils::getDataMw( $refsNode );
			// Mark this auto-generated so that we can skip this during
			// html -> wt and so that clients can strip it if necessary.
			if ( $autoGenerated ) {
				$dataMw->autoGenerated = true;
			}
			if ( $nestedRefsHTML ) {
				$dataMw->body = DataMwBody::new( [
					'html' => "\n" . implode( $nestedRefsHTML ),
				] );
			} elseif ( !$autoGenerated && empty( $nodeDp->selfClose ) ) {
				$dataMw->body = DataMwBody::new( [
					'html' => '',
				] );
			} else {
				unset( $dataMw->body );
			}
			unset( $nodeDp->selfClose );
		}

		$hasResponsiveWrapper = false;
		// Deal with responsive wrapper
		if ( DOMUtils::hasClass( $refsNode, 'mw-references-wrap' ) ) {
			// NOTE: The default Cite implementation hardcodes this threshold to 10.
			// We use a configurable parameter here primarily for test coverage purposes.
			// See citeParserTests.txt where we set a threshold of 1 or 2.
			$rrThreshold = $this->mainConfig->get( 'CiteResponsiveReferencesThreshold' ) ?? 10;
			if ( $refGroup && count( $refGroup->refs ) > $rrThreshold ) {
				DOMCompat::getClassList( $refsNode )->add( 'mw-references-columns' );
			}
			$refsNode = $refsNode->firstChild;
			$hasResponsiveWrapper = true;
		}
		DOMUtils::assertElt( $refsNode );

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
			if ( $autoGenerated && $groupName !== '' ) {
				$errorUtils = new ErrorUtils( $extApi );
				$frag = $errorUtils->renderParsoidError(
					new MessageValue( 'cite_error_group_refs_without_references', [ $groupName ] ) );
				$span = DOMCompat::getFirstElementChild( $frag );
				$refsNode->parentNode->insertBefore( $span, $refsNode->nextSibling );
				$refsNodeAbout = DOMCompat::getAttribute( $refsNode, 'about' );
				if ( $refsNodeAbout ) {
					$span->setAttribute( 'about', $refsNodeAbout );
				}

				$destNode = $hasResponsiveWrapper ? DOMCompat::getParentElement( $refsNode ) : $refsNode;
				ErrorUtils::addErrorsToNode( $destNode,
					[ new DataMwError( 'cite_error_group_refs_without_references', [ $groupName ] )
				] );
			}
		}

		// Remove the group from refsData
		$refsData->removeRefGroup( $groupName );
	}

	public function processRefs(
		ParsoidExtensionAPI $extApi, ReferencesData $refsData, Node $node
	): void {
		$child = $node->firstChild;
		while ( $child !== null ) {
			$nextChild = $child->nextSibling;
			if ( $child instanceof Element ) {
				if ( WTUtils::isSealedFragmentOfType( $child, 'ref' ) ) {
					$this->extractRefFromNode( $extApi, $child, $refsData );
				} elseif ( DOMUtils::hasTypeOf( $child, 'mw:Extension/references' ) ) {
					if ( !$refsData->inReferencesContent() ) {
						$refsData->referencesGroup =
							DOMDataUtils::getDataParsoid( $child )->group ?? '';
					}
					$refsData->pushEmbeddedContentFlag( 'references' );
					if ( $child->hasChildNodes() ) {
						$this->processRefs( $extApi, $refsData, $child );
					}
					$refsData->popEmbeddedContentFlag();
					if ( !$refsData->inReferencesContent() ) {
						$refsData->referencesGroup = '';
						$this->insertReferencesIntoDOM( $extApi, $child, $refsData, false );
					}
				} else {
					if ( DOMUtils::hasTypeOf( $child, 'mw:Extension/indicator' ) ) {
						$refsData->pushEmbeddedContentFlag( 'indicator' );
					} else {
						$refsData->pushEmbeddedContentFlag();
					}
					// Look for <ref>s embedded in data attributes
					$extApi->processAttributeEmbeddedDom( $child,
						function ( DocumentFragment $df ) use ( $extApi, $refsData ) {
							$this->processRefs( $extApi, $refsData, $df );
							return true;
						}
					);
					$refsData->popEmbeddedContentFlag();
					if ( $child->hasChildNodes() ) {
						$this->processRefs( $extApi, $refsData, $child );
					}
				}
			}
			$child = $nextChild;
		}
	}

	private function addLinkBackData(
		Element $linkBackSup,
		DataParsoid $nodeDp,
		?DataMw $dataMw
	): void {
		$dataParsoid = new DataParsoid();
		if ( isset( $nodeDp->src ) ) {
			$dataParsoid->src = $nodeDp->src;
		}
		if ( isset( $nodeDp->dsr ) ) {
			$dataParsoid->dsr = $nodeDp->dsr;
		}
		if ( isset( $nodeDp->pi ) ) {
			$dataParsoid->pi = $nodeDp->pi;
		}
		DOMDataUtils::setDataParsoid( $linkBackSup, $dataParsoid );
		DOMDataUtils::setDataMw( $linkBackSup, $dataMw );
	}

	private function addLinkBackAttributes(
		Element $linkBackSup,
		?string $id,
		?string $typeof,
		?string $about,
		bool $hasValidFollow
	): void {
		$class = 'mw-ref reference';
		if ( $hasValidFollow ) {
			$class .= ' mw-ref-follow';
		}

		DOMUtils::addAttributes( $linkBackSup, [
			'about' => $about,
			'class' => $class,
			'id' => $hasValidFollow ? null : $id,
			'rel' => 'dc:references',
			'typeof' => $typeof,
		] );
		DOMUtils::removeTypeOf( $linkBackSup, 'mw:DOMFragment/sealed/ref' );
		DOMUtils::addTypeOf( $linkBackSup, 'mw:Extension/ref' );
	}

	private function getLinkbackId( RefGroupItem $ref ): string {
		$lb = $ref->backLinkIdBase;
		if ( $ref->name !== null ) {
			$lb .= '-' . ( $ref->visibleNodes - 1 );
		}
		return $lb;
	}

	/**
	 * This method removes the data-parsoid and about attributes from the HTML string passed in parameters, so
	 * that it doesn't interfere for the comparison of identical references.
	 * Remex does not implement the removal of "foreign" attributes, which means that these attributes cannot be
	 * removed on math and svg elements (T380977), and that trying to do so crashes the rendering.
	 * To avoid this, we only apply the normalization to nodes in the HTML namespace. This is wider than the
	 * exact definition of foreign attributes in Remex, but the other integration points of non-foreign content
	 * would be embedded in foreign content anyway - whose data-parsoid/about attributes would not be stripped
	 * anyway, so there's no need to process them.
	 * This means that identical references containing math or svg tags will be detected as being different.
	 * This is probably a rare enough corner case. If it is not, implementing the handling of foreign attributes
	 * (as started in Idf30b3afa00743fd78b015ff080cac29e1673f09) is a path to re-consider.
	 * The extra parameter $checkContent allows to further suppress error messages for references that are not
	 * technically identical from a round-tripping perspective (hence the need to still consider them distinct) but
	 * "close enough" to not bother editors with a confusing error message. This is in particular the case for
	 * references that involve various levels of templating, which means involve different variation of
	 * data-mw presence/absence.
	 */
	private function normalizeRef( string $s, bool $checkContent = false ): string {
		return HtmlHelper::modifyElements( $s,
			static function ( SerializerNode $node ) use ( $checkContent ): bool {
				return $node->namespace == HTMLData::NS_HTML
					&& ( isset( $node->attrs['data-parsoid'] )
						|| isset( $node->attrs['about'] )
						|| ( $checkContent
							&& ( isset( $node->attrs['data-mw'] ) || isset( $node->attrs['typeof'] ) )
						)
					);
			},
			static function ( SerializerNode $node ) use ( $checkContent ): SerializerNode {
				unset( $node->attrs['data-parsoid'] );
				unset( $node->attrs['about'] );
				if ( $checkContent ) {
					unset( $node->attrs['data-mw'] );
					unset( $node->attrs['typeof'] );
				}
				return $node;
			}
		);
	}

}
