'use strict';

/*!
 * VisualEditor Cite specific test cases for the Converter.  The fromDataBody in these tests
 * should match Parsoid html2wt tests in visualEditorHtml2WtTests.txt
 *
 * @copyright 2011-2025 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

ve.dm.ConverterIntegrationTestCases = {};

ve.dm.ConverterIntegrationTestCases.cases = {
	'mw:Reference: Simple reference re-use (T296044)': {
		// Wikitext:
		// Foo<ref name="bar">[[Bar]]</ref> Baz<ref name="bar" />
		body: ve.dm.example.singleLine`
			<p>
				Foo
				<sup about="#mwt1" class="mw-ref reference" data-mw='{"name":"ref","body":{"html":"
				<a rel=\\"mw:WikiLink\\" href=\\"./Bar\\">Bar
				</a>"},"attrs":{"name":"bar"}}' id="cite_ref-bar-1-1" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-bar-1"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></a>
				</sup>
				 Baz
				<sup about="#mwt2" class="mw-ref reference" data-mw='{"name":"ref","attrs":{"name":"bar"}}' id="cite_ref-bar-1-3" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-bar-1"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></a>
				</sup>
			</p>
		`,
		fromDataBody: ve.dm.example.singleLine`
			<p>
				Foo
				<sup data-mw='{"name":"ref","body":{"html":"
				<a rel=\\"mw:WikiLink\\" href=\\"./Bar\\">Bar
				</a>"},"attrs":{"name":"bar"}}' typeof="mw:Extension/ref">
				</sup>
				 Baz
				<sup data-mw='{"name":"ref","attrs":{"name":"bar"}}' typeof="mw:Extension/ref">
				</sup>
			</p>
		`,
		clipboardBody: ve.dm.example.singleLine`
			<p>
				Foo
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"
				<a href=\\"./Bar\\" rel=\\"mw:WikiLink\\">Bar
				</a>"},"attrs":{"name":"bar"}}' class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
				 Baz
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"name":"bar"}}' class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
			</p>
		`,
		data: [
			{ type: 'paragraph' },
			'F', 'o', 'o',
			{
				type: 'mwReference',
				attributes: {
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/bar',
					refGroup: '',
					mw: { name: 'ref', body: { html: '<a rel="mw:WikiLink" href="./Bar">Bar</a>' }, attrs: { name: 'bar' } },
					originalMw: '{"name":"ref","body":{"html":"<a rel=\\"mw:WikiLink\\" href=\\"./Bar\\">Bar</a>"},"attrs":{"name":"bar"}}',
					contentsUsed: true
				}
			},
			{ type: '/mwReference' },
			' ', 'B', 'a', 'z',
			{
				type: 'mwReference',
				attributes: {
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/bar',
					refGroup: '',
					mw: { name: 'ref', attrs: { name: 'bar' } },
					originalMw: '{"name":"ref","attrs":{"name":"bar"}}',
					contentsUsed: false
				}
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
			{ type: 'internalList' },
			{ type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Bar">Bar</a>' } },
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			[
				'B',
				[ {
					type: 'link/mwInternal',
					attributes: {
						title: 'Bar',
						normalizedTitle: 'Bar',
						lookupTitle: 'Bar'
					}
				} ]
			],
			[
				'a',
				[ {
					type: 'link/mwInternal',
					attributes: {
						title: 'Bar',
						normalizedTitle: 'Bar',
						lookupTitle: 'Bar'
					}
				} ]
			],
			[
				'r',
				[ {
					type: 'link/mwInternal',
					attributes: {
						title: 'Bar',
						normalizedTitle: 'Bar',
						lookupTitle: 'Bar'
					}
				} ]
			],
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: '/internalList' }
		]
	},
	'mw:Reference with comment': {
		body: ve.dm.example.singleLine`
			<p>
				<sup about="#mwt2" class="mw-ref reference"
				 data-mw='{"name":"ref","body":
				{"html":"Foo<!-- bar -->"},"attrs":{}}'
				 id="cite_ref-1-0" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-bar-1"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></a>
				</sup>
			</p>
		`,
		fromDataBody: ve.dm.example.singleLine`
			<p>
				<sup
				 data-mw='{"name":"ref","body":
				{"html":"Foo<!-- bar -->"},"attrs":{}}'
				 typeof="mw:Extension/ref"></sup>
			</p>
		`,
		clipboardBody: ve.dm.example.singleLine`
			<p>
				<sup typeof="mw:Extension/ref"
				 data-mw='{"attrs":{},"body":
			{"html":"Foo<span rel=\\"ve:Comment\\" data-ve-comment=\\" bar \\">&amp;nbsp;</span>"},"name":"ref"}'
			 class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
			</p>
		`,
		previewBody: ve.dm.example.singleLine`
			<p>
				<sup typeof="mw:Extension/ref"
				 data-mw='{"attrs":{},"body":
				{"html":"Foo<!-- bar -->"},"name":"ref"}'
				 class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
			</p>
		`,
		data: [
			{ type: 'paragraph' },
			{
				type: 'mwReference',
				attributes: {
					contentsUsed: true,
					listGroup: 'mwReference/',
					listIndex: 0,
					listKey: 'auto/0',
					mw: {
						attrs: {},
						body: {
							html: 'Foo<!-- bar -->'
						},
						name: 'ref'
					},
					originalMw: '{"name":"ref","body":{"html":"Foo<!-- bar -->"},"attrs":{}}',
					refGroup: ''
				}
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
			{ type: 'internalList' },
			{ type: 'internalItem', attributes: { originalHtml: 'Foo<!-- bar -->' } },
			{
				internal: {
					generated: 'wrapper'
				},
				type: 'paragraph'
			},
			'F', 'o', 'o',
			{
				type: 'comment',
				attributes: {
					text: ' bar '
				}
			},
			{ type: '/comment' },
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: '/internalList' }
		]
	},
	'Delete reused ref': {
		// Given the following wikitext:
		// <ref name="bar">Body</ref>
		// Text
		// <ref name="bar" />
		//
		// When it's edited in VE by deleting the first ref which contains the
		// footnote body, then the result should be:
		//
		// Text
		// <ref name="bar">Body</ref>
		//
		data: [
			{
				type: 'paragraph'
			},
			{
				attributes: {
					contentsUsed: true,
					listGroup: 'mwReference/',
					listIndex: 0,
					listKey: 'literal/bar',
					mw: {
						attrs: { name: 'bar' },
						body: { id: 'mw-reference-text-cite_note-bar-1' },
						name: 'ref'
					},
					originalMw: '{"name":"ref","attrs":{"name":"bar"},"body":{"id":"mw-reference-text-cite_note-bar-1"}}',
					refGroup: '',
					refListItemId: 'mw-reference-text-cite_note-bar-1'
				},
				type: 'mwReference'
			},
			{
				type: '/mwReference'
			},
			'T',
			'e',
			'x',
			't',
			{
				attributes: {
					contentsUsed: false,
					listGroup: 'mwReference/',
					listIndex: 0,
					listKey: 'literal/bar',
					mw: {
						attrs: { name: 'bar' },
						name: 'ref'
					},
					originalMw: '{"name":"ref","attrs":{"name":"bar"}}',
					refGroup: ''
				},
				type: 'mwReference'
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				attributes: {
					isResponsive: true,
					listGroup: 'mwReference/',
					mw: {
						attrs: {},
						autoGenerated: true,
						name: 'references'
					},
					originalMw: '{"name":"references","attrs":{},"autoGenerated":true}',
					refGroup: '',
					templateGenerated: false
				},
				type: 'mwReferencesList'
			},
			{
				type: '/mwReferencesList'
			},
			{
				type: 'internalList'
			},
			{
				attributes: {
					originalHtml: 'Body'
				},
				type: 'internalItem'
			},
			{
				internal: {
					generated: 'wrapper'
				},
				type: 'paragraph'
			},
			'B',
			'o',
			'd',
			'y',
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: '/internalList'
			}
		],
		modify: ( doc ) => {
			doc.commit( ve.dm.TransactionBuilder.static.newFromRemoval(
				doc,
				new ve.Range( 1, 3 )
			) );
		},
		clipboardBody: ve.dm.example.singleLine`
			<p>
				Text
				<sup typeof="mw:Extension/ref" data-mw='{"attrs":{"name":"bar"},"name":"ref","body":{"html":"Body"}}' class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>
			<div typeof="mw:Extension/references" data-mw='{"name":"references","attrs":{},"autoGenerated":true}'><ol class="mw-references references">
				<li style='--footnote-number: "1.";'><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Body</p></span></div></span></li>
			</ol></div>
		`,
		fromDataBody: ve.dm.example.singleLine`
			<p>
				Text
				<sup typeof="mw:Extension/ref" data-mw='{"attrs":{"name":"bar"},"name":"ref","body":{"html":"Body"}}' class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>
			</p>
			<div typeof="mw:Extension/references" data-mw='{"name":"references","attrs":{},"autoGenerated":true}'>
				<ol>
					<li><span typeof="mw:Extension/ref">Body</span></li>
				</ol>
			</div>
		`,
		normalizedBody: ve.dm.example.singleLine`
			<p>
				Text
				<sup about="#mwt2" class="mw-ref reference" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Body&quot;}}"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>
			</p>
			<div typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references">
				<li about="#cite_note-bar-1" id="cite_note-bar-1"><span rel="mw:referencedBy" class="mw-cite-backlink"><a href="#cite_ref-bar_1-0"><span class="cite-accessibility-label">Jump up to: </span><span class="mw-linkback-text">1 </span></a><a href="#cite_ref-bar_1-1"><span class="mw-linkback-text">2 </span></a></span> <span id="mw-reference-text-cite_note-bar-1" class="mw-reference-text reference-text">Body</span></li>
			</ol></div>
		`
	}
};
