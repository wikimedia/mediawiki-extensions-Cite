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
	this.cachedByGroup = {};

	doc.getInternalList().connect( this, { update: 'updateGroups' } );
	this.updateAllGroups();
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
 * @private
 */
ve.dm.MWDocumentReferences.prototype.updateAllGroups = function () {
	const nodes = this.doc.getInternalList().getNodeGroups();
	this.updateGroups( Object.keys( nodes ) );
};

/**
 * @private
 * @param {string[]} groupsChanged A list of group names which have changed in
 *  this transaction
 */
ve.dm.MWDocumentReferences.prototype.updateGroups = function ( groupsChanged ) {
	groupsChanged.forEach( ( groupName ) => this.updateGroup( groupName ) );
};

/**
 * @private
 * @param {string[]} groupName Name of the reference group which needs to be
 *  updated
 */
ve.dm.MWDocumentReferences.prototype.updateGroup = function ( groupName ) {
	const refsByParent = this.getGroupRefsByParents( groupName );
	const topLevelNodes = refsByParent[ '' ] || [];

	const indexNumberLookup = {};
	for ( let i = 0; i < topLevelNodes.length; i++ ) {
		const topLevelNode = topLevelNodes[ i ];
		const topLevelKey = topLevelNode.getAttribute( 'listKey' );
		indexNumberLookup[ topLevelKey ] = ve.dm.MWDocumentReferences.static.contentLangDigits( i + 1 );
		const subrefs = ( refsByParent[ topLevelKey ] || [] );
		for ( let j = 0; j < subrefs.length; j++ ) {
			const subrefNode = subrefs[ j ];
			const subrefKey = subrefNode.getAttribute( 'listKey' );
			// FIXME: RTL, and customization of the separator like with mw:referencedBy
			indexNumberLookup[ subrefKey ] = `${ ve.dm.MWDocumentReferences.static.contentLangDigits( i + 1 ) }.${ ve.dm.MWDocumentReferences.static.contentLangDigits( j + 1 ) }`;
		}
	}
	this.cachedByGroup[ groupName ] = indexNumberLookup;
};

/**
 * Return a formatted number, in the content script, with no separators.
 *
 * Partial clone of mw.language.convertNumber .
 *
 * @param {number} num
 * @return {string}
 */
ve.dm.MWDocumentReferences.static.contentLangDigits = function ( num ) {
	const contentLang = mw.config.get( 'wgContentLanguage' );
	const digitLookup = mw.language.getData( contentLang, 'digitTransformTable' );
	const numString = String( num );
	if ( !digitLookup ) {
		return numString;
	}
	return numString.split( '' ).map( ( numChar ) => digitLookup[ numChar ] ).join( '' );
};

/**
 * @deprecated Should be refactored to store index numbers as a simple property
 *  in each ref node after document transaction.
 * @param {string} groupName Ref group without prefix
 * @param {string} listKey Ref key with prefix
 * @return {string} Rendered index number string which can be used as a footnote
 *  marker or reflist item number.
 */
ve.dm.MWDocumentReferences.prototype.getIndexNumber = function ( groupName, listKey ) {
	return ( this.cachedByGroup[ 'mwReference/' + groupName ] || {} )[ listKey ];
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
			.filter( ( node ) => node && !node.element.attributes.extendsRef && !node.element.attributes.placeholder )
			.map( ( node ) => node.element.attributes.listKey )
			.filter( ( listKey ) => listKey )
	);

	// Group nodes by parent ref, while iterating in order of document appearance.
	return indexOrder.reduce( ( acc, index ) => {
		const node = nodeGroup.firstNodes[ index ];
		if ( !node || node.element.attributes.placeholder ) {
			return acc;
		}

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
