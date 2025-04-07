<?php
declare( strict_types = 1 );

namespace Cite\Parsoid;

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

	public function lookupRefByName( RefGroup $group, string $name ): ?RefGroupItem {
		return $group->indexByName[$name] ?? null;
	}

	public function addRef(
		RefGroup $group,
		string $refName,
		string $refDir,
		?string $details = null
	): RefGroupItem {
		$ref = new RefGroupItem();

		if ( $details === null ) {
			// FIXME: This doesn't count correctly when <ref follow=…> is used on the page
			$ref->numberInGroup = $group->getNextIndex();
			$ref->name = $refName ?: null;
		} else {
			$mainRef = $this->lookupRefByName( $group, $refName ) ??
				// TODO: dir could be different for the main
				$this->addRef( $group, $refName, $refDir );

			$ref->numberInGroup = $mainRef->numberInGroup;
			$ref->subrefIndex = $group->getNextSubrefSequence( $refName );
		}

		$ref->dir = $refDir;
		$ref->group = $group->name;
		$ref->globalId = ++$this->refSequence;

		$group->refs[] = $ref;
		if ( $ref->name ) {
			$group->indexByName[$ref->name] = $ref;
		}

		return $ref;
	}

	/** @return array<string,RefGroup> */
	public function getRefGroups(): array {
		return $this->refGroups;
	}

}
