'use strict';

QUnit.module( 've.ui.MWUseExistingReferenceCommand (Cite)', ve.test.utils.newMwEnvironment() );

function getFragementMock( hasRefs ) {
	const docRefsMock = {
		hasRefs: () => hasRefs
	};

	return {
		getDocument: () => ( {
			getOriginalDocument: () => undefined,
			getStorage: () => docRefsMock
		} ),
		getSelection: () => ( {
			getName: () => 'linear'
		} )
	};
}

QUnit.test( 'Constructor', ( assert ) => {
	const command = new ve.ui.MWUseExistingReferenceCommand();
	assert.strictEqual( command.name, 'reference/existing' );
	assert.strictEqual( command.action, 'window' );
	assert.strictEqual( command.method, 'open' );
} );

QUnit.test( 'isExecutable', ( assert ) => {
	const command = new ve.ui.MWUseExistingReferenceCommand();

	assert.false( command.isExecutable( getFragementMock( false ) ) );
	assert.true( command.isExecutable( getFragementMock( true ) ) );
} );
