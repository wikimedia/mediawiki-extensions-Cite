'use strict';

/*!
 * @copyright 2024 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * A facade providing a simplified and safe interface to Cite `ref` and
 * `references` tags in a document.
 *
 * @constructor
 * @mixes OO.EventEmitter
 * @param {ve.dm.Document} doc The document that reference tags will be embedded in.
 */
ve.dm.MWDocumentReferences = function VeDmMWDocumentReferences( doc ) {
	// Mixin constructors
	OO.EventEmitter.call( this );

	// Properties
	this.doc = doc;
};

/* Inheritance */

OO.mixinClass( ve.dm.MWDocumentReferences, OO.EventEmitter );

/* Methods */

/**
 * Singleton MWDocumentReferences for a document.
 *
 * @param {ve.dm.Document} doc Source document associated with the singleton
 * @return {ve.dm.MWDocumentReferences} Singleton docRefs
 */
ve.dm.MWDocumentReferences.static.refsForDoc = function ( doc ) {
	let docRefs = doc.getStorage( 'document-references-store' );
	if ( docRefs === undefined ) {
		docRefs = new ve.dm.MWDocumentReferences( doc );
		doc.setStorage( 'document-references-store', docRefs );
	}
	return docRefs;
};

/**
 * Get all refs for a group, organized by parent ref
 *
 * This is appropriate when rendering a reflist organized hierarchically by
 * subrefs using the `extends` feature.
 *
 * @param {string} groupName Filter by this group.
 * @return {Object.<string, ve.dm.MWReferenceNode[]>} Mapping from parent ref
 * name to a list of its subrefs.  Note that the top-level refs are under the
 * `null` value.
 */
ve.dm.MWDocumentReferences.prototype.getGroupRefsByParents = function ( groupName ) {
	const nodeGroup = this.doc.getInternalList().getNodeGroup( groupName );
	const indexOrder = ( nodeGroup ? nodeGroup.indexOrder : [] );
	// Compile a list of all top-level node names so that we can handle orphans
	// while keeping them in document order.
	const seenTopLevelNames = new Set(
		indexOrder
			.map( ( index ) => nodeGroup.firstNodes[ index ] )
			.filter( ( node ) => !node.element.attributes.extendsRef )
			.map( ( node ) => node.element.attributes.listKey )
			.filter( ( listKey ) => listKey )
	);
	// Group nodes by parent ref, while iterating in order of document appearance.
	return indexOrder.reduce( ( acc, index ) => {
		const node = nodeGroup.firstNodes[ index ];
		let extendsRef = node.element.attributes.extendsRef || '';

		if ( !seenTopLevelNames.has( extendsRef ) ) {
			// Promote orphaned subrefs to become top-level refs.
			// TODO: Ideally this would be handled by creating placeholder error
			// nodes as is done by the renderer.
			extendsRef = '';
		}

		if ( acc[ extendsRef ] === undefined ) {
			acc[ extendsRef ] = [];
		}
		acc[ extendsRef ].push( node );

		return acc;
	}, {} );
};
