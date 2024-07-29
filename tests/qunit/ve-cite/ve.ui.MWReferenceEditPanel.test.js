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
} );
