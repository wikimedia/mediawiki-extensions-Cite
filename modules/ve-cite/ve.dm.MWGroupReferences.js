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
 * @private
 * @constructor
 */
ve.dm.MWGroupReferences = function VeDmMWGroupReferences() {
	// Mixin constructors
	OO.EventEmitter.call( this );

	// Properties
	this.footnoteNumberLookup = {};
	// FIXME: push labeling to presentation code and drop from here.
	this.footnoteLabelLookup = {};
	this.subRefsByParent = {};

	/** @private */
	this.topLevelCounter = 1;
	this.nodeGroup = null;
};

/* Inheritance */

OO.initClass( ve.dm.MWGroupReferences );

/* Static Methods */

/**
 * Rebuild information about this group of references.
 *
 * @param {Object} nodeGroup InternalList group object containing refs.
 * @return {ve.dm.MWGroupReferences}
 */
ve.dm.MWGroupReferences.static.makeGroupRefs = function ( nodeGroup ) {
	const result = new ve.dm.MWGroupReferences();
	result.nodeGroup = nodeGroup;

	( nodeGroup ? nodeGroup.indexOrder : [] )
		.map( ( index ) => nodeGroup.firstNodes[ index ] )
		// FIXME: debug null nodes
		.filter( ( node ) => node && !node.getAttribute( 'placeholder' ) )
		.forEach( ( node ) => {
			const listKey = node.getAttribute( 'listKey' );
			const extendsRef = node.getAttribute( 'extendsRef' );

			if ( !extendsRef ) {
				result.getOrAllocateTopLevelIndex( listKey );
			} else {
				result.addSubref( extendsRef, listKey, node );
			}
		} );

	return result;
};

/* Methods */

/**
 * @private
 * @param {string} listKey Full key for the top-level ref
 * @return {number[]} Allocated topLevelIndex
 */
ve.dm.MWGroupReferences.prototype.getOrAllocateTopLevelIndex = function ( listKey ) {
	if ( this.footnoteNumberLookup[ listKey ] === undefined ) {
		const number = this.topLevelCounter++;
		this.footnoteNumberLookup[ listKey ] = [ number, -1 ];
		this.footnoteLabelLookup[ listKey ] = ve.dm.MWDocumentReferences.static.contentLangDigits( number );
	}
	return this.footnoteNumberLookup[ listKey ][ 0 ];
};

/**
 * @private
 * @param {string} parentKey Full key of the parent reference
 * @param {string} listKey Full key of the subreference
 * @param {ve.dm.MWReferenceNode} subrefNode Subref to add to internal tracking
 */
ve.dm.MWGroupReferences.prototype.addSubref = function ( parentKey, listKey, subrefNode ) {
	if ( this.subRefsByParent[ parentKey ] === undefined ) {
		this.subRefsByParent[ parentKey ] = [];
	}
	this.subRefsByParent[ parentKey ].push( subrefNode );
	const subrefIndex = this.subRefsByParent[ parentKey ].length;

	const topLevelIndex = this.getOrAllocateTopLevelIndex( parentKey );
	this.footnoteNumberLookup[ listKey ] = [ topLevelIndex, subrefIndex ];
	this.footnoteLabelLookup[ listKey ] = ve.dm.MWDocumentReferences.static.contentLangDigits( topLevelIndex ) +
		// FIXME: RTL, and customization of the separator like with mw:referencedBy
		'.' + ve.dm.MWDocumentReferences.static.contentLangDigits( subrefIndex );
};

/**
 * @return {ve.dm.MWReferenceNode[]}
 */
ve.dm.MWGroupReferences.prototype.getAllRefsInDocumentOrder = function () {
	return Object.keys( this.footnoteNumberLookup )
		.sort( ( aKey, bKey ) => this.footnoteNumberLookup[ aKey ][ 0 ] - this.footnoteNumberLookup[ bKey ][ 0 ] )
		.map( ( listKey ) => this.nodeGroup.keyedNodes[ listKey ] )
		.filter( ( nodes ) => !!nodes )
		.map( ( nodes ) => nodes[ 0 ] );
};

/**
 * @param {string} parentKey parent ref key
 * @return {ve.dm.MWReferenceNode[]} List of subrefs for this parent
 */
ve.dm.MWGroupReferences.prototype.getSubrefs = function ( parentKey ) {
	return this.subRefsByParent[ parentKey ] || [];
};

/**
 * @deprecated TODO: push to presentation
 * @param {string} listKey full ref key
 * @return {string} rendered number label
 */
ve.dm.MWGroupReferences.prototype.getIndexLabel = function ( listKey ) {
	return this.footnoteLabelLookup[ listKey ];
};
