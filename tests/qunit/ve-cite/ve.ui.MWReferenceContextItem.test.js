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

QUnit.test( 'isEditable', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'simpleRef' );
	const context = {
		isMobile: () => false,
		getSurface: () => ( {
			getModel: () => new ve.dm.Surface( doc ),
			isReadOnly: () => false
		} )
	};

	// We know exactly where the ref node is, grab it from the document.
	const refNode = doc.getDocumentNode().children[ 0 ].children[ 1 ];
	const item = new ve.ui.MWReferenceContextItem( context, refNode );

	assert.true( item.isEditable() );
} );

QUnit.test( 'isEditable sub-references main', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'subReferencing' );
	const context = {
		isMobile: () => false,
		getSurface: () => ( {
			getModel: () => new ve.dm.Surface( doc ),
			isReadOnly: () => false
		} )
	};

	// We know exactly where the ref node is, grab it from the document.
	const subRefNode = doc.getDocumentNode().children[ 0 ].children[ 1 ];
	const item = new ve.ui.MWReferenceContextItem( context, subRefNode );

	assert.true( item.isEditable() );
} );

QUnit.test( 'isEditable sub-references empty main', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'simpleRefEmptyMain' );
	const context = {
		isMobile: () => false,
		getSurface: () => ( {
			getModel: () => new ve.dm.Surface( doc ),
			isReadOnly: () => false
		} )
	};

	// We know exactly where the ref node is, grab it from the document.
	const subRefNode = doc.getDocumentNode().children[ 0 ].children[ 0 ];
	const item = new ve.ui.MWReferenceContextItem( context, subRefNode );

	assert.false( item.isEditable() );
} );
