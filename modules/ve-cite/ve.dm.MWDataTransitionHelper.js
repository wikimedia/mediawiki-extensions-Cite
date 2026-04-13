'use strict';

/*!
 * @copyright 2026 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * A facade providing a safe interface to wrap methods using a reference's listIndex
 * each with a fallback using listKey and listGroup.
 *
 * @constructor
 */
ve.dm.MWDataTransitionHelper = function VeDmMWDataTransitionHelper() {
};

/**
 * @param {ve.dm.InternalListNodeGroup|undefined} nodeGroup
 * @return {Object} footnote number lookup
 */
ve.dm.MWDataTransitionHelper.prototype.buildReflistNumbering = function ( nodeGroup ) {
	const footnoteNumberLookup = {};
	const subRefsByMain = {};
	let topLevelCounter = 1;

	const getOrAllocateTopLevelNumber = function ( listIndex ) {
		if ( !( listIndex in footnoteNumberLookup ) ) {
			const number = topLevelCounter++;
			footnoteNumberLookup[ listIndex ] = {
				internalListIndex: listIndex,
				topLevelNumber: number,
				label: ve.dm.MWDocumentReferences.static.contentLangDigits( number )
			};
		}
		return footnoteNumberLookup[ listIndex ].topLevelNumber;
	};

	const addSubref = function ( mainListIndex, subRefIndex, subRefNode ) {
		if ( !( mainListIndex in subRefsByMain ) ) {
			subRefsByMain[ mainListIndex ] = [];
		}
		subRefsByMain[ mainListIndex ].push( subRefNode );
		const subRefPos = subRefsByMain[ mainListIndex ].length;

		const topLevelNumber = getOrAllocateTopLevelNumber( mainListIndex );
		footnoteNumberLookup[ subRefIndex ] = {
			internalListIndex: subRefIndex,
			mainListIndex: mainListIndex,
			topLevelNumber: topLevelNumber,
			subrefNumber: subRefPos,
			label: ve.dm.MWDocumentReferences.static.contentLangDigits( topLevelNumber ) +
				// FIXME: RTL, and customization of the separator like with mw:referencedBy
				'.' + ve.dm.MWDocumentReferences.static.contentLangDigits( subRefPos )
		};

		return footnoteNumberLookup[ subRefIndex ];
	};

	nodeGroup.getFirstNodesInIndexOrder()
		.filter( ( node ) => !node.getAttribute( 'placeholder' ) )
		.forEach( ( node ) => {
			// debugger;
			const listIndex = node.getAttribute( 'listIndex' );
			const mainListIndex = node.getAttribute( 'mainListIndex' );
			if ( mainListIndex !== undefined ) {
				addSubref( mainListIndex, listIndex, node );
			} else {
				getOrAllocateTopLevelNumber( listIndex );
			}
			// TODO: decide how to handle setGroupIndex side effect.
		} );

	return footnoteNumberLookup;
};

/**
 * @param {ve.dm.InternalListNodeGroup|undefined} nodeGroup
 * @return {Object} nested structure with main and subref information
 */
ve.dm.MWDataTransitionHelper.prototype.buildReflistStructure = function ( nodeGroup ) {
	const footnoteNumberLookup = this.buildReflistNumbering( nodeGroup );

	// Get just the top-level refs.
	const topLevelIndexes = Object.keys( footnoteNumberLookup )
		.filter( ( listIndex ) => footnoteNumberLookup[ listIndex ].subrefNumber === undefined );

	// Build an array of top-level refs, and include their subrefs. Sort in footnote number order.
	const nestedRefs = [];

	topLevelIndexes
		.sort( ( a, b ) => footnoteNumberLookup[ a ].topLevelNumber - footnoteNumberLookup[ b ].topLevelNumber )
		.forEach( ( mainListIndex ) => {
			const subrefs =
			Object.keys( footnoteNumberLookup )
				// Get all subrefs of one main ref.
				.filter( ( listIndex ) => footnoteNumberLookup[ listIndex ].mainListIndex === Number( mainListIndex ) )
				// Put them in number order.
				.sort( ( a, b ) => footnoteNumberLookup[ a ].subrefNumber - footnoteNumberLookup[ b ].subrefNumber )
				// Get the lookup object for each subref.
				.map( ( subrefIndex ) => footnoteNumberLookup[ subrefIndex ] );

			nestedRefs.push( ve.extendObject( {}, footnoteNumberLookup[ mainListIndex ], { subrefs } ) );
		} );
	return nestedRefs;
};

module.exports = ve.dm.MWDataTransitionHelper;
