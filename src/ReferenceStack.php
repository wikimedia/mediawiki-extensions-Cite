<?php

namespace Cite;

use LogicException;
use MediaWiki\Parser\StripState;

/**
 * Encapsulates most of Cite state during parsing.  This includes metadata about each ref tag,
 * and a rollback stack to correct confusion caused by lost context when `{{#tag` is used.
 *
 * @license GPL-2.0-or-later
 */
class ReferenceStack {

	/**
	 * Data structure representing all <ref> tags parsed so far, indexed by group name (an empty
	 * string for the default group) and reference name.
	 *
	 * References without a name get a numeric index, starting from 0. Conflicts are avoided by
	 * disallowing numeric names (e.g. <ref name="1">) in {@see Validator::validateRef}.
	 *
	 * @var array<string,array<string|int,ReferenceStackItem>>
	 */
	private array $refs = [];

	/** Global, auto-incrementing sequence number for all <ref>, no matter which group */
	private int $refSequence = 0;
	/** @var array<string,int> Auto-incrementing sequence numbers per group */
	private array $groupRefSequence = [];

	/**
	 * <ref> call stack
	 * Used to cleanup out of sequence ref calls created by #tag
	 * See description of function rollbackRef.
	 *
	 * @var (array|false)[]
	 * @phan-var array<array{0:string,1:int,2:string,3:?string,4:?string,5:?string,6:array}|false>
	 */
	private array $refCallStack = [];

	private const ACTION_ASSIGN = 'assign';
	private const ACTION_INCREMENT = 'increment';
	private const ACTION_NEW = 'new';

	/**
	 * Leave a mark in the stack which matches an invalid ref tag.
	 */
	public function pushInvalidRef(): void {
		$this->refCallStack[] = false;
	}

	/**
	 * Populate $this->refs and $this->refCallStack based on input and arguments to <ref>
	 *
	 * @param StripState $stripState
	 * @param ?string $text Content from the <ref> tag
	 * @param string[] $argv
	 * @param string $group
	 * @param ?string $name
	 * @param ?string $follow Guaranteed to not be a numeric string
	 * @param ?string $dir ref direction
	 * @param ?string $subrefDetails subreference details
	 *
	 * @return ?ReferenceStackItem ref to render at the site of usage, or null
	 * if no footnote marker should be rendered
	 */
	public function pushRef(
		StripState $stripState,
		?string $text,
		array $argv,
		string $group,
		?string $name,
		?string $follow,
		?string $dir,
		?string $subrefDetails
	): ?ReferenceStackItem {
		$this->refs[$group] ??= [];
		$this->groupRefSequence[$group] ??= 0;

		$ref = new ReferenceStackItem();
		$ref->count = 1;
		$ref->dir = $dir;
		// TODO: Read from this group field or deprecate it.
		$ref->group = $group;
		$ref->name = $name;
		$ref->text = $text;

		if ( $follow ) {
			if ( !isset( $this->refs[$group][$follow] ) ) {
				// Mark an incomplete follow="…" as such. This is valid e.g. in the Page:… namespace
				// on Wikisource.
				$ref->follow = $follow;
				$ref->globalId = $this->nextRefSequence();
				$this->refs[$group][$ref->globalId] = $ref;
				$this->refCallStack[] = [ self::ACTION_NEW, $ref->globalId, $group, $name, $text, $argv ];
			} elseif ( $text !== null ) {
				// We know the parent already, so just perform the follow="…" and bail out
				$this->resolveFollow( $group, $follow, $text );
			}
			// A follow="…" never gets its own footnote marker
			return null;
		}

		if ( !$name ) {
			// This is an anonymous reference, which will be given a numeric index.
			$ref->globalId = $this->nextRefSequence();
			$this->refs[$group][$ref->globalId] = &$ref;
			$action = self::ACTION_NEW;
		} elseif ( !isset( $this->refs[$group][$name] ) ) {
			// First occurrence of a named <ref>
			$this->refs[$group][$name] = &$ref;
			$ref->globalId = $this->nextRefSequence();
			$action = self::ACTION_NEW;
		} else {
			// Change an existing entry.
			$ref = &$this->refs[$group][$name];
			$ref->count++;

			if ( $ref->dir && $dir && $ref->dir !== $dir ) {
				$ref->warnings[] = [ 'cite_error_ref_conflicting_dir', $name ];
			}

			if ( $ref->text === null && $text !== null ) {
				// If no text was set before, use this text
				$ref->text = $text;
				// Use the dir parameter only from the full definition of a named ref tag
				$ref->dir = $dir;
				$action = self::ACTION_ASSIGN;
			} else {
				if ( $text !== null
					// T205803 different strip markers might hide the same text
					&& $stripState->unstripBoth( $text )
					!== $stripState->unstripBoth( $ref->text )
				) {
					// Two <ref> with same group and name, but different content
					$ref->warnings[] = [ 'cite_error_references_duplicate_key', $name ];
				}
				$action = self::ACTION_INCREMENT;
			}
		}

		$ref->numberInGroup ??= ++$this->groupRefSequence[$group];

		if ( ( $subrefDetails ?? '' ) !== '' ) {
			$parentRef = $ref;
			// Turns out this is not a reused parent; undo parts of what happened above
			if ( $name ) {
				$parentRef->count--;
			}

			// Make a clone of the sub-reference before we start manipulating the parent
			$subRef = clone $ref;

			$parentRef->subrefCount ??= 0;

			// Create the parent ref if new.
			if ( !$parentRef->count ) {
				$this->refCallStack[] = [ $action, $parentRef->globalId, $group, $name, $text, $argv ];
			}

			// FIXME: At the moment it's impossible to reuse sub-references in any way
			$subRef->count = 1;
			$subRef->globalId = $this->nextRefSequence();
			$subRef->name = null;
			$subRef->parentRefGlobalId = $parentRef->globalId;
			$subRef->subrefIndex = ++$parentRef->subrefCount;
			$subRef->text = $subrefDetails;
			$this->refs[$group][$subRef->globalId] = $subRef;
			$this->refCallStack[] = [ $action, $subRef->globalId, $group, null, $subrefDetails, $argv ];
			return $subRef;
		} else {
			$this->refCallStack[] = [ $action, $ref->globalId, $group, $name, $text, $argv ];
			return $ref;
		}
	}

