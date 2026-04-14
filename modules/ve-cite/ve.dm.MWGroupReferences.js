'use strict';

const MWDataTransitionHelper = require( './ve.dm.MWDataTransitionHelper.js' );

/*!
 * @copyright 2024 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Holds information about the refs from a single Cite group.
 *
 * This structure is persisted in memory until a document change affects a ref
 * tag from this group, at which point it will be fully recalculated.
 *
 * @constructor
 */
ve.dm.MWGroupReferences = function VeDmMWGroupReferences() {
	// Mixin constructors
	OO.EventEmitter.call( this );

	// Properties
	/**
	 * @member {Object.<string, ve.dm.MWDataTransitionHelper.RefInfo>}
	 * @private
	 */
	this.calculatedNumbering = {};

	/**
	 * InternalList node group, or null if no such group exists.
	 *
	 * @member {ve.dm.InternalListNodeGroup|null}
	 * @private
	 */
	this.nodeGroup = null;
};

/* Inheritance */

OO.initClass( ve.dm.MWGroupReferences );

/* Static Methods */

/**
 * Rebuild information about this group of references.
 *
 * @param {ve.dm.InternalListNodeGroup|undefined} nodeGroup
 * @return {ve.dm.MWGroupReferences}
 */
ve.dm.MWGroupReferences.static.makeGroupRefs = function ( nodeGroup ) {
	const result = new ve.dm.MWGroupReferences();
	if ( nodeGroup ) {
		result.nodeGroup = nodeGroup;
		result.calculatedNumbering = new MWDataTransitionHelper().buildReflistNumbering( nodeGroup );
	}
	return result;
};

/* Methods */

/**
 * Check whether the group has any references.
 *
 * @deprecated use {@link ve.dm.InternalListNodeGroup.isEmpty} instead
 * @return {boolean}
 */
ve.dm.MWGroupReferences.prototype.isEmpty = function () {
	return !this.nodeGroup || this.nodeGroup.isEmpty();
};

/**
 * Internal comparator for sorting references into reflist order, given two listIndexes
 *
 * @private
 * @param {string} a Left listIndex
 * @param {string} b Right listIndex
 * @return {number} according to {@link Array.sort}
 */
ve.dm.MWGroupReferences.prototype.compareAsIndexes = function ( a, b ) {
	return this.compareAsRefInfos( this.calculatedNumbering[ a ], this.calculatedNumbering[ b ] );
};

/**
 * Internal comparator for sorting references into reflist order, given two RefInfo objects
 *
 * @private
 * @param {ve.dm.MWDataTransitionHelper.RefInfo} a Left term
 * @param {ve.dm.MWDataTransitionHelper.RefInfo} b Right term
 * @return {number} according to {@link Array.sort}
 */
ve.dm.MWGroupReferences.prototype.compareAsRefInfos = function ( a, b ) {
	return ( a.topLevelNumber - b.topLevelNumber ) || ( ( a.subrefNumber || 0 ) - ( b.subrefNumber || 0 ) );
};

/**
 * List all reference listIndex's in the order they appear in the reflist including
 * named refs, unnamed refs, and those that don't resolve
 *
 * @private
 * @return {number[]}
 */
ve.dm.MWGroupReferences.prototype.getListIndexesInReflistOrder = function () {
	return Object.keys( this.calculatedNumbering )
		.sort( ( a, b ) => this.compareAsIndexes( a, b ) )
		.map( ( indexStr ) => Number( indexStr ) );
};

/**
 * List all document references in the order they first appear, ignoring reuses
 * and placeholders.
 *
 * @return {ve.dm.MWReferenceNode[]}
 */
ve.dm.MWGroupReferences.prototype.getAllRefsInReflistOrder = function () {
	return this.getListIndexesInReflistOrder()
		.map( ( listIndex ) => this.nodeGroup.firstNodes[ listIndex ] )
		.filter( ( firstNode ) => !!firstNode );
};

/**
 * List all main reference listIndex's in the order they appear in the reflist including
 * named refs, unnamed refs, and those that don't resolve
 *
 * @return {number[]} Reference listIndex's
 */
ve.dm.MWGroupReferences.prototype.getTopLevelListIndexesInReflistOrder = function () {
	return this.getListIndexesInReflistOrder()
		.filter( ( listIndex ) => this.calculatedNumbering[ listIndex ].subrefNumber === undefined );
};

