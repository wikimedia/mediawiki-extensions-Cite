'use strict';

{
	const { MWReferenceKeyGenerator } = require( 'ext.cite.visualEditor' ).test;

	QUnit.module( 've.dm.MWReferenceKeyGenerator (Cite)', ve.test.utils.newMwEnvironment() );

	QUnit.test( 'makeListKey', ( assert ) => {
		let i = 7;
		const internalList = {
			getNextUniqueNumber: () => i++
		};
		assert.strictEqual( MWReferenceKeyGenerator.makeListKey( internalList, 'a' ), 'literal/a' );
		assert.strictEqual( MWReferenceKeyGenerator.makeListKey( internalList ), 'auto/7' );
		assert.strictEqual( MWReferenceKeyGenerator.makeListKey( internalList, '' ), 'auto/8' );
	} );

	QUnit.test( 'deduplicateListKey', ( assert ) => {
		let i = 7;
		const internalList = {
			getNodeGroup: () => ( {
				getAllReuses: ( listKey ) => listKey === 'conflicts'
			} ),
			getNextUniqueNumber: () => i++
		};
		assert.strictEqual(
			MWReferenceKeyGenerator.deduplicateListKey( internalList, '', 'fine' ),
			'fine'
		);
		assert.strictEqual(
			MWReferenceKeyGenerator.deduplicateListKey( internalList, '', 'conflicts' ),
			'auto/7'
		);
	} );

	QUnit.test( 'generateName on a normal main reference', ( assert ) => {
		const internalListMock = {
			getNodeGroup: () => new ve.dm.InternalListNodeGroup(),
			getItemNode: () => new ve.dm.InternalItemNode()
		};

		const attributes = {};
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock ),
			undefined,
			'Should return undefined when there\'s no reuse'
		);

		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, true ),
			':0',
			'Should return :0 pattern name when not using the new autoname patterns'
		);

		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
			'cite-ve-dialogbutton-reference-title-0',
			'Should return reference title when using the new autoname patterns'
		);

		attributes.listKey = 'literal/foo';
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
			'foo',
			'Should return literal title when set'
		);
	} );

	QUnit.test( 'generateName when using autonames with citation tools', ( assert ) => {
		const internalListMock = {
			getNodeGroup: () => new ve.dm.InternalListNodeGroup(),
			getItemNode: () => new ve.dm.InternalItemNode()
		};
		const fixtures = [
			{
				mwCitationTools: undefined,
				expected: 'cite-ve-dialogbutton-reference-title-0',
				msg: 'Should fallback if there\'s no citation tool set'
			},
			{
				mwCitationTools: [],
				expected: 'cite-ve-dialogbutton-reference-title-0',
				msg: 'Should fallback if there\'s no citation tool set'
			},
			{
				mwCitationTools: [ { title: 'MockTitle-', template: '' } ],
				expected: 'MockTitle-0',
				msg: 'Should use citation tool title'
			},
			{
				mwCitationTools: [ { title: 'MockTitle-', autoname: 'MockAuto-', template: '' } ],
				expected: 'MockAuto-0',
				msg: 'Should prefer citation tool autoname'
			}
		];

		// mock the transclusion detection
		sinon.stub( ve.ui.MWCitationDialog.static, 'getTransclusionNodeWithTemplate' ).returns( true );
		const attributes = {};

		fixtures.forEach( ( fixture ) => {
			sinon.stub( ve.ui, 'mwCitationTools' ).value( fixture.mwCitationTools );

			assert.strictEqual(
				MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
				fixture.expected,
				fixture.msg
			);
		} );

		sinon.restore();
	} );

	QUnit.test( 'generateName on a sub-reference', ( assert ) => {
		const internalListMock = {
			getNodeGroup: () => new ve.dm.InternalListNodeGroup(),
			getItemNode: () => new ve.dm.InternalItemNode()
		};

		const attributes = { mainListIndex: 0 };

		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock ),
			':0',
			'Should return :0 pattern name when not using the new autoname patterns'
		);

		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, false, true ),
			'cite-ve-dialogbutton-reference-title-0',
			'Should return reference title when using the new autoname patterns'
		);

		attributes.listKey = 'literal/foo';
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, false, true ),
			'foo',
			'Should return literal title when set'
		);
	} );
}
