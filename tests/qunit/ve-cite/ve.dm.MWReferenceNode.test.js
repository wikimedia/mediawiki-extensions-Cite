'use strict';

QUnit.module( 've.dm.MWReferenceNode (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'makeListKey', ( assert ) => {
	const internalList = { getNextUniqueNumber: () => 7 };
	assert.strictEqual( ve.dm.MWReferenceNode.static.makeListKey( internalList, 'a' ), 'literal/a' );
	assert.strictEqual( ve.dm.MWReferenceNode.static.makeListKey( internalList ), 'auto/7' );
} );

QUnit.test( 'isBodyContentSet', ( assert ) => {
	const dataElement = { attributes: { contentsUsed: true, listGroup: 'same' } };
	const element = { attributes: { contentsUsed: false } };
	const nodesWithSameKey = [ new ve.dm.Model( element ) ];
	assert.false( ve.dm.MWReferenceNode.static.isBodyContentSet( dataElement, nodesWithSameKey ) );

	// One of the other ref with the same name already holds the content
	element.attributes.contentsUsed = true;
	assert.true( ve.dm.MWReferenceNode.static.isBodyContentSet( dataElement, nodesWithSameKey ) );

	// The other ref is actually the same as the current one
	element.attributes.listGroup = 'same';
	assert.false( ve.dm.MWReferenceNode.static.isBodyContentSet( dataElement, nodesWithSameKey ) );

	element.attributes.listGroup = 'different';
	assert.true( ve.dm.MWReferenceNode.static.isBodyContentSet( dataElement, nodesWithSameKey ) );

	// Nothing matters when the current ref doesn't hold content
	dataElement.attributes.contentsUsed = false;
	assert.false( ve.dm.MWReferenceNode.static.isBodyContentSet( dataElement, nodesWithSameKey ) );
} );

QUnit.test( 'shouldGetBodyContent on a normal main reference', ( assert ) => {
	const dataElement = { attributes: { listGroup: 'same' } };
	const ownRef = { attributes: { listGroup: 'same' } };

	// There is no other ref, only this one
	const nodesWithSameKey = [ new ve.dm.Model( ownRef ) ];
	assert.true( ve.dm.MWReferenceNode.static.shouldGetBodyContent( dataElement, nodesWithSameKey ) );

	// Another ref was holding the content before
	const otherRef = { attributes: { contentsUsed: true } };
	nodesWithSameKey.push( new ve.dm.Model( otherRef ) );
	assert.false( ve.dm.MWReferenceNode.static.shouldGetBodyContent( dataElement, nodesWithSameKey ) );

	// No other ref was holding the content before
	otherRef.attributes.contentsUsed = false;
	assert.true( ve.dm.MWReferenceNode.static.shouldGetBodyContent( dataElement, nodesWithSameKey ) );

	// The current ref is not the same as the first in the list
	ownRef.attributes.listGroup = 'different';
	assert.false( ve.dm.MWReferenceNode.static.shouldGetBodyContent( dataElement, nodesWithSameKey ) );

	// This ref was holding the content before
	dataElement.attributes.contentsUsed = true;
	assert.true( ve.dm.MWReferenceNode.static.shouldGetBodyContent( dataElement, nodesWithSameKey ) );
} );

QUnit.test( 'shouldGetBodyContent on a sub-reference', ( assert ) => {
	const dataElement = { attributes: { mainRefKey: 'x' } };
	assert.true( ve.dm.MWReferenceNode.static.shouldGetBodyContent( dataElement, [] ) );
} );

QUnit.test( 'generateName on a normal main reference', ( assert ) => {
	const attributes = {};
	const internalList = {
		getUniqueListKey: () => 'literal/:7',
		getNodeGroup: () => ( { firstNodes: [] } )
	};
	const nodesWithSameKey = [ 'dummy1' ];
	assert.strictEqual( ve.dm.MWReferenceNode.static.generateName( attributes, internalList, nodesWithSameKey ), undefined );

	nodesWithSameKey.push( 'dummy2' );
	assert.strictEqual( ve.dm.MWReferenceNode.static.generateName( attributes, internalList, nodesWithSameKey ), ':7' );

	attributes.listKey = 'literal/foo';
	assert.strictEqual( ve.dm.MWReferenceNode.static.generateName( attributes, internalList, nodesWithSameKey ), 'foo' );
} );

