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
			':1',
			'Should return :1 pattern name when not using the new autoname patterns'
		);

		sinon.stub( MWReferenceKeyGenerator, 'getReferenceAutonamePrefix' ).returns( 'cite-ve-dialogbutton-reference-title-' );
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
			'cite-ve-dialogbutton-reference-title-1',
			'Should return reference title when using the new autoname patterns'
		);
		sinon.restore();

		attributes.listKey = 'literal/foo';
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
			'foo',
			'Should return literal title when set'
		);
	} );

	QUnit.test( 'generateName uses colon fallback when autoname is normalized to empty', ( assert ) => {
		const internalListMock = {
			getNodeGroup: () => new ve.dm.InternalListNodeGroup(),
			getItemNode: () => new ve.dm.InternalItemNode()
		};

		sinon.stub( MWReferenceKeyGenerator, 'getReferenceAutonamePrefix' ).returns( '   ///<>  ' );

		const attributes = {};
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
			':1',
			// Edge case, can only happen when override translation consists of spaces
			// and/or special chars
			'Should return last resort fallback when normalizedName for overwritten autoname returns empty string '
		);

		sinon.restore();
	} );

	QUnit.test( 'generateName when using autonames with citation tools', ( assert ) => {
		const internalListMock = {
			getNodeGroup: () => new ve.dm.InternalListNodeGroup(),
			getItemNode: () => new ve.dm.InternalItemNode()
		};
		const fixtures = [
			{
				toolDefinition: undefined,
				expected: 'cite-ve-dialogbutton-reference-title-1',
				msg: 'Should fallback if there\'s no fitting citation tool found'
			},
			{
				toolDefinition: { title: 'MockTitle-', autoname: 'MockAuto-' },
				expected: 'MockAuto-1',
				msg: 'Should use tool autoname'
			}
		];

		// mock the default message for cases where code doesn't use translusion
		sinon.stub( MWReferenceKeyGenerator, 'getReferenceAutonamePrefix' ).returns( 'cite-ve-dialogbutton-reference-title-' );
		// mock the transclusion detection
		sinon.stub( ve.ui.MWCitationDialog.static, 'getTransclusionNodeWithTemplate' ).returns( true );
		const attributes = {};
		fixtures.forEach( ( fixture ) => {
			sinon.stub( ve.ui.MWCitationDialog.static, 'getToolDefinitionFromInternalItem' )
				.returns( fixture.toolDefinition );

			assert.strictEqual(
				MWReferenceKeyGenerator.generateName( attributes, internalListMock, true, true ),
				fixture.expected,
				fixture.msg
			);
			sinon.restore();
		} );
	} );

	QUnit.test( 'generateName on a sub-reference', ( assert ) => {
		const internalListMock = {
			getNodeGroup: () => new ve.dm.InternalListNodeGroup(),
			getItemNode: () => new ve.dm.InternalItemNode()
		};

		const attributes = { mainListIndex: 0 };

		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock ),
			':1',
			'Should return :0 pattern name when not using the new autoname patterns'
		);

		sinon.stub( MWReferenceKeyGenerator, 'getReferenceAutonamePrefix' ).returns( 'cite-ve-dialogbutton-reference-title-' );
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, false, true ),
			'cite-ve-dialogbutton-reference-title-1',
			'Should return reference title when using the new autoname patterns'
		);
		sinon.restore();

		attributes.listKey = 'literal/foo';
		assert.strictEqual(
			MWReferenceKeyGenerator.generateName( attributes, internalListMock, false, true ),
			'foo',
			'Should return literal title when set'
		);
	} );

	QUnit.test( 'normalize reference name', ( assert ) => {

		const cases = [
			{
				rawName: 'foo',
				expected: 'foo',
				message: 'Should not change valid name'
			},
			{
				rawName: 'f<o\\"o',
				expected: 'foo',
				message: 'Should remove disallowed characters'
			},

			{
				rawName: 'f     o  o',
				expected: 'f o o',
				message: 'Should change multiple white spaces to one space'
			},
			{
				rawName: '    foo    ',
				expected: 'foo',
				message: 'Should remove leading and trailing whitespaces'
			},
			{
				rawName: '',
				expected: '',
				message: 'Should return empty string when string is empty'
			},
			{
				rawName: null,
				expected: '',
				message: 'Should accept null'
			},
			{
				rawName: undefined,
				expected: '',
				message: 'Should accept undefined'
			} ];

		cases.forEach( ( c ) => {
			assert.strictEqual(
				MWReferenceKeyGenerator.normalizeName( c.rawName ),
				c.expected,
				c.message
			);
		} );
	} );

	QUnit.test( 'getCitationAutonameTemplate', ( assert ) => {
		const mapWithDefault = {
			'Cite web': 'web-autoname',
			'*': 'universal-autoname'
		};

		assert.strictEqual(
			MWReferenceKeyGenerator.getCitationAutonameTemplate( 'Cite web', mapWithDefault ),
			'web-autoname',
			'Should return mapped autoname template'
		);
		assert.strictEqual(
			MWReferenceKeyGenerator.getCitationAutonameTemplate( 'Cite book', mapWithDefault ),
			'universal-autoname',
			'Should return default autoname template'
		);
		assert.strictEqual(
			MWReferenceKeyGenerator.getCitationAutonameTemplate( 'Cite book', {} ),
			undefined,
			'Should return undefined when there is no default'
		);
	} );
}
