'use strict';

{
	const { MWCitationDialog } = require( 'ext.cite.visualEditor' ).test;

	QUnit.module( 've.ui.MWCitationDialog (Cite)', ve.test.utils.newMwEnvironment() );

	const createMockBranch = ( canContainContent, children ) => ( {
		canContainContent: () => canContainContent,
		getChildren: () => children
	} );

	const createMockInternalItem = ( branches ) => ( {
		getChildren: () => branches
	} );

	const citeWebTransclusion = Object.create( ve.dm.MWTransclusionNode.prototype );
	citeWebTransclusion.isSingleTemplate = ( template ) => {
		if ( Array.isArray( template ) ) {
			return template.includes( 'cite web' );
		}
		return template === 'cite web';
	};

	const nonTransclusionNode = new ve.dm.Node();

	QUnit.test( 'getTransclusionNodeWithTemplate', ( assert ) => {
		const internalItem = createMockInternalItem( [
			createMockBranch( true, [ citeWebTransclusion ] )
		] );

		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeWithTemplate( internalItem, 'cite web' ),
			citeWebTransclusion,
			'Returns transclusion node when internal item has a matching single template'
		);

		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeWithTemplate( internalItem, 'cite news' ),
			undefined,
			'Returns undefined when requested template does not match'
		);
	} );

	QUnit.test( 'getTransclusionNodeFromInternalItem', ( assert ) => {
		let internalItem = createMockInternalItem( [
			createMockBranch( true, [ nonTransclusionNode ] )
		] );

		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when leaf node is not an MWTransclusionNode'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( true, [ citeWebTransclusion ] ),
			createMockBranch( true, [ citeWebTransclusion ] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when internal item has multiple branch children'
		);

		internalItem = createMockInternalItem( [] );
		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when internal item has zero branch children'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( false, [ citeWebTransclusion ] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when branch node cannot contain content'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( true, [ citeWebTransclusion, citeWebTransclusion ] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when branch node has multiple leaf children'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( true, [] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getTransclusionNodeFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when branch node has zero leaf children'
		);
	} );

}
