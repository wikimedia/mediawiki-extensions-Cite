'use strict';

QUnit.module( 've.ui.MWReferenceContextItem (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'getInternalItemNode', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'simpleRef' );
	const context = {
		isMobile: () => false,
		getSurface: () => ( {
			getModel: () => new ve.dm.Surface( doc ),
			isReadOnly: () => false
		} )
	};

	const emptyRefNode = new ve.dm.MWReferenceNode();
	emptyRefNode.setDocument( doc );
	let item = new ve.ui.MWReferenceContextItem( context, emptyRefNode );
	assert.false( !!item.getInternalItemNode() );

	// We know exactly where the ref node is, grab it from the document.
	const refNode = doc.getDocumentNode().children[ 0 ].children[ 1 ];
	item = new ve.ui.MWReferenceContextItem( context, refNode );
	assert.strictEqual( item.getInternalItemNode().getType(), 'internalItem' );
} );
