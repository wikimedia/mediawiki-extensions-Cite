'use strict';

QUnit.module( 've.dm.MWDocumentReferences (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'first simple test', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const docRefs = ve.dm.MWDocumentReferences.static.refsForDoc( doc );

	const mainNodes = docRefs.getGroupRefsByParents( 'mwReference/' );
	const fooNodes = docRefs.getGroupRefsByParents( 'mwReference/foo' );

	assert.strictEqual( mainNodes[ '' ].length, 4 );
	assert.strictEqual( fooNodes[ '' ].length, 1 );

	const firstListKey = mainNodes[ '' ][ 0 ].getAttribute( 'listKey' );
	const fooGroupListKey = fooNodes[ '' ][ 0 ].getAttribute( 'listKey' );

	assert.strictEqual( firstListKey, 'auto/0' );
	assert.strictEqual( fooGroupListKey, 'auto/2' );
	assert.strictEqual( docRefs.getIndexNumber( '', firstListKey ), '1' );
	assert.strictEqual( docRefs.getIndexNumber( 'foo', fooGroupListKey ), '1' );
} );

QUnit.test( 'extends test', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'extends' );
	const docRefs = ve.dm.MWDocumentReferences.static.refsForDoc( doc );

	const groupedRefs = docRefs.getGroupRefsByParents( 'mwReference/' );

	assert.strictEqual( Object.keys( groupedRefs ).length, 2 );

	assert.strictEqual( groupedRefs[ '' ].length, 2 );
	assert.strictEqual( groupedRefs[ 'literal/ldr' ].length, 1 );

	const firstListKey = groupedRefs[ '' ][ 0 ].getAttribute( 'listKey' );
	assert.strictEqual( firstListKey, 'auto/1' );
	// FIXME: Documents incorrect behavior, should be '2'.
	assert.strictEqual( docRefs.getIndexNumber( '', firstListKey ), '1' );

	const subrefListKey = groupedRefs[ 'literal/ldr' ][ 0 ].getAttribute( 'listKey' );
	assert.strictEqual( subrefListKey, 'auto/0' );
	// FIXME: Documents incorrect behavior, should be '1.1'.
	assert.strictEqual( docRefs.getIndexNumber( '', subrefListKey ), '2.1' );
} );
