<?php

namespace Cite;

use StripState;

/**
 * Encapsulates most of Cite state during parsing.  This includes metadata about each ref tag,
 * and a rollback stack to correct confusion caused by lost context when `{{#tag` is used.
 *
 * @license GPL-2.0-or-later
 */
class ReferenceStack {

	/**
	 * Datastructure representing <ref> input, in the format of:
	 * <code>
	 * [
	 *     'user supplied' => [
	 *         'text' => 'user supplied reference & key',
	 *         'count' => 1, // occurs twice
	 *         'number' => 1, // The first reference, we want
	 *                        // all occourances of it to
	 *                        // use the same number
	 *     ],
	 *     0 => [
	 *         'text' => 'Anonymous reference',
	 *         'count' => -1,
	 *     ],
	 *     1 => [
	 *         'text' => 'Another anonymous reference',
	 *         'count' => -1,
	 *     ],
	 *     'some key' => [
	 *         'text' => 'this one occurs once'
	 *         'count' => 0,
	 *         'number' => 4
	 *     ],
	 *     3 => 'more stuff'
	 * ];
	 * </code>
	 *
	 * This works because:
	 * * PHP's datastructures are guaranteed to be returned in the
	 *   order that things are inserted into them (unless you mess
	 *   with that)
	 * * User supplied keys can't be integers, therefore avoiding
	 *   conflict with anonymous keys
	 *
	 * In this structure, 'key' will either be an autoincrementing integer.
	 *
	 * @var array[][]
	 */
	private $refs = [];

	/**
	 * Count for user displayed output (ref[1], ref[2], ...)
	 *
	 * @var int
	 */
	private $refSequence = 0;

	/**
	 * Counter for the number of refs in each group.
	 * @var int[]
	 */
	private $groupRefSequence = [];

	/**
	 * @var int[][]
	 */
	private $extendsCount = [];

	/**
	 * <ref> call stack
	 * Used to cleanup out of sequence ref calls created by #tag
	 * See description of function rollbackRef.
	 *
	 * @var (array|false)[]
	 */
	private $refCallStack = [];

	/**
	 * @deprecated We should be able to push this responsibility to calling code.
	 * @var CiteErrorReporter
	 */
	private $errorReporter;

	/**
	 * @param CiteErrorReporter $errorReporter
	 */
	public function __construct( CiteErrorReporter $errorReporter ) {
		$this->errorReporter = $errorReporter;
	}

	/**
	 * Leave a mark in the stack which matches an invalid ref tag.
	 */
	public function pushInvalidRef() {
		$this->refCallStack[] = false;
	}

	/**
	 * Populate $this->refs and $this->refCallStack based on input and arguments to <ref>
	 *
	 * @param ?string $text Content from the <ref> tag
	 * @param ?string $name
	 * @param string $group
	 * @param ?string $extends
	 * @param ?string $follow Guaranteed to not be a numeric string
	 * @param string[] $argv
	 * @param ?string $dir ref direction
	 * @param StripState $stripState
	 *
	 * @return ?array ref structure, or null if nothing was pushed
	 */
	public function pushRef(
		?string $text,
		?string $name,
		string $group,
		?string $extends,
		?string $follow,
		array $argv,
		?string $dir,
		StripState $stripState
	) : ?array {
		if ( !isset( $this->refs[$group] ) ) {
			$this->refs[$group] = [];
		}
		if ( !isset( $this->groupRefSequence[$group] ) ) {
			$this->groupRefSequence[$group] = 0;
		}

		if ( $this->refs[$group][$follow] ?? false ) {
			// We know the parent note already, so just perform the "follow" and bail out
			// TODO: Separate `pushRef` from these side-effects.
			$this->refs[$group][$follow]['text'] .= ' ' . $text;
			return null;
		}

		$ref = [
			'count' => $name ? 0 : -1,
			'dir' => $dir,
			// This assumes we are going to register a new reference, instead of reusing one
			'key' => ++$this->refSequence,
			'name' => $name,
			'text' => $text,
		];

		if ( $follow ) {
			$ref['follow'] = $follow;
			// This inserts the broken "follow" at the end of all other broken "follow"
			$k = 0;
			foreach ( $this->refs[$group] as $value ) {
				if ( !isset( $value['follow'] ) ) {
					break;
				}
				$k++;
			}
			// TODO: Once this edge case is eliminated, we'll be able to assume that a groupRefs
			//  array index is constant, preventing O(N) searches.
			array_splice( $this->refs[$group], $k, 0, [ $ref ] );
			array_splice( $this->refCallStack, $k, 0,
				[ [ 'new', $argv, $text, $name, $extends, $group, $this->refSequence ] ] );

			// A "follow" never gets its own footnote marker
			return null;
		}

		if ( !$name ) {
			// This is an anonymous reference, which will be given a numeric index.
			$this->refs[$group][] = &$ref;
			$action = 'new';
		} elseif ( isset( $this->refs[$group][$name]['__placeholder__'] ) ) {
			// Populate a placeholder.
			unset( $this->refs[$group][$name]['__placeholder__'] );
			unset( $ref['number'] );
			$ref = array_merge( $ref, $this->refs[$group][$name] );
			$this->refs[$group][$name] =& $ref;
			$action = 'new';
		} elseif ( !isset( $this->refs[$group][$name] ) ) {
			// Valid key with first occurrence
			$this->refs[$group][$name] = &$ref;
			$action = 'new';
		} else {
			// Change an existing entry.
			$ref = &$this->refs[$group][$name];
			$ref['count']++;
			// Rollback the global counter since we won't create a new ref.
			$this->refSequence--;
			if ( $ref['text'] === null && $text !== null ) {
				// If no text was set before, use this text
				$ref['text'] = $text;
				// Use the dir parameter only from the full definition of a named ref tag
				$ref['dir'] = $dir;
				$action = 'assign';
			} else {
				if ( $text !== null
					// T205803 different strip markers might hide the same text
					&& $stripState->unstripBoth( $text )
					!== $stripState->unstripBoth( $ref['text'] )
				) {
					// two refs with same name and different text
					// add error message to the original ref
					// TODO: standardize error display and move to `validateRef`.
					$ref['text'] .= ' ' . $this->errorReporter->plain(
						'cite_error_references_duplicate_key', $name
					);
				}
				$action = 'increment';
			}
		}

		$ref['number'] = $ref['number'] ?? ++$this->groupRefSequence[$group];

		if ( $extends ) {
			$this->extendsCount[$group][$extends] =
				( $this->extendsCount[$group][$extends] ?? 0 ) + 1;

			$ref['extends'] = $extends;
			$ref['extendsIndex'] = $this->extendsCount[$group][$extends];

			if ( isset( $this->refs[$group][$extends]['number'] ) ) {
				// Adopt the parent's number.
				// TODO: Do we need to roll back the group ref sequence here?
				$ref['number'] = $this->refs[$group][$extends]['number'];
			} else {
				// Transfer my number to parent ref.
				$this->refs[$group][$extends] = [
					'number' => $ref['number'],
					'__placeholder__' => true,
				];
			}
		}

		$this->refCallStack[] = [ $action, $argv, $text, $name, $extends, $group, $ref['key'] ];
		return $ref;
	}

