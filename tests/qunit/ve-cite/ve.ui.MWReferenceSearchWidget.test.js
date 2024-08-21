'use strict';

QUnit.module( 've.ui.MWReferenceSearchWidget (Cite)', ve.test.utils.newMwEnvironment() );

function getInternalListMock( hasNode ) {
	const listKey = 'literal/foo';
	const node = hasNode ? {
		getAttribute: ( name ) => {
			switch ( name ) {
				case 'listKey': return listKey;
				default: return undefined;
			}
		},
		getAttributes: () => ( {} )
	} : {};
	const groups = hasNode ? {
		'mwReference/': {
			indexOrder: [ 0 ]
		}
	} : {};
	const docRefsMock = {
		getAllGroupNames: () => ( Object.keys( groups ) ),
		getGroupRefsByParents: () => ( { '': [ node ] } ),
		getIndexLabel: () => ( '1' ),
		getItemNode: () => ( node )
	};
	const docMock = {
		getStorage: () => ( docRefsMock ),
		getOriginalDocument: () => ( null )
	};
	const mockInternalList = {
		getDocument: () => ( docMock ),
		getNodeGroups: () => ( groups ),
		getItemNode: () => ( node )
	};
	docMock.getInternalList = () => ( mockInternalList );
	node.getDocument = () => ( docMock );

	return mockInternalList;
}

QUnit.test( 'buildIndex', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	widget.internalList = getInternalListMock();

	assert.strictEqual( widget.index, null );
	widget.buildIndex();
	assert.deepEqual( widget.index, [] );

	widget.onInternalListUpdate( [ 'mwReference/' ] );
	assert.strictEqual( widget.index, null );

	widget.buildIndex();
	assert.deepEqual( widget.index, [] );

	widget.onListNodeUpdate();
	assert.strictEqual( widget.index, null );
} );

QUnit.test( 'buildSearchIndex when empty', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	widget.internalList = getInternalListMock();

	const index = widget.buildSearchIndex();
	assert.deepEqual( index, [] );
} );

QUnit.test( 'buildSearchIndex', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	widget.internalList = getInternalListMock( true );

	const index = widget.buildSearchIndex();
	assert.deepEqual( index.length, 1 );
	assert.deepEqual( index[ 0 ].citation, '1' );
	assert.deepEqual( index[ 0 ].name, 'foo' );
	assert.deepEqual( index[ 0 ].text, '1 foo' );
} );

QUnit.test( 'isIndexEmpty', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	assert.true( widget.isIndexEmpty() );

	widget.internalList = {
		getNodeGroups: () => ( { 'mwReference/': { indexOrder: [ 0 ] } } )
	};
	assert.false( widget.isIndexEmpty() );
} );

QUnit.test( 'buildSearchResults', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	widget.index = [ { text: 'a', reference: 'model-a' }, { text: 'b' } ];

	assert.strictEqual( widget.getResults().getItemCount(), 0 );
	const results = widget.buildSearchResults( 'A' );
	assert.strictEqual( results.length, 1 );
	assert.strictEqual( results[ 0 ].getData(), 'model-a' );
} );
