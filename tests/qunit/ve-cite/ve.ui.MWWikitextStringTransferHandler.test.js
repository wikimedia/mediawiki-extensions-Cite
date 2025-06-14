'use strict';

/*!
 * VisualEditor UserInterface MWWikitextStringTransferHandler tests.
 *
 * @copyright 2011-2018 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

QUnit.module( 've.ui.MWWikitextStringTransferHandler (Cite)', ve.test.utils.newMwEnvironment( {
	beforeEach: function () {
		// Mock XHR for mw.Api()
		this.server = this.sandbox.useFakeServer();
	}
} ) );

/* Tests */

QUnit.test( 'convert', function ( assert ) {
	const cases = [
		{
			msg: 'Simple reference',
			pasteString: '<ref>Foo</ref>',
			pasteType: 'text/plain',
			parsoidResponse: ve.dm.example.singleLine`
				<p>
					<span about="#mwt2" class="mw-ref reference" id="cite_ref-1"
					 rel="dc:references" typeof="mw:Extension/ref" data-mw=\'{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{}}\'>[1]</span>
				</p>
				<ol class="mw-references" typeof="mw:Extension/references" about="#mwt3" data-mw='{"name":"references","attrs":{},"autoGenerated":true}'>
					<li about="#cite_note-1" id="cite_note-1">↑ <span id="mw-reference-text-cite_note-1" class="mw-reference-text">Foo</span></li>
				</ol>
			`,
			expectedData: [
				{ type: 'paragraph' },
				{
					type: 'mwReference',
					attributes: {
						listGroup: 'mwReference/',
						listIndex: 0,
						listKey: 'auto/0',
						refGroup: '',
						refListItemId: 'mw-reference-text-cite_note-1'
					}
				},
				{ type: '/mwReference' },
				{ type: '/paragraph' },
				{ type: 'internalList' },
				{ type: 'internalItem' },
				{ type: 'paragraph', internal: { generated: 'wrapper' } },
				'F', 'o', 'o',
				{ type: '/paragraph' },
				{ type: '/internalItem' },
				{ type: '/internalList' }
			]
		},
		{
			msg: 'Reference template with autoGenerated content',
			pasteString: '{{reference}}',
			pasteType: 'text/plain',
			parsoidResponse: ve.dm.example.singleLine`
				<p><span typeof="mw:Transclusion">[1]</span></p>
				<ol class="mw-references" typeof="mw:Extension/references" about="#mwt3" data-mw='{"name":"references","attrs":{},"autoGenerated":true}'>
					<li>Reference list</li>
				</ol>
			`,
			expectedData: [
				{ type: 'paragraph' },
				{
					type: 'mwTransclusionInline',
					attributes: {
						mw: {}
					}
				},
				{
					type: '/mwTransclusionInline'
				},
				{ type: '/paragraph' },
				{ type: 'internalList' },
				{ type: '/internalList' }
			]
		}
	];

	const server = this.server;
	cases.forEach( ( caseItem ) => {
		ve.test.utils.runWikitextStringHandlerTest(
			assert, server, caseItem.pasteString, caseItem.pasteType,
			caseItem.parsoidResponse, caseItem.expectedData, caseItem.annotations,
			caseItem.assertDom, caseItem.msg
		);
	} );
} );
