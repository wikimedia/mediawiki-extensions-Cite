'use strict';

/*!
 * VisualEditor DataModel MWReferenceModel class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

const MWReferenceKeyGenerator = require( './ve.dm.MWReferenceKeyGenerator.js' );

/**
 * Corresponds to one ref and its metadata, chosen for an action.
 *
 * TODO: Distinguish this module from ve.dm.MWReferenceNode
 *
 * @constructor
 * @mixes OO.EventEmitter
 * @param {ve.dm.Document} [parentDoc] The parent Document we can use to auto-generate a blank
 *  Document for the reference in case {@link #setDocument} was never called
 * @property {ve.dm.Document|Function|undefined} doc Might be deferred via a function, to be
 *  lazy-evaluated when {@link #getDocument} is called
 */
ve.dm.MWReferenceModel = function VeDmMWReferenceModel( parentDoc ) {
	// Mixin constructors
	OO.EventEmitter.call( this );

	// Properties
	/** @member {string}  */
	this.group = '';

	/** @member {string|undefined}  */
	this.mainRefKey = undefined;

	/** @member {number|undefined}  */
	this.mainListIndex = undefined;

	/** @member {string}  */
	this.listGroup = 'mwReference/' + this.group;

	/** @member {string}  */
	this.listKey = '';

	/** @member {number|undefined}  */
	this.listIndex = undefined;

	/**
	 * Document with the primary content of the reference
	 *
	 * @member {ve.dm.Document|function():ve.dm.Document|null}
	 */
	this.doc = null;

	/**
	 * Document with the main content in case of a sub-reference
	 *
	 * @member {ve.dm.Document|function():ve.dm.Document|null}
	 */
	this.mainDoc = null;

	if ( parentDoc ) {
		this.doc = () => parentDoc.cloneWithData( [
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			{ type: '/paragraph' },
			{ type: 'internalList' },
			{ type: '/internalList' }
		] );

		this.mainDoc = () => parentDoc.cloneWithData( [
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			{ type: '/paragraph' },
			{ type: 'internalList' },
			{ type: '/internalList' }
		] );
	}
};

/* Inheritance */

OO.mixinClass( ve.dm.MWReferenceModel, OO.EventEmitter );

/* Static Methods */

/**
 * Create a reference model from a reference internal item.
 *
 * @param {ve.dm.MWReferenceNode} node Reference node
 * @return {ve.dm.MWReferenceModel} Reference model
 */
ve.dm.MWReferenceModel.static.newFromReferenceNode = function ( node ) {
	const doc = node.getDocument();
	const internalList = doc.getInternalList();
	const attributes = node.getAttributes();
	const ref = new ve.dm.MWReferenceModel();

	ref.mainRefKey = attributes.mainRefKey;
	ref.mainListIndex = attributes.mainListIndex;
	ref.listGroup = attributes.listGroup;
	ref.listKey = attributes.listKey;
	ref.listIndex = attributes.listIndex;
	ref.group = attributes.refGroup;
	ref.doc = function () {
		// cloneFromRange is very expensive, so lazy evaluate it
		return doc.cloneFromRange( internalList.getItemNode( attributes.listIndex ).getRange() );
	};
	if ( ref.isSubRef() ) {
		ref.mainDoc = function () {
			// cloneFromRange is very expensive, so lazy evaluate it
			return doc.cloneFromRange( internalList.getItemNode( attributes.mainListIndex ).getRange() );
		};
	}

	return ref;
};

/**
 * Create a copy of a sub-reference to split it up from reuses.  A new
 * listKey and listIndex will be set when inserting into the document.
 *
 * @param {ve.dm.MWReferenceModel} oldSubRef The sub-reference to copy
 * @param {ve.dm.Document} doc The Document we can use to clone the content
 * @return {ve.dm.MWReferenceModel}
 */
