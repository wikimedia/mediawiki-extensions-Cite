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
					mainBody: 'mw-reference-text-cite_note-book-1',
					isSubRefWithMainBody: 1,
					mainRef: 'book'
				},
				originalMw: '{"name":"ref","attrs":{"details":"page. 123"},"body":{"id":"mw-reference-text-cite_note-2"},"mainBody":"mw-reference-text-cite_note-book-1","isSubRefWithMainBody":1,"mainRef":"book"}',
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
		"<p id=\"mwAg\"><sup about=\"#mwt1\" class=\"mw-ref reference\" id=\"cite_ref-2\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}\"><a href=\"./Example/MainPlusDetails#cite_note-2\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1.1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt2\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid=\\&quot;{}\\&quot; data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;book\\&quot;,\\&quot;group\\&quot;:\\&quot;\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-book-1\\&quot;},\\&quot;isSyntheticMainRef\\&quot;:1}'&gt;Miller&lt;/sup&gt;&quot;}}\" id=\"mwBw\"><ol class=\"mw-references references\" id=\"mwCA\"><li about=\"#cite_note-book-1\" id=\"cite_note-book-1\"><span rel=\"mw:referencedBy\" class=\"mw-cite-backlink\" id=\"mwCQ\"></span> <span id=\"mw-reference-text-cite_note-book-1\" class=\"mw-reference-text reference-text\">Miller</span><ol class=\"mw-subreference-list\" id=\"mwCg\"><li about=\"#cite_note-2\" id=\"cite_note-2\"><span class=\"mw-cite-backlink\" id=\"mwCw\"><a href=\"./Example/MainPlusDetails#cite_ref-2\" rel=\"mw:referencedBy\" id=\"mwDA\"><span class=\"mw-linkback-text\" id=\"mwDQ\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-2\" class=\"mw-reference-text reference-text\">page. 123</span></li>\n</ol></li>\n</ol></div>"
};

