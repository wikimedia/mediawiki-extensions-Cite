'use strict';

QUnit.module( 've.ui.MWReferenceDialog (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'setReferenceForEditing', ( assert ) => {
	const dialog = new ve.ui.MWReferenceDialog();

	dialog.editPanel = {};
	dialog.editPanel.referenceGroupInput = new ve.ui.MWReferenceGroupInputWidget( {} );
	dialog.editPanel.reuseWarning = new OO.ui.MessageWidget();
	dialog.editPanel.extendsWarning = new OO.ui.MessageWidget();

	// XXX: This is a regression test with a fragile setup. Please feel free to delete this test
	// when you feel like it doesn't make sense to update it.
	dialog.editPanel.referenceTarget = {
		setDocument: () => null
	};
	dialog.fragment = {
		getDocument: () => ( {
			getInternalList: () => ( {
				getNodeGroup: () => null
			} )
		} )
	};

	const parentDoc = {
		cloneWithData: () => null
	};
	const ref = new ve.dm.MWReferenceModel( parentDoc );
	ref.setGroup( 'g' );
	dialog.setReferenceForEditing( ref );

	assert.strictEqual( dialog.referenceModel, ref );
	assert.strictEqual( dialog.originalGroup, 'g' );
	assert.strictEqual( dialog.editPanel.referenceGroupInput.getValue(), 'g' );
	assert.false( dialog.editPanel.referenceGroupInput.isDisabled() );
	assert.false( dialog.editPanel.reuseWarning.isVisible() );
} );
