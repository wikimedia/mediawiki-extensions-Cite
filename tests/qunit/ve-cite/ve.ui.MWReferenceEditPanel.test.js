'use strict';

QUnit.module( 've.ui.MWReferenceEditPanel (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'setReferenceForEditing', ( assert ) => {
	ve.init.target.surface = { commandRegistry: { registry: {} } };
	const editPanel = new ve.ui.MWReferenceEditPanel();

	const emptyDoc = new ve.dm.Document( [
		{ type: 'paragraph', internal: { generated: 'empty' } },
		{ type: '/paragraph' },
		{ type: 'internalList' },
		{ type: '/internalList' }
	] );
	const ref = new ve.dm.MWReferenceModel( emptyDoc );

	editPanel.setInternalList( emptyDoc.getInternalList() );
	ref.setGroup( 'g' );
	editPanel.setReferenceForEditing( ref );

	assert.strictEqual( editPanel.originalGroup, 'g' );
	assert.strictEqual( editPanel.referenceGroupInput.getValue(), 'g' );
	assert.false( editPanel.referenceGroupInput.isDisabled() );
	assert.false( editPanel.reuseWarning.isVisible() );
	assert.false( editPanel.extendsWarning.isVisible() );
	assert.strictEqual( editPanel.getReferenceFromEditing().getGroup(), 'g' );
} );

QUnit.test( 'sub refs', ( assert ) => {
	ve.init.target.surface = { commandRegistry: { registry: {} } };
	const editPanel = new ve.ui.MWReferenceEditPanel();

	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const ref = new ve.dm.MWReferenceModel( doc );

	editPanel.setInternalList( doc.getInternalList() );
	// does exist in the example document
	ref.extendsRef = 'literal/bar';
	editPanel.setReferenceForEditing( ref );

	assert.false( editPanel.reuseWarning.isVisible() );
	assert.true( editPanel.extendsWarning.isVisible() );
	assert.true( !!editPanel.extendsWarning.getLabel().text().indexOf( 'Bar' ) );

	// test sub ref with missing main ref
	ref.extendsRef = 'literal/notexist';
	editPanel.setReferenceForEditing( ref );

	assert.false( editPanel.reuseWarning.isVisible() );
	assert.false( editPanel.extendsWarning.isVisible() );
} );
