'use strict';

QUnit.module( 've.ui.MWReferencesListDialog (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'isModified', ( assert ) => {
	const dialog = new ve.ui.MWReferencesListDialog();
	assert.true( dialog.isModified() );
} );
