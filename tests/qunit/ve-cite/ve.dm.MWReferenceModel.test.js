'use strict';

/*!
 * @license MIT
 */

QUnit.module( 've.dm.MWReferenceModel (Cite)', ve.test.utils.newMwEnvironment() );

/* Tests */

QUnit.test( 'find an unknown ref', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const surface = new ve.dm.Surface( doc );

	const refNode = new ve.dm.MWReferenceNode( {
		type: 'mwReference',
		attributes: {},
		originalDomElementsHash: Math.random()
	} );
	refNode.setDocument( doc );
	const refModel = ve.dm.MWReferenceModel.static.newFromReferenceNode( refNode );
	// TODO: Callers might be surprised, the docs hint that a missing entry results in `null`.
	assert.strictEqual( refModel.findInternalItem( surface ), undefined );
} );

QUnit.test( 'find a known ref', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const surface = new ve.dm.Surface( doc );

	// We know exactly where the third ref node is, grab it from the document.
	const refNode = doc.getDocumentNode().children[ 1 ].children[ 3 ];
	const refModel = ve.dm.MWReferenceModel.static.newFromReferenceNode( refNode );
	const found = refModel.findInternalItem( surface );
	assert.strictEqual( found.type, 'internalItem' );
} );

QUnit.test( 'insert new ref', ( assert ) => {
	const doc = new ve.dm.Document( [
		{ type: 'paragraph', internal: { generated: 'empty' } },
		{ type: '/paragraph' },
		{ type: 'internalList' },
		{ type: '/internalList' }
	] );
	const surface = new ve.dm.Surface( doc );
	const internalList = doc.getInternalList();

	// Create a new, blank reference model linked to the doc.
	const refModel = new ve.dm.MWReferenceModel( doc );

	const oldNodeCount = internalList.getItemNodeCount();
	const oldDocLength = doc.getLength();

	refModel.insertInternalItem( surface );
	assert.strictEqual( internalList.getItemNodeCount(), oldNodeCount + 1, 'internalItem added' );

	surface.setLinearSelection( new ve.Range( 1 ) );
	refModel.insertReferenceNode( surface.getFragment().collapseToEnd() );
	assert.strictEqual( doc.getLength(), oldDocLength + 6, 'mwReference added to document' );

	refModel.updateInternalItem( surface );
	assert.strictEqual(
		internalList.getNodeGroup( 'mwReference/' ).getAllReuses( 'auto/0' ).length,
		1,
		'keyedNodes tracks the ref after insertion'
	);
} );

QUnit.test( 'insert ref reuse', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'simpleRef' );
	const surface = new ve.dm.Surface( doc );
	const internalList = doc.getInternalList();

	// Get a ref model from the existing reference node
	const refNode = doc.getDocumentNode().children[ 0 ].children[ 1 ];
	const refModel = ve.dm.MWReferenceModel.static.newFromReferenceNode( refNode );

	const oldNodeCount = internalList.getItemNodeCount();
	assert.strictEqual(
		internalList.getNodeGroup( 'mwReference/' ).getAllReuses( 'auto/0' ).length,
		1,
		'Initial document does only count one use of the ref'
	);

	surface.setLinearSelection( new ve.Range( 1 ) );
	// Insert a new node using the ref model ( creating a reuse )
	refModel.insertReferenceNode( surface.getFragment().collapseToEnd() );

	assert.strictEqual(
		internalList.getItemNodeCount(),
		oldNodeCount,
		'itemNodeCount does not change on reuse'
	);
	assert.strictEqual(
		doc.getDocumentNode().children[ 0 ].children[ 0 ].getAttribute( 'contentsUsed' ),
		undefined,
		'Reuse was added at the beginning of the doc'
	);
	assert.true(
		doc.getDocumentNode().children[ 0 ].children[ 2 ].getAttribute( 'contentsUsed' ),
		'Original node is now 2nd'
	);
	assert.strictEqual(
		internalList.getNodeGroup( 'mwReference/' ).getAllReuses( 'auto/0' ).length,
		2,
		'Reuse count of the node increases'
	);
} );

QUnit.test( 'update internal item changing the group', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'simpleRef' );
	const surface = new ve.dm.Surface( doc );
	const internalList = doc.getInternalList();

	// Get a ref model from the existing reference node
	const refNode = doc.getDocumentNode().children[ 0 ].children[ 1 ];
	const refModel = ve.dm.MWReferenceModel.static.newFromReferenceNode( refNode );

	const oldNodeCount = internalList.getItemNodeCount();
	assert.strictEqual(
		internalList.getNodeGroup( 'mwReference/' ).getAllReuses( 'auto/0' ).length,
		1,
		'Initial document does count one use of the ref in the default group'
	);

	// Change the group model and update the internal item accroding to the changed model
	refModel.group = 'other';
	refModel.updateInternalItem( surface );

	assert.strictEqual(
		internalList.getItemNodeCount(),
		oldNodeCount,
		'itemNodeCount does not change on update'
	);
	assert.strictEqual(
		internalList.getNodeGroup( 'mwReference/' ).getAllReuses( 'auto/0' ),
		undefined,
		'Ref is not part of the default group anymore'
	);
	assert.strictEqual(
		internalList.getNodeGroup( 'mwReference/other' ).getAllReuses( 'auto/0' ).length,
		1,
		'Ref can be found in the new group'
	);
} );