	/**
	 * Undo the changes made by the last $count ref tags.  This is used when we discover that the
	 * last few tags were actually inside of a references tag.
	 *
	 * @param int $count
	 * @return array Refs to restore under the correct context. [ $argv, $text ]
	 */
	public function rollbackRefs( int $count ) : array {
		$redoStack = [];
		for ( $i = 0; $i < $count; $i++ ) {
			if ( !$this->refCallStack ) {
				break;
			}

			$call = array_pop( $this->refCallStack );
			if ( $call !== false ) {
				[ $action, $argv, $text, $name, $extends, $group, $key ] = $call;
				$this->rollbackRef( $action, $name, $extends, $group, $key );
				$redoStack[] = [ $argv, $text ];
			}
		}
		// Drop unused rollbacks, this group is finished.
		$this->refCallStack = [];

		return array_reverse( $redoStack );
	}

	/**
	 * Partially undoes the effect of calls to stack()
	 *
	 * Called by guardedReferences()
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
	 * @param string $type
	 * @param string|null $name The name attribute passed in the ref tag.
	 * @param string|null $extends
	 * @param string $group
	 * @param int $key Autoincrement counter for this ref.
	 */
	private function rollbackRef(
		string $type,
		?string $name,
		?string $extends,
		string $group,
		int $key
	) {
		if ( !$this->hasGroup( $group ) ) {
			return;
		}

		$lookup = $name;
		if ( $name === null ) {
			// Find anonymous ref by key.
			foreach ( $this->refs[$group] as $k => $v ) {
				if ( isset( $this->refs[$group][$k]['key'] ) &&
					$this->refs[$group][$k]['key'] === $key
				) {
					$lookup = $k;
					break;
				}
			}
		}

		// Sanity checks that specified element exists.
		if ( $lookup === null ||
			!isset( $this->refs[$group][$lookup] ) ||
			$this->refs[$group][$lookup]['key'] !== $key
		) {
			return;
		}

		if ( $extends ) {
			$this->extendsCount[$group][$extends]--;
		}

		switch ( $type ) {
			case 'new':
				# Rollback the addition of new elements to the stack.
				unset( $this->refs[$group][$lookup] );
				if ( $this->refs[$group] === [] ) {
					// TODO: Unsetting is unecessary.
					$this->deleteGroup( $group );
				}
				// TODO: else, don't we need to decrement groupRefSequence?
				break;
			case 'assign':
				# Rollback assignment of text to pre-existing elements.
				$this->refs[$group][$lookup]['text'] = null;
				$this->refs[$group][$lookup]['count']--;
				break;
			case 'increment':
				# Rollback increase in named ref occurrences.
				$this->refs[$group][$lookup]['count']--;
				break;
			default:
				throw new \LogicException( "Unknown call stack action \"$type\"" );
		}
	}

	/**
	 * Reset all state.
	 */
	public function clear() {
		$this->groupRefSequence = [];
		$this->refSequence = 0;
		$this->refs = [];
		$this->refCallStack = [];
	}

	/**
	 * Clear state for a single group.
	 *
	 * @param string $group
	 */
	public function deleteGroup( string $group ) {
		unset( $this->refs[$group] );
		unset( $this->groupRefSequence[$group] );
		unset( $this->extendsCount[$group] );
	}

	/**
	 * Retruns true if the group exists and contains references.
	 *
	 * @param string $group
	 * @return bool
	 */
	public function hasGroup( string $group ) : bool {
		return isset( $this->refs[$group] ) && $this->refs[$group];
	}

	/**
	 * Returns a list of all groups with references.
	 *
	 * @return string[]
	 */
	public function getGroups() : array {
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
	 * @param ?string $group
	 * @return array[]
	 */
	public function getGroupRefs( ?string $group ) : array {
		return $this->refs[$group ?? Cite::DEFAULT_GROUP] ?? [];
	}

	/**
	 * Interface to set reference text from external code.  Ideally we can take over
	 * responsibility for this logic.
	 * @deprecated
	 *
	 * @param string $group
	 * @param string $name
	 * @param ?string $text
	 */
	public function setRefText( string $group, string $name, ?string $text ) {
		$this->refs[$group][$name]['text'] = $text;
	}

}
