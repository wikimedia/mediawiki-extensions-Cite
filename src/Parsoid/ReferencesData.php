<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

use Wikimedia\Parsoid\Core\Sanitizer;
use Wikimedia\Parsoid\NodeData\DataMwError;

/**
 * @license GPL-2.0-or-later
 */
class ReferencesData {

	/**
	 * Global, auto-incrementing sequence number for all <ref>, no matter which group, starting
	 * from 1. Reflects the total number of <ref>.
	 */
	private int $refSequence = 0;
	/** @var array<string,RefGroup> indexed by group name */
	private array $refGroups = [];
	/** @var array<string,list<DataMwError>> */
	public array $embeddedErrors = [];
	/** @var string[] */
	private array $embeddedContentStack = [];
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
			return in_array( $needle, $this->embeddedContentStack, true );
		} else {
			return $this->embeddedContentStack !== [];
		}
	}

	public function pushEmbeddedContentFlag( string $needle = 'embed' ): void {
		array_push( $this->embeddedContentStack, $needle );
	}

	public function popEmbeddedContentFlag(): void {
		array_pop( $this->embeddedContentStack );
	}

	public function peekEmbeddedContentFlag(): ?string {
		return $this->embeddedContentStack[count( $this->embeddedContentStack ) - 1] ?? null;
	}

	public function incrementRefDepth(): void {
		$this->nestedRefsDepth++;
	}

	public function decrementRefDepth(): void {
		$this->nestedRefsDepth--;
	}

	public function getOrCreateRefGroup( string $groupName ): RefGroup {
		$this->refGroups[$groupName] ??= new RefGroup( $groupName );
		return $this->refGroups[$groupName];
	}

	public function lookupRefGroup( string $groupName ): ?RefGroup {
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
		string $groupName, ?string $refName, string $refDir
	): RefGroupItem {
		$group = $this->getOrCreateRefGroup( $groupName );

		// The ids produced Cite.php have some particulars:
		// Simple refs get 'cite_ref-' + index
		// Refs with names get 'cite_ref-' + name + '_' + index + (backlink num || 0)
		// Notes (references) whose ref doesn't have a name are 'cite_note-' + index
		// Notes whose ref has a name are 'cite_note-' + name + '-' + index
		$refKey = ++$this->refSequence;

		$refIdBase = 'cite_ref-';
		$noteId = 'cite_note-';
		if ( $refName ) {
			$refNameSanitized = $this->normalizeFragmentIdentifier( $refName );
			$refIdBase .= $refNameSanitized . '_';
			$noteId .= $refNameSanitized . '-';
		}
		$refIdBase .= $refKey;
		$noteId .= $refKey;

		$ref = new RefGroupItem();
		$ref->dir = $refDir;
		$ref->group = $group->name;
		// FIXME: This doesn't count correctly when <ref follow=…> is used on the page
		$ref->numberInGroup = $group->getNextIndex();
		$ref->globalId = $refKey;
		$ref->backLinkIdBase = $refIdBase;
		$ref->name = $refName ?: null;
		$ref->noteId = $noteId;

		$group->refs[] = $ref;

		if ( $refName ) {
			$group->indexByName[$refName] = $ref;
		}

		return $ref;
	}

	public function addSubref(
		string $groupName, string $parentName, string $refDir
	): RefGroupItem {
		$group = $this->getOrCreateRefGroup( $groupName );

		$refKey = ++$this->refSequence;

		$refIdBase = 'cite_ref-' . $refKey;
		$noteId = 'cite_note-' . $refKey;

		$ref = new RefGroupItem();
		$ref->dir = $refDir;
		$ref->group = $group->name;

		$parentRef = $group->indexByName[$parentName] ?? null;
		if ( $parentRef ) {
			$ref->numberInGroup = $parentRef->numberInGroup;
		}
		$ref->subrefIndex = $group->getNextSubrefSequence( $parentName );
		$ref->globalId = $refKey;
		$ref->backLinkIdBase = $refIdBase;
		$ref->noteId = $noteId;

		$group->refs[] = $ref;

		return $ref;
	}

	/**
	 * @return RefGroup[]
	 */
	public function getRefGroups(): array {
		return $this->refGroups;
	}
}
