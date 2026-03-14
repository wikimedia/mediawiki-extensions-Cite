'use strict';

QUnit.module( 've.dm.MWReferenceKeyGenerator (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'makeListKey', ( assert ) => {
	const internalList = { getNextUniqueNumber: () => 7 };
	assert.strictEqual( ve.dm.MWReferenceKeyGenerator.makeListKey( internalList, 'a' ), 'literal/a' );
	assert.strictEqual( ve.dm.MWReferenceKeyGenerator.makeListKey( internalList ), 'auto/7' );
} );

QUnit.test( 'generateName on a normal main reference', ( assert ) => {
	const attributes = {};
	const internalList = {
		getNodeGroup: () => new ve.dm.InternalListNodeGroup()
	};
	assert.strictEqual(
		ve.dm.MWReferenceKeyGenerator.generateName( attributes, internalList ),
		undefined
	);

	assert.strictEqual( ve.dm.MWReferenceKeyGenerator.generateName( attributes, internalList, true ), ':0' );

	attributes.listKey = 'literal/foo';
	assert.strictEqual( ve.dm.MWReferenceKeyGenerator.generateName( attributes, internalList, true ), 'foo' );
} );

QUnit.test( 'generateName on a sub-reference', ( assert ) => {
	const attributes = { mainListIndex: 0 };
	const internalList = {
		getNodeGroup: () => new ve.dm.InternalListNodeGroup()
	};
	assert.strictEqual( ve.dm.MWReferenceKeyGenerator.generateName( attributes, internalList ), ':0' );

	attributes.mainRefKey = 'literal/foo';
	assert.strictEqual( ve.dm.MWReferenceKeyGenerator.generateName( attributes, internalList ), 'foo' );
} );
