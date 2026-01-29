'use strict';

/*!
 * VisualEditor DataModel MWReferenceModel class.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

/**
 * Corresponds to one ref and its metadata, chosen for an action.
 *
 * TODO: Distinguish this module from ve.dm.MWReferenceNode
 *
 * @constructor
 * @mixes OO.EventEmitter
 * @param {ve.dm.Document} [parentDoc] The parent Document we can use to auto-generate a blank
 *  Document for the reference in case {@link setDocument} was never called
 * @property {ve.dm.Document|Function|undefined} doc Might be deferred via a function, to be
 *  lazy-evaluated when {@link getDocument} is called
 */
ve.dm.MWReferenceModel = function VeDmMWReferenceModel( parentDoc ) {
	// Mixin constructors
	OO.EventEmitter.call( this );

	// Properties
	this.mainRefKey = null;
	this.mainListIndex = null;
	this.listKey = '';
	this.listGroup = '';
	this.listIndex = null;
	this.group = '';
	if ( parentDoc ) {
		this.doc = () => parentDoc.cloneWithData( [
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
	ref.listKey = attributes.listKey;
	ref.listGroup = attributes.listGroup;
	ref.listIndex = attributes.listIndex;
	ref.group = attributes.refGroup;
	ref.doc = function () {
		// cloneFromRange is very expensive, so lazy evaluate it
		return doc.cloneFromRange( internalList.getItemNode( attributes.listIndex ).getRange() );
	};

	return ref;
};

/**
 * Create a copy of a sub-reference to split it up from reuses.
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
	newSubRef.setGroup( oldSubRef.getGroup() );

	return newSubRef;
};

/* Methods */

/**
 * Find matching item in a surface.
 *
 * @param {ve.dm.Surface} surfaceModel Surface reference is in
 * @return {ve.dm.InternalItemNode|null} Internal reference item, null if none exists
 */
ve.dm.MWReferenceModel.prototype.findInternalItem = function ( surfaceModel ) {
	if ( this.listIndex !== null ) {
		return surfaceModel.getDocument().getInternalList().getItemNode( this.listIndex );
	}
	return null;
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
	this.listKey = 'auto/' + internalList.getNextUniqueNumber();
	this.listGroup = 'mwReference/' + this.group;

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
 * Update an internal reference item.
 *
 * An internal item for the reference will be created if no `ref` argument is given.
 *
 * @param {ve.dm.Surface} surfaceModel Surface model of main document
 */
ve.dm.MWReferenceModel.prototype.updateInternalItem = function ( surfaceModel ) {
	const doc = surfaceModel.getDocument();
	const internalList = doc.getInternalList();
	const newListGroup = 'mwReference/' + this.group;

	// Group/key has changed
	if ( this.listGroup !== newListGroup ) {
		// Get all reference nodes with the same group and key
		const oldNodeGroup = internalList.getNodeGroup( this.listGroup );
		const refNodes = oldNodeGroup.getAllReuses( this.listKey );

		// Check for name collision when moving items between groups
		const newNodeGroup = internalList.getNodeGroup( newListGroup );
		const isUsed = newNodeGroup || newNodeGroup.getFirstNode( this.listKey );
		const newListKey = isUsed ? 'auto/' + internalList.getNextUniqueNumber() : this.listKey;

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
	}

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
				listKey: this.listKey,
				listGroup: this.listGroup,
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
 * Get the key of a reference in the references list.
 *
 * @return {string} Reference's list key
 */
ve.dm.MWReferenceModel.prototype.getListKey = function () {
	return this.listKey;
};

/**
 * Get the name of the group a references list is in.
 *
 * @return {string} References list's group
 */
ve.dm.MWReferenceModel.prototype.getListGroup = function () {
	return this.listGroup;
};

/**
 * Get the index of reference in the references list.
 *
 * @return {string} Reference's index
 */
ve.dm.MWReferenceModel.prototype.getListIndex = function () {
	return this.listIndex;
};

/**
 * Get the name of the group a reference is in.
 *
 * @return {string} Reference's group
 */
ve.dm.MWReferenceModel.prototype.getGroup = function () {
	return this.group;
};

/**
 * Get reference document.
 *
 * Auto-generates a blank document if no document exists.
 *
 * @return {ve.dm.Document} The (small) document with the content of the reference
 */
ve.dm.MWReferenceModel.prototype.getDocument = function () {
	if ( typeof this.doc === 'function' ) {
		this.doc = this.doc();
	}
	return this.doc;
};

/**
 * @return {boolean}
 */
ve.dm.MWReferenceModel.prototype.isSubRef = function () {
	return !!this.mainRefKey;
};

/**
 * Set the name of the group a reference is in.
 *
 * @param {string} group Reference's group
 */
ve.dm.MWReferenceModel.prototype.setGroup = function ( group ) {
	this.group = group;
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