ve.dm.ConverterIntegrationTestCases.cases[ 'Simple main ref including details' ] = {
	data: ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails.data,
	body: ve.dm.ConverterIntegrationTestCases.simpleMainPlusDetails.body,
	fromDataBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page. 123</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt2" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}" id="mwBw"><ol class="mw-references references" id="mwCA"><li about="#cite_note-book-1" id="cite_note-book-1"><span rel="mw:referencedBy" class="mw-cite-backlink" id="mwCQ"></span> <span id="mw-reference-text-cite_note-book-1" class="mw-reference-text reference-text">Miller</span><ol class="mw-subreference-list" id="mwCg"><li about="#cite_note-2" id="cite_note-2"><span class="mw-cite-backlink" id="mwCw"><a href="./Example/MainPlusDetails#cite_ref-2" rel="mw:referencedBy" id="mwDA"><span class="mw-linkback-text" id="mwDQ">↑ </span></a></span> <span id="mw-reference-text-cite_note-2" class="mw-reference-text reference-text">page. 123</span></li>\n</ol></li>\n</ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123</p></span></div></span></li></ol></li></ol></div>',
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
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123 NEW&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-book-1">Miller NEW</span><ol><li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page. 123 NEW</span></li></ol></li></ol></div>',
	normalizedBody:
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123 NEW&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference" about="#mwt1" id="cite_ref-2" rel="dc:references"><a href="./Example/MainPlusDetails#cite_note-2" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1.1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt2" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}" id="mwBw"><ol class="mw-references references" id="mwCA"><li about="#cite_note-book-1" id="cite_note-book-1"><span rel="mw:referencedBy" class="mw-cite-backlink" id="mwCQ"></span> <span id="mw-reference-text-cite_note-book-1" class="mw-reference-text reference-text">Miller</span><ol class="mw-subreference-list" id="mwCg"><li about="#cite_note-2" id="cite_note-2"><span class="mw-cite-backlink" id="mwCw"><a href="./Example/MainPlusDetails#cite_ref-2" rel="mw:referencedBy" id="mwDA"><span class="mw-linkback-text" id="mwDQ">↑ </span></a></span> <span id="mw-reference-text-cite_note-2" class="mw-reference-text reference-text">page. 123</span></li>\n</ol></li>\n</ol></div>',
	clipboardBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;,&quot;name&quot;:&quot;book&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;,&quot;html&quot;:&quot;page. 123 NEW&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;Miller NEW&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller NEW</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123 NEW</p></span></div></span></li></ol></li></ol></div>',
	previewBody:
		'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;details&quot;:&quot;page. 123&quot;},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-2&quot;},&quot;mainBody&quot;:&quot;mw-reference-text-cite_note-book-1&quot;,&quot;isSubRefWithMainBody&quot;:1,&quot;mainRef&quot;:&quot;book&quot;}" class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true,&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;book&amp;quot;,&amp;quot;group&amp;quot;:&amp;quot;&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-book-1&amp;quot;},&amp;quot;isSyntheticMainRef&amp;quot;:1}\\&quot; class=\\&quot;mw-ref reference\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><span rel="mw:referencedBy"></span> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Miller NEW</p></span></div></span><ol><li style="--footnote-number: &quot;1.1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page. 123 NEW</p></span></div></span></li></ol></li></ol></div>'
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
		'<p id="mwAg"><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;&lt;a typeof=\\&quot;mw:Transclusion\\&quot; data-mw=\\&quot;{&amp;quot;parts&amp;quot;:[{&amp;quot;template&amp;quot;:{&amp;quot;target&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Cite&amp;quot;,&amp;quot;href&amp;quot;:&amp;quot;./Template:Cite&amp;quot;},&amp;quot;params&amp;quot;:{&amp;quot;author&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Miller&amp;quot;},&amp;quot;title&amp;quot;:{&amp;quot;wt&amp;quot;:&amp;quot;Foo New&amp;quot;}},&amp;quot;i&amp;quot;:0}}]}\\&quot; id=\\&quot;mwEA\\&quot;&gt;&lt;/a&gt;&quot;}}" class="mw-ref reference" about="#mwt2" id="cite_ref-1" rel="dc:references"><a href="./Example/CiteTemplates#cite_note-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>\n<sup about="#mwt3" class="mw-ref reference" id="cite_ref-ldrTpl_2-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldrTpl&quot;}}"><a href="./Example/CiteTemplates#cite_note-ldrTpl-2" id="mwBw"><span class="mw-reflink-text" id="mwCA"><span class="cite-bracket" id="mwCQ">[</span>2<span class="cite-bracket" id="mwCg">]</span></span></a></sup></p>\n<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt6" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldrTpl&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldrTpl-2&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;&amp;lt;a typeof=\\\\&amp;quot;mw:Transclusion\\\\&amp;quot; data-mw=\\\\&amp;quot;{&amp;amp;quot;parts&amp;amp;quot;:[{&amp;amp;quot;template&amp;amp;quot;:{&amp;amp;quot;target&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Cite&amp;amp;quot;,&amp;amp;quot;href&amp;amp;quot;:&amp;amp;quot;./Template:Cite&amp;amp;quot;},&amp;amp;quot;params&amp;amp;quot;:{&amp;amp;quot;author&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Smith&amp;amp;quot;},&amp;amp;quot;title&amp;amp;quot;:{&amp;amp;quot;wt&amp;amp;quot;:&amp;amp;quot;Bar New&amp;amp;quot;}},&amp;amp;quot;i&amp;amp;quot;:0}}]}\\\\&amp;quot; id=\\\\&amp;quot;mwFA\\\\&amp;quot;&amp;gt;&amp;lt;/a&amp;gt;&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot; about=\\&quot;#mwt5\\&quot; rel=\\&quot;dc:references\\&quot; data-parsoid=\\&quot;{&amp;quot;dsr&amp;quot;:[79,135,19,6]}\\&quot;&gt;&lt;a href=\\&quot;./Example/CiteTemplates#cite_note-ldrTpl-2\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;[&lt;/span&gt;2&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}" id="mwCw"><ol class="mw-references references" id="mwDA"><li about="#cite_note-1" id="cite_note-1"><span class="mw-cite-backlink" id="mwDQ"><a href="./Example/CiteTemplates#cite_ref-1" rel="mw:referencedBy" id="mwDg"><span class="mw-linkback-text" id="mwDw">↑ </span></a></span> <span id="mw-reference-text-cite_note-1" class="mw-reference-text reference-text"><a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt1" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Miller&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Foo&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwEA">Template:Cite</a></span></li>\n<li about="#cite_note-ldrTpl-2" id="cite_note-ldrTpl-2"><span class="mw-cite-backlink" id="mwEQ"><a href="./Example/CiteTemplates#cite_ref-ldrTpl_2-0" rel="mw:referencedBy" id="mwEg"><span class="mw-linkback-text" id="mwEw">↑ </span></a></span> <span id="mw-reference-text-cite_note-ldrTpl-2" class="mw-reference-text reference-text"><a rel="mw:WikiLink" href="./Template:Cite?action=edit&amp;redlink=1" title="Template:Cite" about="#mwt4" typeof="mw:Transclusion mw:LocalizedAttrs" class="new" data-mw="{&quot;parts&quot;:[{&quot;template&quot;:{&quot;target&quot;:{&quot;wt&quot;:&quot;Cite&quot;,&quot;href&quot;:&quot;./Template:Cite&quot;},&quot;params&quot;:{&quot;author&quot;:{&quot;wt&quot;:&quot;Smith&quot;},&quot;title&quot;:{&quot;wt&quot;:&quot;Bar&quot;}},&quot;i&quot;:0}}]}" data-mw-i18n="{&quot;title&quot;:{&quot;lang&quot;:&quot;x-page&quot;,&quot;key&quot;:&quot;red-link-title&quot;,&quot;params&quot;:[&quot;Template:Cite&quot;]}}" id="mwFA">Template:Cite</a></span></li>\n</ol></div>',
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
