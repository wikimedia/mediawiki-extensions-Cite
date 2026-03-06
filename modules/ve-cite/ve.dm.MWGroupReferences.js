'use strict';

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
	 * Lookup from listIndex to a pair of integers which are the [major, minor] footnote numbers
	 * that will be rendered on the ref in some digit system.  Note that top-level refs always
	 * have minor number `-1`.
	 *
	 * @member {Object.<number, number[]>}
	 * @private
	 */
	this.footnoteNumberLookup = {};

	/**
	 * Lookup from a main reference's listIndex to the corresponding sub-refs.
	 *
	 * @member {Object.<number, ve.dm.MWReferenceNode[]>}
	 * @private
	 */
	this.subRefsByMain = {};

	/** @private */
	this.topLevelCounter = 1;

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
	if ( !nodeGroup ) {
		return result;
	}
	result.nodeGroup = nodeGroup;

	nodeGroup.getFirstNodesInIndexOrder()
		.filter( ( node ) => !node.getAttribute( 'placeholder' ) )
		.forEach( ( node ) => {
			const listKey = node.getAttribute( 'listKey' );
			const listIndex = node.getAttribute( 'listIndex' );
			const mainListIndex = node.getAttribute( 'mainListIndex' );
			const groupItemIndex = ( mainListIndex !== undefined ?
				result.addSubref( mainListIndex, listIndex, node ) :
				[ result.getOrAllocateTopLevelIndex( listIndex ), -1 ]
			);

			const reuseNodes = nodeGroup.getAllReuses( listKey );
			if ( reuseNodes ) {
				reuseNodes.forEach( ( refNode ) => refNode.setGroupIndex( groupItemIndex ) );
			}
		} );

	return result;
};

/* Methods */

/**
 * @private
 * @param {number} listIndex for the top-level ref
 * @return {number} Allocated topLevelIndex
 */
ve.dm.MWGroupReferences.prototype.getOrAllocateTopLevelIndex = function ( listIndex ) {
	if ( !( listIndex in this.footnoteNumberLookup ) ) {
		const number = this.topLevelCounter++;
		this.footnoteNumberLookup[ listIndex ] = [ number, -1 ];
	}
	return this.footnoteNumberLookup[ listIndex ][ 0 ];
};

/**
 * @private
 * @param {number} mainListIndex listIndex of the main reference
 * @param {number} subRefListIndex listIndex of the sub-reference
 * @param {ve.dm.MWReferenceNode} subRefNode Sub-reference to add to internal tracking
 * @return {number[]}
 */
ve.dm.MWGroupReferences.prototype.addSubref = function ( mainListIndex, subRefListIndex, subRefNode ) {
	if ( !( mainListIndex in this.subRefsByMain ) ) {
		this.subRefsByMain[ mainListIndex ] = [];
	}
	this.subRefsByMain[ mainListIndex ].push( subRefNode );
	const subRefIndex = this.subRefsByMain[ mainListIndex ].length;

	const topLevelIndex = this.getOrAllocateTopLevelIndex( mainListIndex );
	this.footnoteNumberLookup[ subRefListIndex ] = [ topLevelIndex, subRefIndex ];

	return this.footnoteNumberLookup[ subRefListIndex ];
};

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
 * List all reference listIndex's in the order they appear in the reflist including
 * named refs, unnamed refs, and those that don't resolve
 *
 * @private
 * @return {number[]}
 */
ve.dm.MWGroupReferences.prototype.getListIndexesInReflistOrder = function () {
	return Object.keys( this.footnoteNumberLookup )
		.map( ( s ) => Number( s ) )
		.sort( ( a, b ) => (
			( this.footnoteNumberLookup[ a ][ 0 ] - this.footnoteNumberLookup[ b ][ 0 ] ) ||
			( this.footnoteNumberLookup[ a ][ 1 ] - this.footnoteNumberLookup[ b ][ 1 ] )
		) );
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
		// Remove sub-refs
		.filter( ( listIndex ) => this.footnoteNumberLookup[ listIndex ][ 1 ] === -1 );
};

/**
 * Return the defining reference node for this key
 *
 * @see #getInternalModelNode
 *
 * @deprecated use {@link ve.dm.InternalListNodeGroup.getFirstNode} instead
 * @param {number} listIndex
 * @return {ve.dm.MWReferenceNode|undefined}
 */
ve.dm.MWGroupReferences.prototype.getRefNode = function ( listIndex ) {
	return this.nodeGroup && this.nodeGroup.firstNodes[ listIndex ];
};

/**
 * Return the internalList internal item if it exists.
 *
 * @see #getRefNode
 *
 * @param {number} listIndex
 * @return {ve.dm.InternalItemNode|undefined}
 */
ve.dm.MWGroupReferences.prototype.getInternalModelNode = function ( listIndex ) {
	const ref = this.getRefNode( listIndex );
	return ref && ref.getInternalItem();
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
 * @param {number} mainListIndex
 * @return {ve.dm.MWReferenceNode[]} List of subrefs for this parent not including re-uses
 */
ve.dm.MWGroupReferences.prototype.getSubrefs = function ( mainListIndex ) {
	return this.subRefsByMain[ mainListIndex ] || [];
};

/**
 * Lookup from listKey to a rendered footnote number or subref number like "1.2", in the
 * local content language.
 *
 * @deprecated TODO: push to presentation
 * @param {number} listIndex
 * @return {string} rendered number label
 */
ve.dm.MWGroupReferences.prototype.getIndexLabel = function ( listIndex ) {
	const num = this.footnoteNumberLookup[ listIndex ];
	if ( !num ) {
		return '…';
	}

	let label = ve.dm.MWDocumentReferences.static.contentLangDigits( num[ 0 ] );

	if ( num[ 1 ] !== -1 ) {
		// FIXME: RTL, and customization of the separator like with mw:referencedBy
		label += '.' + ve.dm.MWDocumentReferences.static.contentLangDigits( num[ 1 ] );
	}

	return label;
};

module.exports = ve.dm.MWGroupReferences;
