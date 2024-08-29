'use strict';

QUnit.module( 've.ui.MWReferenceEditPanel (Cite)', ve.test.utils.newMwEnvironment() );

function getDocRefsMock( doc, hasNode, reUse ) {
	const node = new ve.dm.MWReferenceNode( {
		type: 'mwReference',
		attributes: {
			listKey: 'literal/bar',
			refGroup: 'mwReference/'
		},
		originalDomElementsHash: Math.random()
	} );
	node.setDocument( doc );
	const groupRefs = {
		getRefUsages: () => ( reUse ? [ node, node ] : [] ),
		getInternalModelNode: () => ( hasNode ? node : undefined )
	};
	return {
		getAllGroupNames: () => ( [ 'mwReference/' ] ),
		getGroupRefs: () => ( groupRefs )
	};
}

QUnit.test( 'setReferenceForEditing', ( assert ) => {
	ve.init.target.surface = { commandRegistry: { registry: {} } };
	const editPanel = new ve.ui.MWReferenceEditPanel();

	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const ref = new ve.dm.MWReferenceModel( doc );
	editPanel.setDocumentReferences( getDocRefsMock( doc ) );

	const changeHandlerStub = sinon.stub();
	editPanel.connect( changeHandlerStub );

	ref.setGroup( 'group' );
	editPanel.setReferenceForEditing( ref );

	assert.strictEqual( editPanel.originalGroup, 'group' );
	assert.strictEqual( editPanel.referenceGroupInput.getValue(), 'group' );
	assert.false( editPanel.referenceGroupInput.isDisabled() );
	assert.false( editPanel.reuseWarning.isVisible() );
	assert.false( editPanel.extendsWarning.isVisible() );
	assert.strictEqual( editPanel.getReferenceFromEditing().getGroup(), 'group' );

	sinon.assert.notCalled( changeHandlerStub );
} );

QUnit.test( 're-used references', ( assert ) => {
	ve.init.target.surface = { commandRegistry: { registry: {} } };
	const editPanel = new ve.ui.MWReferenceEditPanel();

	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const ref = new ve.dm.MWReferenceModel( doc );

	editPanel.setDocumentReferences( getDocRefsMock( doc, true, true ) );
	editPanel.setReferenceForEditing( ref );

	assert.true( editPanel.reuseWarning.isVisible() );
	assert.false( editPanel.extendsWarning.isVisible() );
} );

QUnit.test( 'sub-references', ( assert ) => {
	ve.init.target.surface = { commandRegistry: { registry: {} } };
	const editPanel = new ve.ui.MWReferenceEditPanel();

	const doc = ve.dm.citeExample.createExampleDocument( 'references' );
	const ref = new ve.dm.MWReferenceModel( doc );

	// does exist in the example document
	ref.extendsRef = 'literal/bar';
	editPanel.setDocumentReferences( getDocRefsMock( doc, true ) );
	editPanel.setReferenceForEditing( ref );

	assert.false( editPanel.reuseWarning.isVisible() );
	assert.true( editPanel.extendsWarning.isVisible() );
	assert.false( editPanel.extendsWarning.getLabel().text().indexOf( 'cite-ve-dialog-reference-missing-parent-ref' ) !== -1 );
	// TODO improve node mock to check content insertion for the parent
	// assert.true( editPanel.extendsWarning.getLabel().text().indexOf( 'Bar' ) !== -1 );

	// test sub ref with missing main ref
	ref.extendsRef = 'literal/notexist';
	editPanel.setDocumentReferences( getDocRefsMock( doc ) );
	editPanel.setReferenceForEditing( ref );

	assert.false( editPanel.reuseWarning.isVisible() );
	assert.true( editPanel.extendsWarning.isVisible() );
	assert.true( editPanel.extendsWarning.getLabel().text().indexOf( 'cite-ve-dialog-reference-missing-parent-ref' ) !== -1 );
} );