ve.dm.MWReferenceModel.static.copySubReference = function ( oldSubRef, doc ) {
	const newSubRef = new ve.dm.MWReferenceModel();

	// Clone the content of the exiting sub-ref into a new one
	const originalData = oldSubRef.getDocument().getData();
	const internalListRange = oldSubRef.getDocument().internalList.getListNode().getRange();

	// Remove the InternalList's content from the original data to avoid duplicating it when merging the ref
	originalData.splice( internalListRange.start, internalListRange.end - internalListRange.start );
	newSubRef.setDocument( doc.cloneWithData( originalData ) );

	newSubRef.mainRefKey = oldSubRef.mainRefKey;
	newSubRef.mainListIndex = oldSubRef.mainListIndex;
	newSubRef.mainDoc = oldSubRef.mainDoc;
	newSubRef.setGroup( oldSubRef.getGroup() );

	return newSubRef;
};

/* Methods */

/**
 * Find matching item in a surface.
 *
 * @param {ve.dm.Surface} surfaceModel Surface reference is in
 * @return {ve.dm.InternalItemNode|undefined} Internal reference item, undefined if none exists
 */
ve.dm.MWReferenceModel.prototype.findInternalItem = function ( surfaceModel ) {
	return surfaceModel.getDocument().getInternalList().getItemNode( this.listIndex );
};

/**
 * Insert reference internal item into a surface.
 *
 * If the internal item for this reference doesn't exist, use this method to create one.
 * The inserted reference is empty and auto-numbered.
 *
 * @param {ve.dm.Surface} surfaceModel Surface model of main document
 */
ve.dm.MWReferenceModel.prototype.insertInternalItem = function ( surfaceModel ) {
	// Create new internal item
	const doc = surfaceModel.getDocument();
	const internalList = doc.getInternalList();

	// Fill in data
	this.listGroup = 'mwReference/' + this.group;
	this.listKey = MWReferenceKeyGenerator.makeListKey( internalList );

	// Insert internal reference item into document
	const item = internalList.getItemInsertion( this.listGroup, this.listKey, [] );
	surfaceModel.change( item.transaction );
	this.listIndex = item.index;

	// Inject reference document into internal reference item
	surfaceModel.change(
		ve.dm.TransactionBuilder.static.newFromDocumentInsertion(
			doc,
			internalList.getItemNode( item.index ).getRange().start,
			this.getDocument()
		)
	);
};

/**
 * Synchronize internal data structures and document to reflect a possibly changed group name.
 *
 * @param {ve.dm.Surface} surfaceModel Surface model of main document
 */
ve.dm.MWReferenceModel.prototype.updateGroup = function ( surfaceModel ) {
	const newListGroup = 'mwReference/' + this.group;
	if ( this.listGroup === newListGroup ) {
		return;
	}

	const doc = surfaceModel.getDocument();
	const internalList = doc.getInternalList();

	// Get all reference nodes with the same group and key
	const oldNodeGroup = internalList.getNodeGroup( this.listGroup );
	const refNodes = oldNodeGroup.getAllReuses( this.listKey );

	// Check for name collision when moving items between groups
	const newNodeGroup = internalList.getNodeGroup( newListGroup );
	const isUsed = newNodeGroup && newNodeGroup.getFirstNode( this.listKey );
	const newListKey = isUsed ? MWReferenceKeyGenerator.makeListKey( internalList ) : this.listKey;

	// Update the group name of all references nodes with the same group and key
	const txs = [];
	for ( let i = 0, len = refNodes.length; i < len; i++ ) {
		txs.push( ve.dm.TransactionBuilder.static.newFromAttributeChanges(
			doc,
			refNodes[ i ].getOuterRange().start,
			{
				// This is the new group from the form
				refGroup: this.group,
				listGroup: newListGroup,
				listKey: newListKey
			}
		) );
	}
	surfaceModel.change( txs );
	this.listGroup = newListGroup;
	this.listKey = newListKey;
};

/**
 * Update an internal reference item.
 *
 * An internal item for the reference will be created if no `ref` argument is given.
 *
 * @param {ve.dm.Surface} surfaceModel Surface model of main document
 */
ve.dm.MWReferenceModel.prototype.updateInternalItem = function ( surfaceModel ) {
	const doc = surfaceModel.getDocument();
	const internalList = doc.getInternalList();

	// Update internal node content
	const itemNodeRange = internalList.getItemNode( this.listIndex ).getRange();
	surfaceModel.change(
		ve.dm.TransactionBuilder.static
			.newFromRemoval( doc, itemNodeRange, true ) );
	surfaceModel.change(
		ve.dm.TransactionBuilder.static
			.newFromDocumentInsertion( doc, itemNodeRange.start, this.getDocument() ) );
};

