'use strict';

/*!
 * VisualEditor Cite specific test cases for the Converter.  The normalizedBody in these tests
 * should match the HTML for Parsoid of the html2wt tests in visualEditorHtml2WtTests.txt
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
					mw: {
						name: 'ref',
						body: { html: '<a rel="mw:WikiLink" href="./Bar">Bar</a>' },
						attrs: { name: 'bar' }
					},
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
	}
};

// Wikitext:
// <ref name="bar">Body</ref>
// Text
// <ref name="bar" />
ve.dm.ConverterIntegrationTestCases.SimpleRefReuse = {
	data:
		[
			{
				type: 'paragraph',
				internal: {
					whitespace: [
						undefined,
						undefined,
						undefined,
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'bar'
						},
						body: {
							id: 'mw-reference-text-cite_note-bar-1'
						}
					},
					originalMw: '{"name":"ref","attrs":{"name":"bar"},"body":{"id":"mw-reference-text-cite_note-bar-1"}}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/bar',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-bar-1'
				}
			},
			{
				type: '/mwReference'
			},
			'\n',
			'T',
			'e',
			'x',
			't',
			'\n',
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'bar'
						}
					},
					originalMw: '{"name":"ref","attrs":{"name":"bar"}}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/bar',
					refGroup: '',
					contentsUsed: false
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: {},
						autoGenerated: true
					},
					originalMw: '{"name":"references","attrs":{},"autoGenerated":true}',
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: false
				},
				internal: {
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: '/mwReferencesList'
			},
			{
				type: 'internalList'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'Body'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
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
	body:
		'<p id="mwAg"><sup about="#mwt1" class="mw-ref reference" id="cite_ref-bar_1-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-bar-1&quot;}}"><a href="./Example/DeleteReuse#cite_note-bar-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\nText\n<sup about="#mwt2" class="mw-ref reference" id="cite_ref-bar_1-1" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;}}"><a href="./Example/DeleteReuse#cite_note-bar-1" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-bar-1" id="cite_note-bar-1"><span rel="mw:referencedBy" class="mw-cite-backlink" id="mwDQ"><a href="./Example/DeleteReuse#cite_ref-bar_1-0" id="mwDg"><span class="mw-linkback-text" id="mwDw">1 </span></a><a href="./Example/DeleteReuse#cite_ref-bar_1-1" id="mwEA"><span class="mw-linkback-text" id="mwEQ">2 </span></a></span> <span id="mw-reference-text-cite_note-bar-1" class="mw-reference-text reference-text">Body</span></li>\n</ol></div>'
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Simple ref reuse' ] = {
	data: ve.dm.ConverterIntegrationTestCases.SimpleRefReuse.data,
	body: ve.dm.ConverterIntegrationTestCases.SimpleRefReuse.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-bar-1&quot;}}"></sup>\nText\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;}}"></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-bar-1">Body</span></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup about="#mwt1" class="mw-ref reference" id="cite_ref-bar_1-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-bar-1&quot;}}"><a href="./Example/DeleteReuse#cite_note-bar-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\nText\n<sup about="#mwt2" class="mw-ref reference" id="cite_ref-bar_1-1" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;}}"><a href="./Example/DeleteReuse#cite_note-bar-1" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-bar-1" id="cite_note-bar-1"><span rel="mw:referencedBy" class="mw-cite-backlink" id="mwDQ"><a href="./Example/DeleteReuse#cite_ref-bar_1-0" id="mwDg"><span class="mw-linkback-text" id="mwDw">1 </span></a><a href="./Example/DeleteReuse#cite_ref-bar_1-1" id="mwEA"><span class="mw-linkback-text" id="mwEQ">2 </span></a></span> <span id="mw-reference-text-cite_note-bar-1" class="mw-reference-text reference-text">Body</span></li>\n</ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-bar-1&quot;,&quot;html&quot;:&quot;Body&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>\nText\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"><a><span class="mw-linkback-text">1 </span></a><a><span class="mw-linkback-text">2 </span></a></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Body</p></span></div></span></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-bar-1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>↵Text↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"><a><span class="mw-linkback-text">1 </span></a><a><span class="mw-linkback-text">2 </span></a></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Body</p></span></div></span></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

// Wikitext before:
// <ref name="bar">Body</ref>
// Text
// <ref name="bar" />
// Wikitext after:
// Text
// <ref name="bar">Body</ref>
ve.dm.ConverterIntegrationTestCases.cases[ 'Simple ref reuse ( delete ref )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.SimpleRefReuse.data,
	body: ve.dm.ConverterIntegrationTestCases.SimpleRefReuse.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 1, [ [ {
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: { name: 'bar' },
						body: { id: 'mw-reference-text-cite_note-bar-1' }
					},
					originalMw: '{"name":"ref","attrs":{"name":"bar"},"body":{"id":"mw-reference-text-cite_note-bar-1"}}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/bar',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-bar-1'
				},
				originalDomElementsHash: 'h5905f2e6efddeced'
			}, { type: '/mwReference' }, '\n' ], '' ], 20 ] ) );
		},
	fromDataBody:
		'<p>Text\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Body&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol><li><span typeof="mw:Extension/ref">Body</span></li></ol></div>',
	normalizedBody:
		'<p id="mwAg">Text\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Body&quot;}}" class="mw-ref reference" about="#mwt2" id="cite_ref-bar_1-1" rel="dc:references"><a href="./Example/DeleteReuse#cite_note-bar-1" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-bar-1" id="cite_note-bar-1"><span rel="mw:referencedBy" class="mw-cite-backlink" id="mwDQ"><a href="./Example/DeleteReuse#cite_ref-bar_1-0" id="mwDg"><span class="mw-linkback-text" id="mwDw">1 </span></a><a href="./Example/DeleteReuse#cite_ref-bar_1-1" id="mwEA"><span class="mw-linkback-text" id="mwEQ">2 </span></a></span> <span id="mw-reference-text-cite_note-bar-1" class="mw-reference-text reference-text">Body</span></li>\n</ol></div>',
	clipboardBody:
		'<p>Text\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Body&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Body</p></span></div></span></li></ol></div>',
	previewBody:
		'<p>Text↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;bar&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Body</p></span></div></span></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

// Wikitext:
// <ref details="page. 123" name="book">Miller</ref>
ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails = {
	data: [
		{
			type: 'paragraph',
			internal: {
				whitespace: [
					undefined,
					undefined,
					undefined,
					'\n'
				]
			}
		},
		{
			type: 'mwReference',
			attributes: {
				mw: {
					name: 'ref',
					attrs: {
						details: 'page. 123'
					},
					body: {
						id: 'mw-reference-text-cite_note-2'
					},
					mainRef: 'book',
					mainBody: 'mw-reference-text-cite_note-book-1',
					isSubRefWithMainBody: 1
				},
				originalMw: '{"name":"ref","attrs":{"details":"page. 123"},"body":{"id":"mw-reference-text-cite_note-2"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}',
				listIndex: 0,
				listGroup: 'mwReference/',
				listKey: 'auto/0',
				refGroup: '',
				contentsUsed: true,
				mainRefKey: 'literal/book',
				refListItemId: 'mw-reference-text-cite_note-2'
			}
		},
		{
			type: '/mwReference'
		},
		{
			type: '/paragraph'
		},
		{
			type: 'mwReferencesList',
			attributes: {
				mw: {
					name: 'references',
					attrs: {},
					autoGenerated: true,
					body: {
						html: "\n<sup typeof=\"mw:Extension/ref\" data-parsoid=\"{}\" data-mw='{\"name\":\"ref\",\"attrs\":{\"name\":\"book\",\"group\":\"\"},\"body\":{\"id\":\"mw-reference-text-cite_note-book-1\"},\"isSyntheticMainRef\":1}'>Miller</sup>"
					}
				},
				originalMw: "{\"name\":\"references\",\"attrs\":{},\"autoGenerated\":true,\"body\":{\"html\":\"\\n<sup typeof=\\\"mw:Extension/ref\\\" data-parsoid=\\\"{}\\\" data-mw='{\\\"name\\\":\\\"ref\\\",\\\"attrs\\\":{\\\"name\\\":\\\"book\\\",\\\"group\\\":\\\"\\\"},\\\"body\\\":{\\\"id\\\":\\\"mw-reference-text-cite_note-book-1\\\"},\\\"isSyntheticMainRef\\\":1}'>Miller</sup>\"}}",
				refGroup: '',
				listGroup: 'mwReference/',
				isResponsive: true,
				templateGenerated: false
			},
			internal: {
				whitespace: [
					'\n'
				]
			}
		},
		{
			type: 'paragraph',
			internal: {
				generated: 'wrapper',
				whitespace: [
					'\n'
				]
			}
		},
		{
			type: 'mwReference',
			attributes: {
				mw: {
					name: 'ref',
					attrs: {
						name: 'book',
						group: ''
					},
					body: {
						id: 'mw-reference-text-cite_note-book-1'
					},
					isSyntheticMainRef: 1
				},
				originalMw: '{"name":"ref","attrs":{"name":"book","group":""},"body":{"id":"mw-reference-text-cite_note-book-1"},"isSyntheticMainRef":1}',
				listIndex: 1,
				listGroup: 'mwReference/',
				listKey: 'literal/book',
				refGroup: '',
				contentsUsed: true,
				refListItemId: 'mw-reference-text-cite_note-book-1'
			}
		},
		{
			type: '/mwReference'
		},
		{
			type: '/paragraph'
		},
		{
			type: '/mwReferencesList'
		},
		{
			type: 'internalList'
		},
		{
			type: 'internalItem',
			attributes: {
				originalHtml: 'page. 123'
			}
		},
		{
			type: 'paragraph',
			internal: {
				generated: 'wrapper'
			}
		},
		'p',
		'a',
		'g',
		'e',
		'.',
		' ',
		'1',
		'2',
		'3',
		{
			type: '/paragraph'
		},
		{
			type: '/internalItem'
		},
		{
			type: 'internalItem',
			attributes: {
				originalHtml: 'Miller'
			}
		},
		{
			type: 'paragraph',
			internal: {
				generated: 'wrapper'
			}
		},
		'M',
		'i',
		'l',
		'l',
		'e',
		'r',
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
	body:
		"<p id=\"mwAg\"><sup about=\"#mwt1\" class=\"mw-ref reference\" id=\"cite_ref-2\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}\"><a href=\"./Example/MainPlusDetails#cite_note-2\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1.1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt2\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid=\\&quot;{}\\&quot; data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;book\\&quot;,\\&quot;group\\&quot;:\\&quot;\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-book-1\\&quot;},\\&quot;isSyntheticMainRef\\&quot;:1}'&gt;Miller&lt;/sup&gt;&quot;}}\" id=\"mwBw\"><ol class=\"mw-references references\" id=\"mwCA\"><li about=\"#cite_note-book-1\" id=\"cite_note-book-1\"><span rel=\"mw:referencedBy\" class=\"mw-cite-backlink\" id=\"mwCQ\"></span> <span id=\"mw-reference-text-cite_note-book-1\" class=\"mw-reference-text reference-text\">Miller</span><ol class=\"mw-subreference-list\" id=\"mwCg\"><li about=\"#cite_note-2\" id=\"cite_note-2\"><span class=\"mw-cite-backlink\" id=\"mwCw\"><a href=\"./Example/MainPlusDetails#cite_ref-2\" rel=\"mw:referencedBy\" id=\"mwDA\"><span class=\"mw-linkback-text\" id=\"mwDQ\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-2\" class=\"mw-reference-text reference-text\">page. 123</span></li>\n</ol></li>\n</ol></div>"
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Simple main ref including details' ] = {
	data: ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails.data,
	body: ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page. 123</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page. 123</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
	[
		undefined,
		undefined
	],
	preserveAnnotationDomElements:
	true
};

// Wikitext before:
// <ref details="page. 123" name="book">Miller</ref>
// Wikitext after:
// <ref details="page. 123 NEW" name="book">Miller NEW</ref>
ve.dm.ConverterIntegrationTestCases.cases[ 'Simple main ref including details ( edits on main and details )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails.data,
	body: ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 25, [ [ { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, 'M', 'i', 'l', 'l', 'e', 'r', { type: '/paragraph' } ], '' ], 2 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 11, [ [ { type: 'internalItem', attributes: { originalHtml: 'page. 123' } }, { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, 'p', 'a', 'g', 'e', '.', ' ', '1', '2', '3', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'Miller' } }, { type: '/internalItem' } ], [ { type: 'internalItem', attributes: { originalHtml: 'page. 123' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'p', 'a', 'g', 'e', '.', ' ', '1', '2', '3', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'Miller' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'M', 'i', 'l', 'l', 'e', 'r', ' ', 'N', 'E', 'W', { type: '/paragraph' }, { type: '/internalItem' } ] ], 1 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 12, [ [ { type: 'paragraph', internal: { generated: 'wrapper' } }, 'p', 'a', 'g', 'e', '.', ' ', '1', '2', '3', { type: '/paragraph' } ], '' ], 16 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 11, [ [ { type: 'internalItem', attributes: { originalHtml: 'page. 123' } }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'Miller' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'M', 'i', 'l', 'l', 'e', 'r', ' ', 'N', 'E', 'W', { type: '/paragraph' }, { type: '/internalItem' } ], [ { type: 'internalItem', attributes: { originalHtml: 'page. 123' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'p', 'a', 'g', 'e', '.', ' ', '1', '2', '3', ' ', 'N', 'E', 'W', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'Miller' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'M', 'i', 'l', 'l', 'e', 'r', ' ', 'N', 'E', 'W', { type: '/paragraph' }, { type: '/internalItem' } ] ], 1 ] ) );
		},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123 NEW&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller NEW</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page. 123 NEW</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123 NEW&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller NEW</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page. 123 NEW</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123 NEW&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller NEW</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123 NEW</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller NEW</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123 NEW</p></span></div></span></li></ol></li></ol></div>'
};

// Wikitext
// <ref>{{Cite|author=Miller|title=Foo}}</ref>
// <ref name="ldrTpl" />
// <references>
// <ref name="ldrTpl">{{Cite|author=Smith|title=Bar}}</ref>
// </references>
ve.dm.ConverterIntegrationTestCases.simpleTemplateInRefs = {
	data:
		[
			{
				type: 'paragraph',
				internal: {
					whitespace: [
						undefined,
						undefined,
						undefined,
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {},
						body: {
							id: 'mw-reference-text-cite_note-1'
						}
					},
					originalMw: '{"name":"ref","attrs":{},"body":{"id":"mw-reference-text-cite_note-1"}}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'auto/0',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-1'
				}
			},
			{
				type: '/mwReference'
			},
			'\n',
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'ldrTpl'
						}
					},
					originalMw: '{"name":"ref","attrs":{"name":"ldrTpl"}}',
					listIndex: 1,
					listGroup: 'mwReference/',
					listKey: 'literal/ldrTpl',
					refGroup: '',
					contentsUsed: false
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: {},
						body: {
							html: "\n<sup about=\"#mwt5\" class=\"mw-ref reference\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-parsoid='{\"dsr\":[79,135,19,6]}' data-mw='{\"name\":\"ref\",\"attrs\":{\"name\":\"ldrTpl\"},\"body\":{\"id\":\"mw-reference-text-cite_note-ldrTpl-2\"}}'><a href=\"./Example/CiteTemplates#cite_note-ldrTpl-2\" data-parsoid=\"{}\"><span class=\"mw-reflink-text\" data-parsoid=\"{}\"><span class=\"cite-bracket\" data-parsoid=\"{}\">[</span>2<span class=\"cite-bracket\" data-parsoid=\"{}\">]</span></span></a></sup>\n"
						}
					},
					originalMw: "{\"name\":\"references\",\"attrs\":{},\"body\":{\"html\":\"\\n<sup about=\\\"#mwt5\\\" class=\\\"mw-ref reference\\\" rel=\\\"dc:references\\\" typeof=\\\"mw:Extension/ref\\\" data-parsoid='{\\\"dsr\\\":[79,135,19,6]}' data-mw='{\\\"name\\\":\\\"ref\\\",\\\"attrs\\\":{\\\"name\\\":\\\"ldrTpl\\\"},\\\"body\\\":{\\\"id\\\":\\\"mw-reference-text-cite_note-ldrTpl-2\\\"}}'><a href=\\\"./Example/CiteTemplates#cite_note-ldrTpl-2\\\" data-parsoid=\\\"{}\\\"><span class=\\\"mw-reflink-text\\\" data-parsoid=\\\"{}\\\"><span class=\\\"cite-bracket\\\" data-parsoid=\\\"{}\\\">[</span>2<span class=\\\"cite-bracket\\\" data-parsoid=\\\"{}\\\">]</span></span></a></sup>\\n\"}}",
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: false
				},
				internal: {
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper',
					whitespace: [
						'\n',
						undefined,
						undefined,
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'ldrTpl'
						},
						body: {
							id: 'mw-reference-text-cite_note-ldrTpl-2'
						}
					},
					originalMw: '{"name":"ref","attrs":{"name":"ldrTpl"},"body":{"id":"mw-reference-text-cite_note-ldrTpl-2"}}',
					listIndex: 1,
					listGroup: 'mwReference/',
					listKey: 'literal/ldrTpl',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-ldrTpl-2'
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: '/mwReferencesList'
			},
			{
				type: 'internalList'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt1" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwEA">Template:Cite</a>'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			{
				type: 'mwTransclusionInline',
				attributes: {
					mw: {
						parts: [
							{
								template: {
									target: {
										wt: 'Cite',
										href: './Template:Cite'
									},
									params: {
										author: {
											wt: 'Miller'
										},
										title: {
											wt: 'Foo'
										}
									},
									i: 0
								}
							}
						]
					},
					originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Miller"},"title":{"wt":"Foo"}},"i":0}}]}'
				}
			},
			{
				type: '/mwTransclusionInline'
			},
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt4" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwFA">Template:Cite</a>'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			{
				type: 'mwTransclusionInline',
				attributes: {
					mw: {
						parts: [
							{
								template: {
									target: {
										wt: 'Cite',
										href: './Template:Cite'
									},
									params: {
										author: {
											wt: 'Smith'
										},
										title: {
											wt: 'Bar'
										}
									},
									i: 0
								}
							}
						]
					},
					originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Smith"},"title":{"wt":"Bar"}},"i":0}}]}'
				}
			},
			{
				type: '/mwTransclusionInline'
			},
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
	body:
		"<p id=\"mwAg\"><sup about=\"#mwt2\" class=\"mw-ref reference\" id=\"cite_ref-1\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}\"><a href=\"./Example/CiteTemplates#cite_note-1\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup>\n<sup about=\"#mwt3\" class=\"mw-ref reference\" id=\"cite_ref-ldrTpl_2-0\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}\"><a href=\"./Example/CiteTemplates#cite_note-ldrTpl-2\" id=\"mwBw\"><span class=\"mw-reflink-text\" id=\"mwCA\"><span class=\"cite-bracket\" id=\"mwCQ\">[</span>2<span class=\"cite-bracket\" id=\"mwCg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt6\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup about=\\&quot;#mwt5\\&quot; class=\\&quot;mw-ref reference\\&quot; rel=\\&quot;dc:references\\&quot; typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid='{\\&quot;dsr\\&quot;:[79,135,19,6]}' data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;ldrTpl\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-ldrTpl-2\\&quot;}}'&gt;&lt;a href=\\&quot;./Example/CiteTemplates#cite_note-ldrTpl-2\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}\" id=\"mwCw\"><ol class=\"mw-references references\" id=\"mwDA\"><li about=\"#cite_note-1\" id=\"cite_note-1\"><span class=\"mw-cite-backlink\" id=\"mwDQ\"><a href=\"./Example/CiteTemplates#cite_ref-1\" rel=\"mw:referencedBy\" id=\"mwDg\"><span class=\"mw-linkback-text\" id=\"mwDw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-1\" class=\"mw-reference-text reference-text\"><a rel=\"mw:WikiLink\" href=\"./Template:Cite?action=edit&amp;redlink=1\" title=\"Template:Cite\" about=\"#mwt1\" typeof=\"mw:Transclusion mw:LocalizedAttrs\" class=\"new\" data-mw=\"{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}\" data-mw-i18n=\"{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}\" id=\"mwEA\">Template:Cite</a></span></li>\n<li about=\"#cite_note-ldrTpl-2\" id=\"cite_note-ldrTpl-2\"><span class=\"mw-cite-backlink\" id=\"mwEQ\"><a href=\"./Example/CiteTemplates#cite_ref-ldrTpl_2-0\" rel=\"mw:referencedBy\" id=\"mwEg\"><span class=\"mw-linkback-text\" id=\"mwEw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-ldrTpl-2\" class=\"mw-reference-text reference-text\"><a rel=\"mw:WikiLink\" href=\"./Template:Cite?action=edit&amp;redlink=1\" title=\"Template:Cite\" about=\"#mwt4\" typeof=\"mw:Transclusion mw:LocalizedAttrs\" class=\"new\" data-mw=\"{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}\" data-mw-i18n=\"{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}\" id=\"mwFA\">Template:Cite</a></span></li>\n</ol></div>"
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Simple template used in refs' ] = {
	data: ve.dm.ConverterIntegrationTestCases.simpleTemplateInRefs.data,
	body: ve.dm.ConverterIntegrationTestCases.simpleTemplateInRefs.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;&lt;span typeof=\\&quot;mw:Transclusion\\&quot; data-mw=\\&quot;{&amp;quot;parts&amp;quot;:[{&amp;quot;template&amp;quot;:{&amp;quot;target&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Cite&amp;quot;,&amp;quot;href&amp;quot;:&amp;quot;./Template:Cite&amp;quot;},&amp;quot;params&amp;quot;:{&amp;quot;author&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;title&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Foo&amp;quot;}},&amp;quot;i&amp;quot;:0}}]}\\&quot;&gt;&lt;/span&gt;&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}"></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;&amp;lt;span typeof=\\\\&amp;quot;mw:Transclusion\\\\&amp;quot; data-mw=\\\\&amp;quot;{&amp;amp;quot;parts&amp;amp;quot;:[{&amp;amp;quot;template&amp;amp;quot;:{&amp;amp;quot;target&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Cite&amp;amp;quot;,&amp;amp;quot;href&amp;amp;quot;:&amp;amp;quot;./Template:Cite&amp;amp;quot;},&amp;amp;quot;params&amp;amp;quot;:{&amp;amp;quot;author&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Smith&amp;amp;quot;},&amp;amp;quot;title&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Bar&amp;amp;quot;}},&amp;amp;quot;i&amp;amp;quot;:0}}]}\\\\&amp;quot;&amp;gt;&amp;lt;/span&amp;gt;&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-1"><span typeof="mw:Transclusion" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}"></span></span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-ldrTpl-2"><span typeof="mw:Transclusion" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}"></span></span></li></ol></div>',
	normalizedBody:
		"<p id=\"mwAg\"><sup about=\"#mwt2\" class=\"mw-ref reference\" id=\"cite_ref-1\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}\"><a href=\"./Example/CiteTemplates#cite_note-1\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup>\n<sup about=\"#mwt3\" class=\"mw-ref reference\" id=\"cite_ref-ldrTpl_2-0\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}\"><a href=\"./Example/CiteTemplates#cite_note-ldrTpl-2\" id=\"mwBw\"><span class=\"mw-reflink-text\" id=\"mwCA\"><span class=\"cite-bracket\" id=\"mwCQ\">[</span>2<span class=\"cite-bracket\" id=\"mwCg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt6\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup about=\\&quot;#mwt5\\&quot; class=\\&quot;mw-ref reference\\&quot; rel=\\&quot;dc:references\\&quot; typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid='{\\&quot;dsr\\&quot;:[79,135,19,6]}' data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;ldrTpl\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-ldrTpl-2\\&quot;}}'&gt;&lt;a href=\\&quot;./Example/CiteTemplates#cite_note-ldrTpl-2\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}\" id=\"mwCw\"><ol class=\"mw-references references\" id=\"mwDA\"><li about=\"#cite_note-1\" id=\"cite_note-1\"><span class=\"mw-cite-backlink\" id=\"mwDQ\"><a href=\"./Example/CiteTemplates#cite_ref-1\" rel=\"mw:referencedBy\" id=\"mwDg\"><span class=\"mw-linkback-text\" id=\"mwDw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-1\" class=\"mw-reference-text reference-text\"><a rel=\"mw:WikiLink\" href=\"./Template:Cite?action=edit&amp;redlink=1\" title=\"Template:Cite\" about=\"#mwt1\" typeof=\"mw:Transclusion mw:LocalizedAttrs\" class=\"new\" data-mw=\"{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}\" data-mw-i18n=\"{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}\" id=\"mwEA\">Template:Cite</a></span></li>\n<li about=\"#cite_note-ldrTpl-2\" id=\"cite_note-ldrTpl-2\"><span class=\"mw-cite-backlink\" id=\"mwEQ\"><a href=\"./Example/CiteTemplates#cite_ref-ldrTpl_2-0\" rel=\"mw:referencedBy\" id=\"mwEg\"><span class=\"mw-linkback-text\" id=\"mwEw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-ldrTpl-2\" class=\"mw-reference-text reference-text\"><a rel=\"mw:WikiLink\" href=\"./Template:Cite?action=edit&amp;redlink=1\" title=\"Template:Cite\" about=\"#mwt4\" typeof=\"mw:Transclusion mw:LocalizedAttrs\" class=\"new\" data-mw=\"{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}\" data-mw-i18n=\"{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}\" id=\"mwFA\">Template:Cite</a></span></li>\n</ol></div>",
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;&lt;span typeof=\\&quot;mw:Transclusion\\&quot; data-mw=\\&quot;{&amp;quot;parts&amp;quot;:[{&amp;quot;template&amp;quot;:{&amp;quot;target&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Cite&amp;quot;,&amp;quot;href&amp;quot;:&amp;quot;./Template:Cite&amp;quot;},&amp;quot;params&amp;quot;:{&amp;quot;author&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;title&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Foo&amp;quot;}},&amp;quot;i&amp;quot;:0}}]}\\&quot; data-ve-no-generated-contents=\\&quot;true\\&quot;&gt;&amp;nbsp;&lt;/span&gt;&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;&amp;lt;span typeof=\\\\&amp;quot;mw:Transclusion\\\\&amp;quot; data-mw=\\\\&amp;quot;{&amp;amp;quot;parts&amp;amp;quot;:[{&amp;amp;quot;template&amp;amp;quot;:{&amp;amp;quot;target&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Cite&amp;amp;quot;,&amp;amp;quot;href&amp;amp;quot;:&amp;amp;quot;./Template:Cite&amp;amp;quot;},&amp;amp;quot;params&amp;amp;quot;:{&amp;amp;quot;author&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Smith&amp;amp;quot;},&amp;amp;quot;title&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Bar&amp;amp;quot;}},&amp;amp;quot;i&amp;amp;quot;:0}}]}\\\\&amp;quot; data-ve-no-generated-contents=\\\\&amp;quot;true\\\\&amp;quot;&amp;gt;&amp;amp;nbsp;&amp;lt;/span&amp;gt;&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li><li style="--footnote-number: &quot;2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li><li style="--footnote-number: &quot;2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

// Wikitext before:
// <ref>{{Cite|author=Miller|title=Foo}}</ref>
// <ref name="ldrTpl" />
// <references>
// <ref name="ldrTpl">{{Cite|author=Smith|title=Bar}}</ref>
// </references>
//
// Wikitext after:
// <ref>{{Cite|author=Miller|title=Foo New}}</ref>
// <ref name="ldrTpl" />
// <references>
// <ref name="ldrTpl">{{Cite|author=Smith|title=Bar New}}</ref>
// </references>
ve.dm.ConverterIntegrationTestCases.cases[ 'Simple template in refs ( edits on template parameters )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.simpleTemplateInRefs.data,
	body: ve.dm.ConverterIntegrationTestCases.simpleTemplateInRefs.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 15, [ [ { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Miller' }, title: { wt: 'Foo' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Miller"},"title":{"wt":"Foo"}},"i":0}}]}' }, originalDomElementsHash: 'h9f955be85e91fd8f' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' } ], '' ], 8 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 14, [ [ { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt1" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwEA">Template:Cite</a>' } }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt4" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwFA">Template:Cite</a>' } }, { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Smith' }, title: { wt: 'Bar' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Smith"},"title":{"wt":"Bar"}},"i":0}}]}' }, originalDomElementsHash: 'h22c504c521f00956' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' }, { type: '/internalItem' } ], [ { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt1" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwEA">Template:Cite</a>' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Miller' }, title: { wt: 'Foo New' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Miller"},"title":{"wt":"Foo"}},"i":0}}]}' }, originalDomElementsHash: 'h9f955be85e91fd8f' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt4" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwFA">Template:Cite</a>' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Smith' }, title: { wt: 'Bar' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Smith"},"title":{"wt":"Bar"}},"i":0}}]}' }, originalDomElementsHash: 'h22c504c521f00956' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' }, { type: '/internalItem' } ] ], 1 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 21, [ [ { type: 'paragraph', internal: { generated: 'wrapper' } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Smith' }, title: { wt: 'Bar' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Smith"},"title":{"wt":"Bar"}},"i":0}}]}' }, originalDomElementsHash: 'h22c504c521f00956' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' } ], '' ], 2 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 14, [ [ { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt1" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwEA">Template:Cite</a>' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Miller' }, title: { wt: 'Foo New' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Miller"},"title":{"wt":"Foo"}},"i":0}}]}' }, originalDomElementsHash: 'h9f955be85e91fd8f' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt4" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwFA">Template:Cite</a>' } }, { type: '/internalItem' } ], [ { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt1" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwEA">Template:Cite</a>' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Miller' }, title: { wt: 'Foo New' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Miller"},"title":{"wt":"Foo"}},"i":0}}]}' }, originalDomElementsHash: 'h9f955be85e91fd8f' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: '<a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt4" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwFA">Template:Cite</a>' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, { type: 'mwTransclusionInline', attributes: { mw: { parts: [ { template: { target: { wt: 'Cite', href: './Template:Cite' }, params: { author: { wt: 'Smith' }, title: { wt: 'Bar New' } }, i: 0 } } ] }, originalMw: '{"parts":[{"template":{"target":{"wt":"Cite","href":"./Template:Cite"},"params":{"author":{"wt":"Smith"},"title":{"wt":"Bar"}},"i":0}}]}' }, originalDomElementsHash: 'h22c504c521f00956' }, { type: '/mwTransclusionInline' }, { type: '/paragraph' }, { type: '/internalItem' } ] ], 1 ] ) );
		},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;&lt;span typeof=\\&quot;mw:Transclusion\\&quot; data-mw=\\&quot;{&amp;quot;parts&amp;quot;:[{&amp;quot;template&amp;quot;:{&amp;quot;target&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Cite&amp;quot;,&amp;quot;href&amp;quot;:&amp;quot;./Template:Cite&amp;quot;},&amp;quot;params&amp;quot;:{&amp;quot;author&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;title&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Foo New&amp;quot;}},&amp;quot;i&amp;quot;:0}}]}\\&quot;&gt;&lt;/span&gt;&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}"></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;&amp;lt;span typeof=\\\\&amp;quot;mw:Transclusion\\\\&amp;quot; data-mw=\\\\&amp;quot;{&amp;amp;quot;parts&amp;amp;quot;:[{&amp;amp;quot;template&amp;amp;quot;:{&amp;amp;quot;target&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Cite&amp;amp;quot;,&amp;amp;quot;href&amp;amp;quot;:&amp;amp;quot;./Template:Cite&amp;amp;quot;},&amp;amp;quot;params&amp;amp;quot;:{&amp;amp;quot;author&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Smith&amp;amp;quot;},&amp;amp;quot;title&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Bar New&amp;amp;quot;}},&amp;amp;quot;i&amp;amp;quot;:0}}]}\\\\&amp;quot;&amp;gt;&amp;lt;/span&amp;gt;&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-1"><span typeof="mw:Transclusion" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo New&quot;}},&quot;i&quot;:0}}]}"></span></span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-ldrTpl-2"><span typeof="mw:Transclusion" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar New&quot;}},&quot;i&quot;:0}}]}"></span></span></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;&lt;a typeof=\\&quot;mw:Transclusion\\&quot; data-mw=\\&quot;{&amp;quot;parts&amp;quot;:[{&amp;quot;template&amp;quot;:{&amp;quot;target&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Cite&amp;quot;,&amp;quot;href&amp;quot;:&amp;quot;./Template:Cite&amp;quot;},&amp;quot;params&amp;quot;:{&amp;quot;author&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;title&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Foo New&amp;quot;}},&amp;quot;i&amp;quot;:0}}]}\\&quot; id=\\&quot;mwEA\\&quot;&gt;&lt;/a&gt;&quot;}}" class="mw-ref reference" about="#mwt2" id="cite_ref-1" rel="dc:references"><a href="./Example/CiteTemplates#cite_note-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup about="#mwt3" class="mw-ref reference" id="cite_ref-ldrTpl_2-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}"><a href="./Example/CiteTemplates#cite_note-ldrTpl-2" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>2<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;&amp;lt;a typeof=\\\\&amp;quot;mw:Transclusion\\\\&amp;quot; data-mw=\\\\&amp;quot;{&amp;amp;quot;parts&amp;amp;quot;:[{&amp;amp;quot;template&amp;amp;quot;:{&amp;amp;quot;target&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Cite&amp;amp;quot;,&amp;amp;quot;href&amp;amp;quot;:&amp;amp;quot;./Template:Cite&amp;amp;quot;},&amp;amp;quot;params&amp;amp;quot;:{&amp;amp;quot;author&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Smith&amp;amp;quot;},&amp;amp;quot;title&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Bar New&amp;amp;quot;}},&amp;amp;quot;i&amp;amp;quot;:0}}]}\\\\&amp;quot; id=\\\\&amp;quot;mwFA\\\\&amp;quot;&amp;gt;&amp;lt;/a&amp;gt;&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot; about=\\&quot;#mwt5\\&quot; rel=\\&quot;dc:references\\&quot; data-parsoid=\\&quot;{&amp;quot;dsr&amp;quot;:[79,135,19,6]}\\&quot;&gt;&lt;a href=\\&quot;./Example/CiteTemplates#cite_note-ldrTpl-2\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-1"><a typeof="mw:Transclusion" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo New&quot;}},&quot;i&quot;:0}}]}" id="mwEA"></a></span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-ldrTpl-2"><a typeof="mw:Transclusion" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar New&quot;}},&quot;i&quot;:0}}]}" id="mwFA"></a></span></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;&lt;span typeof=\\&quot;mw:Transclusion\\&quot; data-mw=\\&quot;{&amp;quot;parts&amp;quot;:[{&amp;quot;template&amp;quot;:{&amp;quot;target&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Cite&amp;quot;,&amp;quot;href&amp;quot;:&amp;quot;./Template:Cite&amp;quot;},&amp;quot;params&amp;quot;:{&amp;quot;author&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;title&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Foo New&amp;quot;}},&amp;quot;i&amp;quot;:0}}]}\\&quot; data-ve-no-generated-contents=\\&quot;true\\&quot;&gt;&amp;nbsp;&lt;/span&gt;&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;&amp;lt;span typeof=\\\\&amp;quot;mw:Transclusion\\\\&amp;quot; data-mw=\\\\&amp;quot;{&amp;amp;quot;parts&amp;amp;quot;:[{&amp;amp;quot;template&amp;amp;quot;:{&amp;amp;quot;target&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Cite&amp;amp;quot;,&amp;amp;quot;href&amp;amp;quot;:&amp;amp;quot;./Template:Cite&amp;amp;quot;},&amp;amp;quot;params&amp;amp;quot;:{&amp;amp;quot;author&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Smith&amp;amp;quot;},&amp;amp;quot;title&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Bar New&amp;amp;quot;}},&amp;amp;quot;i&amp;amp;quot;:0}}]}\\\\&amp;quot; data-ve-no-generated-contents=\\\\&amp;quot;true\\\\&amp;quot;&amp;gt;&amp;amp;nbsp;&amp;lt;/span&amp;gt;&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li><li style="--footnote-number: &quot;2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li><li style="--footnote-number: &quot;2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper"><span class="ve-ce-leafNode ve-ce-generatedContentNode-generating ve-ce-focusableNode ve-ce-mwTransclusionNode" contenteditable="false"></span></p></span></div></span></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub = {
	data:
		[
			{
				type: 'paragraph',
				internal: {
					whitespace: [
						undefined,
						undefined,
						undefined,
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							details: 'page 1'
						},
						mainRef: 'book',
						body: {
							id: 'mw-reference-text-cite_note-2'
						}
					},
					originalMw: '{"name":"ref","attrs":{"details":"page 1"},"mainRef":"book","body":{"id":"mw-reference-text-cite_note-2"}}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'auto/0',
					refGroup: '',
					contentsUsed: true,
					mainRefKey: 'literal/book',
					refListItemId: 'mw-reference-text-cite_note-2'
				}
			},
			{
				type: '/mwReference'
			},
			'\n',
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							details: 'page 2'
						},
						body: {
							id: 'mw-reference-text-cite_note-3'
						},
						mainRef: 'book',
						mainBody: 'mw-reference-text-cite_note-book-1',
						isSubRefWithMainBody: 1
					},
					originalMw: '{"name":"ref","attrs":{"details":"page 2"},"body":{"id":"mw-reference-text-cite_note-3"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}',
					listIndex: 1,
					listGroup: 'mwReference/',
					listKey: 'auto/1',
					refGroup: '',
					contentsUsed: true,
					mainRefKey: 'literal/book',
					refListItemId: 'mw-reference-text-cite_note-3'
				}
			},
			{
				type: '/mwReference'
			},
			'\n',
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'book'
						}
					},
					originalMw: '{"name":"ref","attrs":{"name":"book"}}',
					listIndex: 2,
					listGroup: 'mwReference/',
					listKey: 'literal/book',
					refGroup: '',
					contentsUsed: false
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: {},
						autoGenerated: true,
						body: {
							html: "\n<sup typeof=\"mw:Extension/ref\" data-parsoid=\"{}\" data-mw='{\"name\":\"ref\",\"attrs\":{\"name\":\"book\",\"group\":\"\"},\"body\":{\"id\":\"mw-reference-text-cite_note-book-1\"},\"isSyntheticMainRef\":1}'>Miller</sup>"
						}
					},
					originalMw: "{\"name\":\"references\",\"attrs\":{},\"autoGenerated\":true,\"body\":{\"html\":\"\\n<sup typeof=\\\"mw:Extension/ref\\\" data-parsoid=\\\"{}\\\" data-mw='{\\\"name\\\":\\\"ref\\\",\\\"attrs\\\":{\\\"name\\\":\\\"book\\\",\\\"group\\\":\\\"\\\"},\\\"body\\\":{\\\"id\\\":\\\"mw-reference-text-cite_note-book-1\\\"},\\\"isSyntheticMainRef\\\":1}'>Miller</sup>\"}}",
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: false
				},
				internal: {
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper',
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'book',
							group: ''
						},
						body: {
							id: 'mw-reference-text-cite_note-book-1'
						},
						isSyntheticMainRef: 1
					},
					originalMw: '{"name":"ref","attrs":{"name":"book","group":""},"body":{"id":"mw-reference-text-cite_note-book-1"},"isSyntheticMainRef":1}',
					listIndex: 2,
					listGroup: 'mwReference/',
					listKey: 'literal/book',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-book-1'
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: '/mwReferencesList'
			},
			{
				type: 'internalList'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'page 1'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'p',
			'a',
			'g',
			'e',
			' ',
			'1',
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'page 2'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'p',
			'a',
			'g',
			'e',
			' ',
			'2',
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'Miller'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'M',
			'i',
			'l',
			'l',
			'e',
			'r',
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
	body:
		"<p id=\"mwAg\"><sup about=\"#mwt1\" class=\"mw-ref reference\" id=\"cite_ref-2\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}\"><a href=\"./Example/MainPlusDetails#cite_note-2\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1.1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup>\n<sup about=\"#mwt2\" class=\"mw-ref reference\" id=\"cite_ref-3\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}\"><a href=\"./Example/MainPlusDetails#cite_note-3\" id=\"mwBw\"><span class=\"mw-reflink-text\" id=\"mwCA\"><span class=\"cite-bracket\" id=\"mwCQ\">[</span>1.2<span class=\"cite-bracket\" id=\"mwCg\">]</span></span></a></sup>\n<sup about=\"#mwt3\" class=\"mw-ref reference\" id=\"cite_ref-book_1-0\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}\"><a href=\"./Example/MainPlusDetails#cite_note-book-1\" id=\"mwCw\"><span class=\"mw-reflink-text\" id=\"mwDA\"><span class=\"cite-bracket\" id=\"mwDQ\">[</span>1<span class=\"cite-bracket\" id=\"mwDg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt4\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid=\\&quot;{}\\&quot; data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;book\\&quot;,\\&quot;group\\&quot;:\\&quot;\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-book-1\\&quot;},\\&quot;isSyntheticMainRef\\&quot;:1}'&gt;Miller&lt;/sup&gt;&quot;}}\" id=\"mwDw\"><ol class=\"mw-references references\" id=\"mwEA\"><li about=\"#cite_note-book-1\" id=\"cite_note-book-1\"><span class=\"mw-cite-backlink\" id=\"mwEQ\"><a href=\"./Example/MainPlusDetails#cite_ref-book_1-0\" rel=\"mw:referencedBy\" id=\"mwEg\"><span class=\"mw-linkback-text\" id=\"mwEw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-book-1\" class=\"mw-reference-text reference-text\">Miller</span><ol class=\"mw-subreference-list\" id=\"mwFA\"><li about=\"#cite_note-2\" id=\"cite_note-2\"><span class=\"mw-cite-backlink\" id=\"mwFQ\"><a href=\"./Example/MainPlusDetails#cite_ref-2\" rel=\"mw:referencedBy\" id=\"mwFg\"><span class=\"mw-linkback-text\" id=\"mwFw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-2\" class=\"mw-reference-text reference-text\">page 1</span></li>\n<li about=\"#cite_note-3\" id=\"cite_note-3\"><span class=\"mw-cite-backlink\" id=\"mwGA\"><a href=\"./Example/MainPlusDetails#cite_ref-3\" rel=\"mw:referencedBy\" id=\"mwGQ\"><span class=\"mw-linkback-text\" id=\"mwGg\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-3\" class=\"mw-reference-text reference-text\">page 2</span></li>\n</ol></li>\n</ol></div>"
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Moving main content from subref' ] = {
	data: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.data,
	body: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}"></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-3">page 2</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt2" id="cite_ref-3" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-3" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1.2<span class="cite-bracket" id="mwCg">]</span></span></a></sup>\n<sup about="#mwt3" class="mw-ref reference" id="cite_ref-book_1-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}"><a href="./Example/MainPlusDetails#cite_note-book-1" id="mwCw"><span class="mw-reflink-text" id="mwDA"><span class="cite-bracket" id="mwDQ">[</span>1<span class="cite-bracket" id="mwDg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-3">page 2</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;,&quot;html&quot;:&quot;page 2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li><li style="--footnote-number: &quot;1.2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 2</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li><li style="--footnote-number: &quot;1.2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 2</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Moving main content from subref ( delete subref providing the main )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.data,
	body: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.body,
	modify:
	( model ) => {
		model.commit( ve.dm.Transaction.static.deserialize( [ 4, [ [ { type: 'mwReference', attributes: { mw: { name: 'ref', attrs: { details: 'page 2' }, body: { id: 'mw-reference-text-cite_note-3' }, mainRef: 'book', mainBody: 'mw-reference-text-cite_note-book-1', isSubRefWithMainBody: 1 }, originalMw: '{"name":"ref","attrs":{"details":"page 2"},"body":{"id":"mw-reference-text-cite_note-3"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}', listIndex: 1, listGroup: 'mwReference/', listKey: 'auto/1', refGroup: '', contentsUsed: true, mainRefKey: 'literal/book', refListItemId: 'mw-reference-text-cite_note-3' }, originalDomElementsHash: 'h67fe10d1b207f7de' }, { type: '/mwReference' } ], '' ], 42 ] ) );
	},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference" about="#mwt3" id="cite_ref-book_1-0" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-book-1" id="mwCw"><span class="mw-reflink-text" id="mwDA"><span class="cite-bracket" id="mwDQ">[</span>1<span class="cite-bracket" id="mwDg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>↵↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Moving main content from subref ( delete all subrefs )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.data,
	body: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 1, [ [ { type: 'mwReference', attributes: { mw: { name: 'ref', attrs: { details: 'page 1' }, mainRef: 'book', body: { id: 'mw-reference-text-cite_note-2' } }, originalMw: '{"name":"ref","attrs":{"details":"page 1"},"mainRef":"book","body":{"id":"mw-reference-text-cite_note-2"}}', listIndex: 0, listGroup: 'mwReference/', listKey: 'auto/0', refGroup: '', contentsUsed: true, mainRefKey: 'literal/book', refListItemId: 'mw-reference-text-cite_note-2' }, originalDomElementsHash: 'h048318195d003e3e' }, { type: '/mwReference' }, '\n', { type: 'mwReference', attributes: { mw: { name: 'ref', attrs: { details: 'page 2' }, body: { id: 'mw-reference-text-cite_note-3' }, mainRef: 'book', mainBody: 'mw-reference-text-cite_note-book-1', isSubRefWithMainBody: 1 }, originalMw: '{"name":"ref","attrs":{"details":"page 2"},"body":{"id":"mw-reference-text-cite_note-3"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}', listIndex: 1, listGroup: 'mwReference/', listKey: 'auto/1', refGroup: '', contentsUsed: true, mainRefKey: 'literal/book', refListItemId: 'mw-reference-text-cite_note-3' }, originalDomElementsHash: 'h67fe10d1b207f7de' }, { type: '/mwReference' }, '\n' ], '' ], 41 ] ) );
		},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference" about="#mwt3" id="cite_ref-book_1-0" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-book-1" id="mwCw"><span class="mw-reflink-text" id="mwDA"><span class="cite-bracket" id="mwDQ">[</span>1<span class="cite-bracket" id="mwDg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Moving main content from subref ( delete subref providing the main and other reuse )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.data,
	body: ve.dm.ConverterIntegrationTestCases.movingMainContentFromSub.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 3, [ [ '\n', { type: 'mwReference', attributes: { mw: { name: 'ref', attrs: { details: 'page 2' }, body: { id: 'mw-reference-text-cite_note-3' }, mainRef: 'book', mainBody: 'mw-reference-text-cite_note-book-1', isSubRefWithMainBody: 1 }, originalMw: '{"name":"ref","attrs":{"details":"page 2"},"body":{"id":"mw-reference-text-cite_note-3"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}', listIndex: 1, listGroup: 'mwReference/', listKey: 'auto/1', refGroup: '', contentsUsed: true, mainRefKey: 'literal/book', refListItemId: 'mw-reference-text-cite_note-3' }, originalDomElementsHash: 'h67fe10d1b207f7de' }, { type: '/mwReference' }, '\n', { type: 'mwReference', attributes: { mw: { name: 'ref', attrs: { name: 'book' } }, originalMw: '{"name":"ref","attrs":{"name":"book"}}', listIndex: 2, listGroup: 'mwReference/', listKey: 'literal/book', refGroup: '', contentsUsed: false }, originalDomElementsHash: 'hdeef8b6886d0e53f' }, { type: '/mwReference' } ], '' ], 39 ] ) );
		},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

// Wikitext:
// <ref name="book">Miller</ref>
// <ref name="book" details="page 1" />
ve.dm.ConverterIntegrationTestCases.deleteMainUsedBySub = {
	data:
		[
			{
				type: 'paragraph',
				internal: {
					whitespace: [
						undefined,
						undefined,
						undefined,
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'book'
						},
						body: {
							id: 'mw-reference-text-cite_note-book-1'
						}
					},
					originalMw: '{"name":"ref","attrs":{"name":"book"},"body":{"id":"mw-reference-text-cite_note-book-1"}}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/book',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-book-1'
				}
			},
			{
				type: '/mwReference'
			},
			'\n',
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							details: 'page 1'
						},
						mainRef: 'book',
						body: {
							id: 'mw-reference-text-cite_note-2'
						}
					},
					originalMw: '{"name":"ref","attrs":{"details":"page 1"},"mainRef":"book","body":{"id":"mw-reference-text-cite_note-2"}}',
					listIndex: 1,
					listGroup: 'mwReference/',
					listKey: 'auto/0',
					refGroup: '',
					contentsUsed: true,
					mainRefKey: 'literal/book',
					refListItemId: 'mw-reference-text-cite_note-2'
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: {},
						autoGenerated: true
					},
					originalMw: '{"name":"references","attrs":{},"autoGenerated":true}',
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: false
				},
				internal: {
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: '/mwReferencesList'
			},
			{
				type: 'internalList'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'Miller'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'M',
			'i',
			'l',
			'l',
			'e',
			'r',
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'page 1'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'p',
			'a',
			'g',
			'e',
			' ',
			'1',
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
	body:
		'<p id="mwAg"><sup about="#mwt1" class="mw-ref reference" id="cite_ref-book_1-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-book-1&quot;}}"><a href="./Example/MainPlusDetails#cite_note-book-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup about="#mwt2" class="mw-ref reference" id="cite_ref-2" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}"><a href="./Example/MainPlusDetails#cite_note-2" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1.1<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-book-1" id="cite_note-book-1"><span class="mw-cite-backlink" id="mwDQ"><a href="./Example/MainPlusDetails#cite_ref-book_1-0" rel="mw:referencedBy" id="mwDg"><span class="mw-linkback-text" id="mwDw">↑ </span></a></span> <span id="mw-reference-text-cite_note-book-1" class="mw-reference-text reference-text">Miller</span><ol class="mw-subreference-list" id="mwEA"><li about="#cite_note-2" id="cite_note-2"><span class="mw-cite-backlink" id="mwEQ"><a href="./Example/MainPlusDetails#cite_ref-2" rel="mw:referencedBy" id="mwEg"><span class="mw-linkback-text" id="mwEw">↑ </span></a></span> <span id="mw-reference-text-cite_note-2" class="mw-reference-text reference-text">page 1</span></li>\n</ol></li>\n</ol></div>'
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Delete main used by sub' ] = {
	data: ve.dm.ConverterIntegrationTestCases.deleteMainUsedBySub.data,
	body: ve.dm.ConverterIntegrationTestCases.deleteMainUsedBySub.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-book-1&quot;}}"></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup about="#mwt1" class="mw-ref reference" id="cite_ref-book_1-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-book-1&quot;}}"><a href="./Example/MainPlusDetails#cite_note-book-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference" about="#mwt2" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1.1<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-book-1" id="cite_note-book-1"><span class="mw-cite-backlink" id="mwDQ"><a href="./Example/MainPlusDetails#cite_ref-book_1-0" rel="mw:referencedBy" id="mwDg"><span class="mw-linkback-text" id="mwDw">↑ </span></a></span> <span id="mw-reference-text-cite_note-book-1" class="mw-reference-text reference-text">Miller</span><ol class="mw-subreference-list" id="mwEA"><li about="#cite_note-2" id="cite_note-2"><span class="mw-cite-backlink" id="mwEQ"><a href="./Example/MainPlusDetails#cite_ref-2" rel="mw:referencedBy" id="mwEg"><span class="mw-linkback-text" id="mwEw">↑ </span></a></span> <span id="mw-reference-text-cite_note-2" class="mw-reference-text reference-text">page 1</span></li>\n</ol></li>\n</ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;html&quot;:&quot;Miller&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-book-1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

// Wikitext before:
// <ref name="book">Miller</ref>
// <ref details="page 1" name="book" />
// Wikitext after:
// <ref details="page 1" name="book" >Miller</ref>
ve.dm.ConverterIntegrationTestCases.cases[ 'Delete main used by sub ( delete main ref )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.deleteMainUsedBySub.data,
	body: ve.dm.ConverterIntegrationTestCases.deleteMainUsedBySub.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 1, [ [ { type: 'mwReference', attributes: { mw: { name: 'ref', attrs: { name: 'book' }, body: { id: 'mw-reference-text-cite_note-book-1' } }, originalMw: '{"name":"ref","attrs":{"name":"book"},"body":{"id":"mw-reference-text-cite_note-book-1"}}', listIndex: 0, listGroup: 'mwReference/', listKey: 'literal/book', refGroup: '', contentsUsed: true, refListItemId: 'mw-reference-text-cite_note-book-1' }, originalDomElementsHash: 'h056858c4de1a1101' }, { type: '/mwReference' }, '\n' ], '' ], 27 ] ) );
		},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol><li class="ve-ce-mwReferencesListNode-missingRef"><span></span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference" about="#mwt2" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1.1<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt3" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-book-1" id="cite_note-book-1"><span class="mw-cite-backlink" id="mwDQ"><a href="./Example/MainPlusDetails#cite_ref-book_1-0" rel="mw:referencedBy" id="mwDg"><span class="mw-linkback-text" id="mwDw">↑ </span></a></span> <span id="mw-reference-text-cite_note-book-1" class="mw-reference-text reference-text">Miller</span><ol class="mw-subreference-list" id="mwEA"><li about="#cite_note-2" id="cite_note-2"><span class="mw-cite-backlink" id="mwEQ"><a href="./Example/MainPlusDetails#cite_ref-2" rel="mw:referencedBy" id="mwEg"><span class="mw-linkback-text" id="mwEw">↑ </span></a></span> <span id="mw-reference-text-cite_note-2" class="mw-reference-text reference-text">page 1</span></li>\n</ol></li>\n</ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;" class="ve-ce-mwReferencesListNode-missingRef"><span rel="mw:referencedBy"></span> <span class="ve-ce-mwReferencesListNode-muted">cite-ve-referenceslist-missing-parent</span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;}}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;" class="ve-ce-mwReferencesListNode-missingRef"><span rel="mw:referencedBy"></span> <span class="ve-ce-mwReferencesListNode-muted">cite-ve-referenceslist-missing-parent</span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

// Wikitext:
// <ref details="page 1" name="book">Miller</ref>
// <ref details="page 2" name="book">Miller</ref>
ve.dm.ConverterIntegrationTestCases.mainPlusDetailsWithDuplicateMainContent = {
	data:
		[
			{
				type: 'paragraph',
				internal: {
					whitespace: [
						undefined,
						undefined,
						undefined,
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							details: 'page 1'
						},
						body: {
							id: 'mw-reference-text-cite_note-2'
						},
						mainRef: 'book',
						mainBody: 'mw-reference-text-cite_note-book-1',
						isSubRefWithMainBody: 1
					},
					originalMw: '{"name":"ref","attrs":{"details":"page 1"},"body":{"id":"mw-reference-text-cite_note-2"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}',
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'auto/0',
					refGroup: '',
					contentsUsed: true,
					mainRefKey: 'literal/book',
					refListItemId: 'mw-reference-text-cite_note-2'
				}
			},
			{
				type: '/mwReference'
			},
			'\n',
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							details: 'page 2'
						},
						body: {
							id: 'mw-reference-text-cite_note-3'
						},
						mainRef: 'book',
						mainBody: 'mw-reference-text-cite_note-book-1',
						isSubRefWithMainBody: 1
					},
					originalMw: '{"name":"ref","attrs":{"details":"page 2"},"body":{"id":"mw-reference-text-cite_note-3"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1}',
					listIndex: 1,
					listGroup: 'mwReference/',
					listKey: 'auto/1',
					refGroup: '',
					contentsUsed: true,
					mainRefKey: 'literal/book',
					refListItemId: 'mw-reference-text-cite_note-3'
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: {},
						autoGenerated: true,
						body: {
							html: "\n<sup typeof=\"mw:Extension/ref\" data-parsoid=\"{}\" data-mw='{\"name\":\"ref\",\"attrs\":{\"name\":\"book\",\"group\":\"\"},\"body\":{\"id\":\"mw-reference-text-cite_note-book-1\"},\"isSyntheticMainRef\":1}'>Miller</sup>"
						}
					},
					originalMw: "{\"name\":\"references\",\"attrs\":{},\"autoGenerated\":true,\"body\":{\"html\":\"\\n<sup typeof=\\\"mw:Extension/ref\\\" data-parsoid=\\\"{}\\\" data-mw='{\\\"name\\\":\\\"ref\\\",\\\"attrs\\\":{\\\"name\\\":\\\"book\\\",\\\"group\\\":\\\"\\\"},\\\"body\\\":{\\\"id\\\":\\\"mw-reference-text-cite_note-book-1\\\"},\\\"isSyntheticMainRef\\\":1}'>Miller</sup>\"}}",
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: false
				},
				internal: {
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper',
					whitespace: [
						'\n'
					]
				}
			},
			{
				type: 'mwReference',
				attributes: {
					mw: {
						name: 'ref',
						attrs: {
							name: 'book',
							group: ''
						},
						body: {
							id: 'mw-reference-text-cite_note-book-1'
						},
						isSyntheticMainRef: 1
					},
					originalMw: '{"name":"ref","attrs":{"name":"book","group":""},"body":{"id":"mw-reference-text-cite_note-book-1"},"isSyntheticMainRef":1}',
					listIndex: 2,
					listGroup: 'mwReference/',
					listKey: 'literal/book',
					refGroup: '',
					contentsUsed: true,
					refListItemId: 'mw-reference-text-cite_note-book-1'
				}
			},
			{
				type: '/mwReference'
			},
			{
				type: '/paragraph'
			},
			{
				type: '/mwReferencesList'
			},
			{
				type: 'internalList'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'page 1'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'p',
			'a',
			'g',
			'e',
			' ',
			'1',
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'page 2'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'p',
			'a',
			'g',
			'e',
			' ',
			'2',
			{
				type: '/paragraph'
			},
			{
				type: '/internalItem'
			},
			{
				type: 'internalItem',
				attributes: {
					originalHtml: 'Miller'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'M',
			'i',
			'l',
			'l',
			'e',
			'r',
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
	body:
		"<p id=\"mwAg\"><sup about=\"#mwt1\" class=\"mw-ref reference\" id=\"cite_ref-2\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}\"><a href=\"./MainPlusDetails#cite_note-2\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1.1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup>\n<sup about=\"#mwt2\" class=\"mw-ref reference\" id=\"cite_ref-3\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}\"><a href=\"./MainPlusDetails#cite_note-3\" id=\"mwBw\"><span class=\"mw-reflink-text\" id=\"mwCA\"><span class=\"cite-bracket\" id=\"mwCQ\">[</span>1.2<span class=\"cite-bracket\" id=\"mwCg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt3\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid=\\&quot;{}\\&quot; data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;book\\&quot;,\\&quot;group\\&quot;:\\&quot;\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-book-1\\&quot;},\\&quot;isSyntheticMainRef\\&quot;:1}'&gt;Miller&lt;/sup&gt;&quot;}}\" id=\"mwCw\"><ol class=\"mw-references references\" id=\"mwDA\"><li about=\"#cite_note-book-1\" id=\"cite_note-book-1\"><span rel=\"mw:referencedBy\" class=\"mw-cite-backlink\" id=\"mwDQ\"></span> <span id=\"mw-reference-text-cite_note-book-1\" class=\"mw-reference-text reference-text\">Miller</span><ol class=\"mw-subreference-list\" id=\"mwDg\"><li about=\"#cite_note-2\" id=\"cite_note-2\"><span class=\"mw-cite-backlink\" id=\"mwDw\"><a href=\"./MainPlusDetails#cite_ref-2\" rel=\"mw:referencedBy\" id=\"mwEA\"><span class=\"mw-linkback-text\" id=\"mwEQ\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-2\" class=\"mw-reference-text reference-text\">page 1</span></li>\n<li about=\"#cite_note-3\" id=\"cite_note-3\"><span class=\"mw-cite-backlink\" id=\"mwEg\"><a href=\"./MainPlusDetails#cite_ref-3\" rel=\"mw:referencedBy\" id=\"mwEw\"><span class=\"mw-linkback-text\" id=\"mwFA\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-3\" class=\"mw-reference-text reference-text\">page 2</span></li>\n</ol></li>\n</ol></div>"
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Main plus details with duplicate main content' ] = {
	data: ve.dm.ConverterIntegrationTestCases.mainPlusDetailsWithDuplicateMainContent.data,
	body: ve.dm.ConverterIntegrationTestCases.mainPlusDetailsWithDuplicateMainContent.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-3">page 2</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt2" id="cite_ref-3" rel="dc:references"><a href="./MainPlusDetails#cite_note-3" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1.2<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-3">page 2</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;,&quot;html&quot;:&quot;page 2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li><li style="--footnote-number: &quot;1.2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 2</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li><li style="--footnote-number: &quot;1.2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 2</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Main plus details with duplicate main content ( edit main )' ] = {
	data: ve.dm.ConverterIntegrationTestCases.mainPlusDetailsWithDuplicateMainContent.data,
	body: ve.dm.ConverterIntegrationTestCases.mainPlusDetailsWithDuplicateMainContent.body,
	modify:
		( model ) => {
			model.commit( ve.dm.Transaction.static.deserialize( [ 35, [ [ { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, 'M', 'i', 'l', 'l', 'e', 'r', { type: '/paragraph' } ], '' ], 2 ] ) );
			model.commit( ve.dm.Transaction.static.deserialize( [ 14, [ [ { type: 'internalItem', attributes: { originalHtml: 'page 1' } }, { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, 'p', 'a', 'g', 'e', ' ', '1', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'page 2' } }, { type: 'paragraph', internal: { generated: 'wrapper', metaItems: [] } }, 'p', 'a', 'g', 'e', ' ', '2', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'Miller' } }, { type: '/internalItem' } ], [ { type: 'internalItem', attributes: { originalHtml: 'page 1' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'p', 'a', 'g', 'e', ' ', '1', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'page 2' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'p', 'a', 'g', 'e', ' ', '2', { type: '/paragraph' }, { type: '/internalItem' }, { type: 'internalItem', attributes: { originalHtml: 'Miller' } }, { type: 'paragraph', internal: { generated: 'wrapper' } }, 'M', 'i', 'l', 'l', 'e', 'r', ' ', 'N', 'E', 'W', { type: '/paragraph' }, { type: '/internalItem' } ] ], 1 ] ) );
		},
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller NEW</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-3">page 2</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference" about="#mwt2" id="cite_ref-3" rel="dc:references"><a href="./MainPlusDetails#cite_note-3" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>1.2<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller NEW</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-3">page 2</span></li></ol></li></ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page 1&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>\n<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;,&quot;html&quot;:&quot;page 2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller NEW</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li><li style="--footnote-number: &quot;1.2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 2</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 1&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>↵<sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page 2&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-3&quot;},&quot;mainRef&quot;:&quot;book&quot;,&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.2<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller NEW</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li><li style="--footnote-number: &quot;1.2.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 2</p></span></div></span></li></ol></li></ol></div>',
	innerWhitespace:
		[
			undefined,
			undefined
		],
	preserveAnnotationDomElements:
		true
};
