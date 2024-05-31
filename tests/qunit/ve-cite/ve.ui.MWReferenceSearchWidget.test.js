'use strict';

QUnit.module( 've.ui.MWReferenceSearchWidget (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'buildIndex', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	widget.internalList = { getNodeGroups: () => ( {} ) };
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
	widget.internalList = { getNodeGroups: () => ( {} ) };

	const index = widget.buildSearchIndex();
	assert.deepEqual( index, [] );
} );

QUnit.test( 'buildSearchIndex with a placeholder', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();
	const placeholder = true;
	const node = { getAttribute: () => placeholder };
	const groups = { 'mwReference/': { indexOrder: [ 0 ], firstNodes: [ node ] } };
	widget.internalList = { getNodeGroups: () => groups, getItemNode: () => [] };

	const index = widget.buildSearchIndex();
	assert.deepEqual( index, [] );
} );

QUnit.test( 'buildSearchIndex', ( assert ) => {
	const widget = new ve.ui.MWReferenceSearchWidget();

	// XXX: This is a regression test with a fragile setup. Please feel free to delete this test
	// when you feel like it doesn't make sense to update it.
	const placeholder = false;
	const node = {
		getDocument: () => ( {
			getInternalList: () => null
		} ),
		getAttributes: () => ( { listKey: 'literal/foo' } ),
		getAttribute: () => placeholder
	};
	const groups = { 'mwReference/': { indexOrder: [ 0 ], firstNodes: [ node ] } };
	widget.internalList = { getNodeGroups: () => groups, getItemNode: () => [] };

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