/**
 * Return the defining reference node for this key
 *
 * @deprecated use {@link ve.dm.InternalListNodeGroup.getFirstNode} instead
 * @param {number} listIndex
 * @return {ve.dm.MWReferenceNode|undefined}
 */
ve.dm.MWGroupReferences.prototype.getRefNode = function ( listIndex ) {
	return this.nodeGroup && this.nodeGroup.firstNodes[ listIndex ];
};

/**
 * Return document nodes for each usage of a ref listIndex.  This excludes usages
 * under the `<references>` section, so note that nested references won't behave
 * as expected.  The reflist item for a ref is not counted as a reference,
 * either.
 *
 * FIXME: Implement backlinks from within a nested ref within the footnote body.
 *
 * @param {number} listIndex
 * @return {ve.dm.MWReferenceNode[]}
 */
ve.dm.MWGroupReferences.prototype.getRefUsages = function ( listIndex ) {
	return this.getAllReusesByListIndex( listIndex )
		.filter( ( node ) => !node.getAttribute( 'placeholder' ) &&
		// FIXME: Couldn't resolve this so far because of a circular dependency!
				!node.findParent( ve.dm.MWReferencesListNode )
		);
};

/**
 * Temporary helper as long as the upstream {@link ve.dm.InternalListNodeGroup} doesn't have a
 * better method for this.
 *
 * @param {number} listIndex
 * @return {ve.dm.MWReferenceNode[]}
 */
ve.dm.MWGroupReferences.prototype.getAllReusesByListIndex = function ( listIndex ) {
	const key = this.getListKeyForListIndex( listIndex );
	return key && this.nodeGroup.getAllReuses( key ) || [];
};

/**
 * Temporary helper as long as the upstream {@link ve.dm.InternalListNodeGroup} doesn't have a
 * better method for this.
 *
 * @private
 * @param {number} listIndex
 * @return {string|undefined}
 */
ve.dm.MWGroupReferences.prototype.getListKeyForListIndex = function ( listIndex ) {
	const firstNode = this.nodeGroup && this.nodeGroup.firstNodes[ listIndex ];
	if ( !firstNode ) {
		return undefined;
	}

	// FIXME: This is an inefficient workaround, replace with a dedicated method
	for ( const key of this.nodeGroup.getKeysInIndexOrder() ) {
		// Note: This works with the guarantee that the first node is actually the first
		if ( this.nodeGroup.getAllReuses( key )[ 0 ] === firstNode ) {
			// TODO: Temporary safety-net, either remove or just return the attribute
			if ( key !== firstNode.getAttribute( 'listKey' ) ) {
				ve.log( 'Mismatching ' + key + ' vs. ' + firstNode.getAttribute( 'listKey' ) );
			}
			return key;
		}
	}
};

/**
 * Get the total number of usages for a reference, including sub-references.
 *
 * @param {number} listIndex
 * @return {number} Total usage count of main refs and subrefs
 */
ve.dm.MWGroupReferences.prototype.getTotalUsageCount = function ( listIndex ) {
	const mainRefs = this.getRefUsages( listIndex );
	let usageCount = mainRefs.length;

	this.getSubrefs( listIndex ).forEach( ( node ) => {
		usageCount += this.getRefUsages( node.getAttribute( 'listIndex' ) ).length;
	} );

	return usageCount;
};

/**
 * Filter to subrefs on this main ref, order by reflist number, and then turn
 * into a list of MWReferenceNodes from the document.
 *
 * @param {number} mainListIndex
 * @return {ve.dm.MWReferenceNode[]} List of subrefs for this parent not including re-uses
 */
ve.dm.MWGroupReferences.prototype.getSubrefs = function ( mainListIndex ) {
	return Object.values( this.calculatedNumbering )
		.filter( ( ref ) => ref.mainListIndex === mainListIndex )
		.sort( ( a, b ) => this.compareAsRefInfos( a, b ) )
		.map( ( ref ) => this.nodeGroup.firstNodes[ ref.internalListIndex ] )
		.filter( ( node ) => node );
};

/**
 * Lookup from listIndex to a rendered footnote number or subref number like "1.2", in the
 * local content language.
 *
 * @deprecated TODO: push to presentation
 * @param {number} listIndex
 * @return {string} rendered number label
 */
ve.dm.MWGroupReferences.prototype.getIndexLabel = function ( listIndex ) {
	return ( this.calculatedNumbering[ listIndex ] ? this.calculatedNumbering[ listIndex ].label : '…' );
};

module.exports = ve.dm.MWGroupReferences;
