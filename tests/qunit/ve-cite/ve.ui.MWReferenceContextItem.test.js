'use strict';

QUnit.module( 've.ui.MWReferenceContextItem (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'getInternalItemNode', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	// XXX: This is a regression test with a fragile setup. Please feel free to delete this test
	// when you feel like it doesn't make sense to update it.
	const context = {
		isMobile: () => false,
		getSurface: () => ( {
			getModel: () => new ve.dm.Surface( doc ),
			isReadOnly: () => false
		} )
	};

	const node = new ve.dm.MWReferenceNode();
	node.setDocument( doc );
	const item = new ve.ui.MWReferenceContextItem( context, node );

	assert.strictEqual( item.getInternalItemNode(), null );
} );

QUnit.test( 'getInternalItemNode: make sure we get a node', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const context = {
		isMobile: () => false,
		getSurface: () => ( {
			getModel: () => new ve.dm.Surface( doc ),
			isReadOnly: () => false
		} )
	};

	const node = new ve.dm.MWReferenceNode( {
		attributes: { listIndex: 0 }
	} );
	node.setDocument( doc );
	const item = new ve.ui.MWReferenceContextItem( context, node );

	assert.strictEqual( item.getInternalItemNode().getType(), 'internalItem' );
} );