	/**
	 * Undo the changes made by the last $count ref tags.  This is used when we discover that the
	 * last few tags were actually inside of a references tag.
	 *
	 * @param int $count
	 *
	 * @return array[] Refs to restore under the correct context, as a list of [ $text, $argv ]
	 * @phan-return array<array{0:?string,1:array}>
	 */
	public function rollbackRefs( int $count ): array {
		$redoStack = [];
		while ( $count-- && $this->refCallStack ) {
			$call = array_pop( $this->refCallStack );
			if ( $call ) {
				// @phan-suppress-next-line PhanParamTooFewUnpack
				$redoStack[] = $this->rollbackRef( ...$call );
			}
		}

		// Drop unused rollbacks, this group is finished.
		$this->refCallStack = [];

		return array_reverse( $redoStack );
	}

	/**
	 * Partially undoes the effect of calls to stack()
	 *
	 * The option to define <ref> within <references> makes the
	 * behavior of <ref> context dependent.  This is normally fine
	 * but certain operations (especially #tag) lead to out-of-order
	 * parser evaluation with the <ref> tags being processed before
	 * their containing <reference> element is read.  This leads to
	 * stack corruption that this function works to fix.
	 *
	 * This function is not a total rollback since some internal
	 * counters remain incremented.  Doing so prevents accidentally
	 * corrupting certain links.
	 *
	 * @param string $action
	 * @param int $globalId
	 * @param string $group
	 * @param ?string $name The name attribute passed in the ref tag.
	 * @param ?string $text
	 * @param array $argv
	 *
	 * @return array [ $text, $argv ] Ref redo item.
	 */
	private function rollbackRef(
		string $action,
		int $globalId,
		string $group,
		?string $name,
		?string $text,
		array $argv
	): array {
		if ( !$this->hasGroup( $group ) ) {
			throw new LogicException( "Cannot roll back ref with unknown group \"$group\"." );
		}

		$lookup = $name ?: $globalId;

		// Obsessive sanity checks that the specified element exists.
		if ( !isset( $this->refs[$group][$lookup] ) ) {
			throw new LogicException( "Cannot roll back unknown ref \"$lookup\"." );
		}
		$ref =& $this->refs[$group][$lookup];

		switch ( $action ) {
			case self::ACTION_NEW:
				// Rollback the addition of new elements to the stack
				unset( $this->refs[$group][$lookup] );
				if ( !$this->refs[$group] ) {
					$this->popGroup( $group );
				} elseif ( isset( $this->groupRefSequence[$group] ) ) {
					$this->groupRefSequence[$group]--;
				}
				break;
			case self::ACTION_ASSIGN:
				// Rollback assignment of text to pre-existing elements
				$ref->text = null;
				$ref->count--;
				break;
			case self::ACTION_INCREMENT:
				// Rollback increase in named ref occurrences
				$ref->count--;
				break;
			default:
				throw new LogicException( "Unknown call stack action \"$action\"" );
		}
		return [ $text, $argv ];
	}

	/**
	 * Clear state for a single group.
	 *
	 * @param string $group
	 *
	 * @return array<string|int,ReferenceStackItem> The references from the removed group
	 */
	public function popGroup( string $group ): array {
		$refs = $this->getGroupRefs( $group );
		unset( $this->refs[$group] );
		unset( $this->groupRefSequence[$group] );
		return $refs;
	}

	/**
	 * Returns true if the group exists and contains references.
	 */
	public function hasGroup( string $group ): bool {
		return (bool)( $this->refs[$group] ?? false );
	}

	/**
	 * @return string[] List of group names that contain at least one reference
	 */
	public function getGroups(): array {
		$groups = [];
		foreach ( $this->refs as $group => $refs ) {
			if ( $refs ) {
				$groups[] = $group;
			}
		}
		return $groups;
	}

	/**
	 * Return all references for a group.
	 *
	 * @param string $group
	 *
	 * @return array<string|int,ReferenceStackItem>
	 */
	public function getGroupRefs( string $group ): array {
		return $this->refs[$group] ?? [];
	}

	private function resolveFollow( string $group, string $follow, string $text ): void {
		$previousRef =& $this->refs[$group][$follow];
		$previousRef->text ??= '';
		$previousRef->text .= " $text";
	}

	public function listDefinedRef( string $group, string $name, string $text ): ReferenceStackItem {
		$ref =& $this->refs[$group][$name];
		$ref ??= new ReferenceStackItem();
		if ( $ref->text === null ) {
			$ref->text = $text;
		} elseif ( $ref->text !== $text ) {
			// Two <ref> with same group and name, but different content
			$ref->warnings[] = [ 'cite_error_references_duplicate_key', $name ];
		}
		return $ref;
	}

	private function nextRefSequence(): int {
		return ++$this->refSequence;
	}

}
