'use strict';

QUnit.module( 've.dm.MWDocumentReferences (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'first simple test', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const docRefs = ve.dm.MWDocumentReferences.static.refsForDoc( doc );

	const group = docRefs.getGroupRefs( 'mwReference/' );
	assert.strictEqual( group.getIndexLabel( 'auto/0' ), '1' );
	assert.strictEqual( group.getIndexLabel( 'literal/bar' ), '2' );
	assert.strictEqual( group.getIndexLabel( 'literal/:3' ), '3' );
	assert.strictEqual( group.getIndexLabel( 'auto/1' ), '4' );
	assert.strictEqual( docRefs.getGroupRefs( 'mwReference/foo' ).getIndexLabel( 'auto/2' ), '1' );

	assert.strictEqual( group.getIndexLabel( 'doesNotExist' ), '…' );
} );

QUnit.test( 'sub-references', ( assert ) => {
	const doc = ve.dm.citeExample.createExampleDocument( 'subReferencing' );
	const docRefs = ve.dm.MWDocumentReferences.static.refsForDoc( doc );

	const group = docRefs.getGroupRefs( 'mwReference/' );
	assert.strictEqual( group.getIndexLabel( 'auto/0' ), '1.1' );
	assert.strictEqual( group.getIndexLabel( 'auto/1' ), '2' );
	assert.strictEqual( group.getIndexLabel( 'literal/orphaned' ), '3.1' );
	assert.strictEqual( group.getIndexLabel( 'literal/ldr' ), '1' );
} );
