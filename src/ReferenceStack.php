<?php

namespace Cite;

use StripState;

/**
 * Encapsulates most of Cite state during parsing.  This includes metadata about each ref tag,
 * and a rollback stack to correct confusion caused by lost context when `{{#tag` is used.
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
	 * <ref> call stack
	 * Used to cleanup out of sequence ref calls created by #tag
	 * See description of function rollbackRef.
	 *
	 * @var (array|false)[]
	 */
	private $refCallStack = [];

	/**
	 * @deprecated We should be able to push this responsibility to calling code.
	 * @var CiteErrorReporter $errorReporter
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
	 * @return string[]|null of [ $key, $count, $label, $subkey ] or null if nothing is pushed.
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
			array_splice( $this->refs[$group], $k, 0, [ $ref ] );
			array_splice( $this->refCallStack, $k, 0,
				[ [ 'new', $argv, $text, $name, $extends, $group, $this->refSequence ] ] );

			// A "follow" never gets its own footnote marker
			return null;
		}

		if ( !$name ) {
			// This is an anonymous reference, which will be given a numeric index.
			$this->refs[$group][] = $ref;
			$action = 'new';
		} elseif ( !isset( $this->refs[$group][$name] ) ) {
			// Valid key with first occurrence
			$ref['number'] = ++$this->groupRefSequence[$group];
			$this->refs[$group][$name] = $ref;
			$action = 'new';
		} else {
			// Change an existing ref entry.
			$ref =& $this->refs[$group][$name];
			$ref['count']++;
			if ( $ref['text'] === null && $text !== '' ) {
				// If no text was set before, use this text
				$ref['text'] = $text;
				// Use the dir parameter only from the full definition of a named ref tag
				$ref['dir'] = $dir;
				$action = 'assign';
			} else {
				if ( $text != null && $text !== ''
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
			// Rollback the counter since we are dropping this ref.  Goes away once we
			// move this logic to validateRef.
			$this->refSequence--;
		}
		$this->refCallStack[] = [ $action, $argv, $text, $name, $extends, $group, $ref['key'] ];
		return [
			$name ?? $ref['key'],
			$name ? $ref['key'] . '-' . $ref['count'] : null,
			$ref['number'] ?? ++$this->groupRefSequence[$group],
			$name ? '-' . $ref['key'] : null
		];
	}

	/**
	 * Undo the changes made by the last $count ref tags.  This is used when we discover that the
	 * last few tags were actually inside of a references tag.
	 *
	 * @param int $count
	 * @return array Refs to restore under the correct context. [ $argv, $text ]
	 */
	public function rollbackRefs( $count ) : array {
		$redoStack = [];
		for ( $i = 0; $i < $count; $i++ ) {
			if ( !$this->refCallStack ) {
				break;
			}

			$call = array_pop( $this->refCallStack );
			if ( $call !== false ) {
				[ $action, $argv, $text, $name, $extends, $group, $index ] = $call;
				$this->rollbackRef( $action, $name, $extends, $group, $index );
				$redoStack[] = [ $argv, $text ];
			}
		}
		// Drop unused rollbacks.  TODO: Warn if not fully consumed?
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
	 * @param int $index Autoincrement counter for this ref.
	 */
	private function rollbackRef( $type, $name, $extends, $group, $index ) {
		if ( !$this->hasGroup( $group ) ) {
			return;
		}

		$key = $name;
		if ( $name === null ) {
			foreach ( $this->refs[$group] as $k => $v ) {
				if ( $this->refs[$group][$k]['key'] === $index ) {
					$key = $k;
					break;
				}
			}
		}

		// Sanity checks that specified element exists.
		if ( $key === null ||
			!isset( $this->refs[$group][$key] ) ||
			$this->refs[$group][$key]['key'] !== $index
		) {
			return;
		}

		switch ( $type ) {
			case 'new':
				# Rollback the addition of new elements to the stack.
				unset( $this->refs[$group][$key] );
				if ( $this->refs[$group] === [] ) {
					unset( $this->refs[$group] );
					unset( $this->groupRefSequence[$group] );
				}
				break;
			case 'assign':
				# Rollback assignment of text to pre-existing elements.
				$this->refs[$group][$key]['text'] = null;
			# continue without break
			case 'increment':
				# Rollback increase in named ref occurrences.
				$this->refs[$group][$key]['count']--;
				break;
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
	 * @param string $group
	 * @return array[]
	 */
	public function getGroupRefs( string $group ) : array {
		return $this->refs[$group] ?? [];
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
