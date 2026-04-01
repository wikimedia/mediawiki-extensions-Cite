'use strict';

QUnit.module( 've.ui.MWCitationDialogTool (Cite)', ve.test.utils.newMwEnvironment() );

QUnit.test( 'isCompatibleWith', ( assert ) => {
	const model = new ve.dm.MWReferenceNode();
	assert.false(
		ve.ui.MWCitationDialogTool.static.isCompatibleWith( model ),
		'CitationDialogTools are not compatible with plain ReferenceNodes'
	);
} );
