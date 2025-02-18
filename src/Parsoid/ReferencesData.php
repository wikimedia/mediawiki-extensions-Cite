<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Wikimedia\Parsoid\Core\Sanitizer;
use Wikimedia\Parsoid\Ext\ParsoidExtensionAPI;
use Wikimedia\Parsoid\NodeData\DataMwError;

/**
 * @license GPL-2.0-or-later
 */
class ReferencesData {

	/** Global, auto-incrementing sequence number for all <ref>, no matter which group */
	private int $index = 0;
	/** @var array<string,RefGroup> indexed by group name */
	private array $refGroups = [];
	/** @var array<string,list<DataMwError>> */
	public array $embeddedErrors = [];
	/** @var string[] */
	private array $inEmbeddedContent = [];
	public string $referencesGroup = '';
	private int $nestedRefsDepth = 0;

	public function inReferencesContent(): bool {
		return $this->inEmbeddedContent( 'references' );
	}

	public function inRefContent(): bool {
		return $this->nestedRefsDepth > 0;
	}

	public function inEmbeddedContent( ?string $needle = null ): bool {
		if ( $needle ) {
			return in_array( $needle, $this->inEmbeddedContent, true );
		} else {
			return $this->inEmbeddedContent !== [];
		}
	}

	public function pushEmbeddedContentFlag( string $needle = 'embed' ): void {
		array_unshift( $this->inEmbeddedContent, $needle );
	}

	public function popEmbeddedContentFlag() {
		array_shift( $this->inEmbeddedContent );
	}

	public function incrementRefDepth(): void {
		$this->nestedRefsDepth++;
	}

	public function decrementRefDepth(): void {
		$this->nestedRefsDepth--;
	}

	public function getRefGroup(
		string $groupName, bool $allocIfMissing = false
	): ?RefGroup {
		if ( $allocIfMissing ) {
			$this->refGroups[$groupName] ??= new RefGroup( $groupName );
		}
		return $this->refGroups[$groupName] ?? null;
	}

	public function removeRefGroup( string $groupName ): void {
		// '' is a valid group (the default group)
		unset( $this->refGroups[$groupName] );
	}

	/**
	 * Normalizes and sanitizes anchor names for use in id="…" and <a href="#…"> attributes.
	 */
	private function normalizeFragmentIdentifier( string $id ): string {
		$ret = Sanitizer::escapeIdForLink( $id );
		// MediaWiki normalizes spaces and underscores in [[#…]] links, but not in id="…"
		// attributes. To make them behave the same we normalize in advance.
		$ret = preg_replace( '/[_\s]+/u', '_', $ret );
		return $ret;
	}

	public function add(
		ParsoidExtensionAPI $extApi, string $groupName, string $refName, string $refDir
	): RefGroupItem {
		$group = $this->getRefGroup( $groupName, true );
		$hasRefName = $refName !== '';

		// The ids produced Cite.php have some particulars:
		// Simple refs get 'cite_ref-' + index
		// Refs with names get 'cite_ref-' + name + '_' + index + (backlink num || 0)
		// Notes (references) whose ref doesn't have a name are 'cite_note-' + index
		// Notes whose ref has a name are 'cite_note-' + name + '-' + index
		$n = $this->index;
		$refKey = strval( 1 + $n );

		$refNameSanitized = $this->normalizeFragmentIdentifier( $refName );

		$refIdBase = 'cite_ref-' . ( $hasRefName ? $refNameSanitized . '_' . $refKey : $refKey );
		$noteId = 'cite_note-' . ( $hasRefName ? $refNameSanitized . '-' . $refKey : $refKey );

		// bump index
		$this->index++;

		$ref = new RefGroupItem();
		$ref->dir = $refDir;
		$ref->group = $group->name;
		// FIXME: This doesn't count correctly when <ref follow=…> is used on the page
		$ref->numberInGroup = count( $group->refs ) + 1;
		$ref->index = $n;
		$ref->backLinkIdBase = $refIdBase;
		$ref->name = $refName;
		$ref->target = $noteId;

		$group->refs[] = $ref;

		if ( $hasRefName ) {
			$group->indexByName[$refName] = $ref;
		}

		return $ref;
	}

	/**
	 * @return RefGroup[]
	 */
	public function getRefGroups(): array {
		return $this->refGroups;
	}
}