/**
 * Insert a reference node at the end of a surface fragment. Will also add an internal item
 * if needed.
 *
 * @param {ve.dm.SurfaceFragment} surfaceFragment Surface fragment to insert at
 * @param {boolean} [contentsUsed] If the new node should get the contentsUsed flag
 */
ve.dm.MWReferenceModel.prototype.insertIntoFragment = function ( surfaceFragment, contentsUsed ) {
	const surfaceModel = surfaceFragment.getSurface();

	if ( !this.findInternalItem( surfaceModel ) ) {
		this.insertInternalItem( surfaceModel );
	}
	this.insertReferenceNode( surfaceFragment, { contentsUsed } );
};

/**
 * Insert a reference node at the end of a surface fragment.
 *
 * @param {ve.dm.SurfaceFragment} surfaceFragment Surface fragment to insert at
 * @param {Object} [attributes] Additional attributes
 * @param {boolean} [attributes.placeholder=false] Reference is a placeholder for staging purposes
 * @param {boolean} [attributes.contentsUsed=false] If the new node should get the contentsUsed flag
 */
ve.dm.MWReferenceModel.prototype.insertReferenceNode = function ( surfaceFragment, attributes ) {
	// Temporary backwards-compatibility with Citoid
	if ( typeof attributes === 'boolean' ) {
		attributes = { placeholder: attributes };
	}

	surfaceFragment.insertContent( [
		{
			type: 'mwReference',
			attributes: Object.assign( {
				mainRefKey: this.mainRefKey,
				mainListIndex: this.mainListIndex,
				listGroup: this.listGroup,
				listKey: this.listKey,
				listIndex: this.listIndex,
				refGroup: this.group
			}, attributes ),
			// See ve.dm.MWReferenceNode.static.cloneElement
			originalDomElementsHash: Math.random()
		},
		{ type: '/mwReference' }
	] );
};

/**
 * @return {string} The reference's list group name with the "mwReference/" prefix
 */
ve.dm.MWReferenceModel.prototype.getListGroup = function () {
	return this.listGroup;
};

/**
 * Get the key of a reference in the references list.
 *
 * @return {string} Reference's list key
 */
ve.dm.MWReferenceModel.prototype.getListKey = function () {
	return this.listKey;
};

/**
 * Get the index of reference in the references list.
 *
 * @return {number} Reference's index
 */
ve.dm.MWReferenceModel.prototype.getListIndex = function () {
	return this.listIndex;
};

/**
 * @return {string} The reference's plain list group name without any prefix
 */
ve.dm.MWReferenceModel.prototype.getGroup = function () {
	return this.group;
};

/**
 * Get the document with the primary content of the reference.
 *
 * Auto-generates a blank document if no document exists.
 *
 * @return {ve.dm.Document} The (small) document with the primary content of the reference
 */
ve.dm.MWReferenceModel.prototype.getDocument = function () {
	if ( typeof this.doc === 'function' ) {
		this.doc = this.doc();
	}
	return this.doc;
};

/**
 * Get the document with the main content in case of a sub-reference.
 *
 * Auto-generates a blank document if no document exists. Null if the reference is not
 * a sub-reference.
 *
 * @return {ve.dm.Document|null} The (small) document with the main content of the sub-reference
 */
ve.dm.MWReferenceModel.prototype.getMainDocument = function () {
	if ( !this.isSubRef() ) {
		return null;
	}
	if ( typeof this.mainDoc === 'function' ) {
		this.mainDoc = this.mainDoc();
	}
	return this.mainDoc;
};

/**
 * @return {boolean}
 */
ve.dm.MWReferenceModel.prototype.isSubRef = function () {
	return this.mainListIndex !== undefined;
};

/**
 * @param {string} group The reference's plain list group name without any prefix
 */
ve.dm.MWReferenceModel.prototype.setGroup = function ( group ) {
	this.group = group;
	// For a moment this.listGroup holds the old value until this.updateGroup() got called
};

/**
 * Set the reference document.
 *
 * @param {ve.dm.Document} doc The (small) document with the content of the reference
 */
ve.dm.MWReferenceModel.prototype.setDocument = function ( doc ) {
	this.doc = doc;
};

module.exports = ve.dm.MWReferenceModel;
