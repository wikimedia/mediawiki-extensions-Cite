'use strict';

QUnit.module( 've.dm.MWDataTransitionHelper (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'getInternalModelNode', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const dataTransitionHelper = new ve.dm.MWDataTransitionHelper(
		doc.getInternalList()
	);

	const itemFromIndex = dataTransitionHelper.getInternalItemNode(
		'invalid',
		'invalid',
		1
	);

	// we want to test the fallback approach
	const itemFromKey = dataTransitionHelper.getInternalItemNode(
		'literal/bar',
		'mwReference/'
	);

	assert.deepEqual(
		itemFromIndex,
		itemFromKey,
		'InternalItemNode can be found either way, using the listIndex or listKey.'
	);
} );
