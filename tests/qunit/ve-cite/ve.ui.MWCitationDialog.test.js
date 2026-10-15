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

	QUnit.test( 'getCitationTransclusionMatchingTemplate', ( assert ) => {
		const internalItem = createMockInternalItem( [
			createMockBranch( true, [ citeWebTransclusion ] )
		] );

		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionMatchingTemplate( internalItem, 'cite web' ),
			citeWebTransclusion,
			'Returns transclusion node when internal item has a matching single template'
		);

		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionMatchingTemplate( internalItem, 'cite news' ),
			undefined,
			'Returns undefined when requested template does not match'
		);
	} );

	QUnit.test( 'getCitationTransclusionFromInternalItem', ( assert ) => {
		let internalItem = createMockInternalItem( [
			createMockBranch( true, [ nonTransclusionNode ] )
		] );

		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when leaf node is not an MWTransclusionNode'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( true, [ citeWebTransclusion ] ),
			createMockBranch( true, [ citeWebTransclusion ] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when internal item has multiple branch children'
		);

		internalItem = createMockInternalItem( [] );
		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when internal item has zero branch children'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( false, [ citeWebTransclusion ] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when branch node cannot contain content'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( true, [ citeWebTransclusion, citeWebTransclusion ] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when branch node has multiple leaf children'
		);

		internalItem = createMockInternalItem( [
			createMockBranch( true, [] )
		] );
		assert.strictEqual(
			MWCitationDialog.static.getCitationTransclusionFromInternalItem( internalItem ),
			undefined,
			'Returns undefined when branch node has zero leaf children'
		);
	} );

	const templateTransclusionNode = new ve.dm.MWTransclusionNode(
		{ type: 'mwTransclusion', attributes: { mw: { parts: [ {
			template: {
				target: {
					wt: 'Infobox',
					href: './Template:Infobox'
				},
				params: {}
			}
		} ] } } }
	);
	const transclusionNode = new ve.dm.MWTransclusionNode(
		{ type: 'mwTransclusion', attributes: { mw: { parts: [] } } }
	);

	QUnit.test.each( 'findFirstTemplateTransclusionNode', {
		'no transclusion node': {
			node: new ve.dm.ParagraphNode(),
			expected: undefined
		},
		'no transclusion node in child': {
			node: new ve.dm.ParagraphNode( {}, [ new ve.dm.TextNode() ] ),
			expected: undefined
		},
		'transclusion node': {
			node: transclusionNode,
			expected: undefined
		},
		'template transclusion node': {
			node: templateTransclusionNode,
			expected: templateTransclusionNode
		},
		'template transclusion node in child': {
			node: new ve.dm.ParagraphNode( {}, [ templateTransclusionNode ] ),
			expected: templateTransclusionNode
		},
		'transclusion node in child sibling': {
			node: new ve.dm.ParagraphNode( {}, [ new ve.dm.TextNode(), templateTransclusionNode ] ),
			expected: templateTransclusionNode
		},
		'transclusion node in 2nd level child sibling': {
			node: new ve.dm.ParagraphNode( {}, [
				new ve.dm.TextNode(),
				new ve.dm.ParagraphNode( {}, [ new ve.dm.TextNode(), templateTransclusionNode ] )
			] ),
			expected: templateTransclusionNode
		},
		'multiple transclusion nodes, return first match': {
			node: new ve.dm.ParagraphNode( {}, [
				new ve.dm.TextNode(),
				transclusionNode,
				templateTransclusionNode
			] ),
			expected: templateTransclusionNode
		}
	}, ( assert, caseItem ) => {
		assert.strictEqual(
			ve.ui.MWCitationDialog.static.findFirstTemplateTransclusionNode( caseItem.node ),
			caseItem.expected
		);
	} );
}