QUnit.test( 'generateName on a sub-reference', ( assert ) => {
	const attributes = { mainRefKey: 'x' };
	const internalList = { getUniqueListKey: () => 'literal/:7' };
	assert.strictEqual( ve.dm.MWReferenceNode.static.generateName( attributes, internalList, [] ), ':7' );

	attributes.mainRefKey = 'literal/foo';
	assert.strictEqual( ve.dm.MWReferenceNode.static.generateName( attributes, internalList, [] ), 'foo' );
} );

QUnit.test( 'hasSubRefs', ( assert ) => {
	const attributes = { listKey: 'a' };
	const firstNodes = [];
	const internalList = { getNodeGroup: () => ( { firstNodes } ) };
	assert.false( ve.dm.MWReferenceNode.static.hasSubRefs( attributes, internalList ) );

	firstNodes.push( new ve.dm.Model( { attributes: { mainRefKey: 'a' } } ) );
	assert.true( ve.dm.MWReferenceNode.static.hasSubRefs( attributes, internalList ) );

	// But when it's a sub-ref it cannot have sub-refs
	attributes.mainRefKey = 'x';
	assert.false( ve.dm.MWReferenceNode.static.hasSubRefs( attributes, internalList ) );
} );

QUnit.test( 'remapInternalListIndexes', ( assert ) => {
	const dataElement = { attributes: { listIndex: 'old', listKey: 'auto/' } };
	const mapping = { old: 'new' };
	const internalList = { getNextUniqueNumber: () => 7 };
	ve.dm.MWReferenceNode.static.remapInternalListIndexes( dataElement, mapping, internalList );
	assert.deepEqual( dataElement.attributes, { listIndex: 'new', listKey: 'auto/7' } );
} );

QUnit.test( 'remapInternalListKeys', ( assert ) => {
	const dataElement = { attributes: { listKey: 'k' } };
	const internalList = { keys: [ 'k' ] };
	ve.dm.MWReferenceNode.static.remapInternalListKeys( dataElement, internalList );
	assert.strictEqual( dataElement.attributes.listKey, 'k2' );
} );

QUnit.test( 'getGroup', ( assert ) => {
	const dataElement = { attributes: { refGroup: 'g' } };
	assert.deepEqual( ve.dm.MWReferenceNode.static.getGroup( dataElement ), 'g' );
} );

QUnit.test( 'cloneElement', ( assert ) => {
	const element = {
		attributes: { contentsUsed: true, mw: {}, originalMw: {} }
	};
	const store = { value: () => false };
	const clone = ve.dm.MWReferenceNode.static.cloneElement( element, store );
	assert.deepEqual( clone.attributes, {} );
	assert.true( isFinite( clone.originalDomElementsHash ) );
} );

QUnit.test( 'getHashObject', ( assert ) => {
	const dataElement = { type: 'T', attributes: { listGroup: 'L' } };
	assert.deepEqual( ve.dm.MWReferenceNode.static.getHashObject( dataElement ), dataElement );
	// FIXME: Shouldn't this behave different?
	assert.deepEqual( ve.dm.MWReferenceNode.static.getInstanceHashObject( dataElement ),
		dataElement );
} );

QUnit.test( 'describeChange', ( assert ) => {
	for ( const [ key, change, expected ] of [
		[ 'refGroup', { to: 'b' }, 'cite-ve-changedesc-ref-group-to,<ins>b</ins>' ],
		[ 'refGroup', { from: 'a' }, 'cite-ve-changedesc-ref-group-from,<del>a</del>' ],
		[ 'refGroup', { from: 'a', to: 'b' }, 'cite-ve-changedesc-ref-group-both,<del>a</del>,<ins>b</ins>' ],
		[ '', {}, undefined ]
	] ) {
		let msg = ve.dm.MWReferenceNode.static.describeChange( key, change );
		if ( Array.isArray( msg ) ) {
			msg = $( '<span>' ).append( msg ).html();
		}
		assert.strictEqual( msg, expected );
	}
} );
