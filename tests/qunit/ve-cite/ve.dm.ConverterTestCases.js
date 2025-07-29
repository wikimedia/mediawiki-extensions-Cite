'use strict';

/*!
 * VisualEditor Cite specific test cases for the Converter.
 *
 * @copyright 2011-2025 VisualEditor Team's Cite sub-team and others; see AUTHORS.txt
 * @license MIT
 */

ve.dm.ConverterTestCases = {};

ve.dm.ConverterTestCases.refListItemClipboard = function ( text ) {
	return ve.dm.example.singleLine`
		<span class="reference-text">
		<div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output">
			<span class="ve-ce-branchNode ve-ce-internalItemNode">
				<p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">
					${ text }
				</p>
			</span>
		</div>
		</span>
	`;
};

ve.dm.ConverterTestCases.cases = {
	'mw:Reference': {
		// Wikitext:
		// Foo<ref name="bar" /> Baz<ref group="g1" name=":0">Quux</ref> Whee<ref name="bar">[[Bar]]
		// </ref> Yay<ref group="g1">No name</ref> Quux<ref name="bar">Different content</ref> Foo
		// <ref group="g1" name="foo" />
		//
		// <references group="g1"><ref group="g1" name="foo">Ref in refs</ref></references>
		body: ve.dm.example.singleLine`
			<p>
				Foo
				<sup about="#mwt1" class="mw-ref reference" data-mw='{"name":"ref","attrs":{"name":"bar"}}' id="cite_ref-bar-1-0" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-bar-1"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></a>
				</sup>
				 Baz
				<sup about="#mwt2" class="mw-ref reference" data-mw='{"name":"ref","body":{"html":"Quux"},"attrs":{"group":"g1","name":":0"}}' id="cite_ref-quux-2-0" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-.3A0-2"><span class="cite-bracket">[</span>g1 1<span class="cite-bracket">]</span></a>
				</sup>
				 Whee
				<sup about="#mwt3" class="mw-ref reference" data-mw='{"name":"ref","body":{"html":"
				<a rel=\\"mw:WikiLink\\" href=\\"./Bar\\">Bar
				</a>"},"attrs":{"name":"bar"}}' id="cite_ref-bar-1-1" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-bar-1"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></a>
				</sup>
				 Yay
				${ /* This reference has .body.id instead of .body.html */'' }
				<sup about="#mwt4" class="mw-ref reference" data-mw='{"name":"ref","body":{"id":"mw-cite-3"},"attrs":{"group":"g1"}}' id="cite_ref-1-0" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-3"><span class="cite-bracket">[</span>g1 2<span class="cite-bracket">]</span></a>
				</sup>
				 Quux
				<sup about="#mwt5" class="mw-ref reference" data-mw='{"name":"ref","body":{"html":"Different content"},"attrs":{"name":"bar"}}' id="cite_ref-bar-1-2" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_note-bar-1"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></a>
				</sup>
				 Foo
				<sup about="#mwt6" class="mw-ref reference" data-mw='{"name":"ref","attrs":{"group":"g1","name":"foo"}}'
					 id="cite_ref-foo-4" rel="dc:references" typeof="mw:Extension/ref">
					<a href="#cite_ref-foo-4"><span class="cite-bracket">[</span>g1 3<span class="cite-bracket">]</span></a>
				</sup>
			</p>
			${ /* The HTML below is enriched to wrap reference contents in <span id="mw-cite-[...]"> */'' }
			${ /* which Parsoid doesn't do yet, but T88290 asks for */'' }
			<ol class="references" typeof="mw:Extension/references" about="#mwt7"
					data-mw='{"name":"references","body":{
					"html":"<sup about=\\"#mwt8\\" class=\\"mw-ref reference\\"
					 data-mw=&apos;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;body&amp;quot;:{&amp;quot;html&amp;quot;:&amp;quot;Ref in refs&amp;quot;},
					&amp;quot;attrs&amp;quot;:{&amp;quot;group&amp;quot;:&amp;quot;g1&amp;quot;,&amp;quot;name&amp;quot;:&amp;quot;foo&amp;quot;}}&apos;
					 rel=\\"dc:references\\" typeof=\\"mw:Extension/ref\\">
					<a href=\\"#cite_note-foo-3\\">[3]</a></sup>"},"attrs":{"group":"g1"}}'>
					<li about="#cite_note-.3A0-2" id="cite_note-.3A0-2"><span rel="mw:referencedBy"><a href="#cite_ref-.3A0_2-0">↑</a></span> <span id="mw-cite-:0">Quux</span></li>
					<li about="#cite_note-3" id="cite_note-3"><span rel="mw:referencedBy"><a href="#cite_ref-3">↑</a></span> <span id="mw-cite-3">No name</span></li>
					<li about="#cite_note-foo-4" id="cite_note-foo-4"><span rel="mw:referencedBy"><a href="#cite_ref-foo_4-0">↑</a></span> <span id="mw-cite-foo">Ref in refs</span></li>
			</ol>
		`,
		fromDataBody: ve.dm.example.singleLine`
			<p>
				Foo
				<sup data-mw='{"name":"ref","attrs":{"name":"bar"}}' typeof="mw:Extension/ref">
				</sup>
				 Baz
				<sup data-mw='{"name":"ref","body":{"html":"Quux"},"attrs":{"group":"g1","name":":0"}}' typeof="mw:Extension/ref">
				</sup>
				 Whee
				<sup data-mw='{"name":"ref","body":{"html":"
				<a rel=\\"mw:WikiLink\\" href=\\"./Bar\\">Bar
				</a>"},"attrs":{"name":"bar"}}' typeof="mw:Extension/ref">
				</sup>
				 Yay
				<sup data-mw='{"name":"ref","body":{"id":"mw-cite-3"},"attrs":{"group":"g1"}}' typeof="mw:Extension/ref">
				</sup>
				 Quux
				<sup data-mw='{"name":"ref","body":{"html":"Different content"},"attrs":{"name":"bar"}}' typeof="mw:Extension/ref">
				</sup>
				 Foo
				<sup data-mw='{"name":"ref","attrs":{"group":"g1","name":"foo"}}'
					 typeof="mw:Extension/ref">
				</sup>
			</p>
			<div typeof="mw:Extension/references"
				 data-mw='{"name":"references","attrs":{"group":"g1"},"body":{
				"html":"<sup typeof=\\"mw:Extension/ref\\"
				 data-mw=&apos;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;body&amp;quot;:{&amp;quot;html&amp;quot;:&amp;quot;Ref in refs&amp;quot;},
				&amp;quot;attrs&amp;quot;:{&amp;quot;group&amp;quot;:&amp;quot;g1&amp;quot;,&amp;quot;name&amp;quot;:&amp;quot;foo&amp;quot;}}&apos;>
				</sup>"}}'>
				<ol>
					<li><span typeof="mw:Extension/ref">Quux</span></li>
					<li><span typeof="mw:Extension/ref" id="mw-cite-3">No name</span></li>
					<li><span typeof="mw:Extension/ref">Ref in refs</span></li>
				</ol>
			</div>
		`,
		clipboardBody: ve.dm.example.singleLine`
			<p>
				Foo
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"name":"bar"}}' class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
				 Baz
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"Quux"},"attrs":{"group":"g1","name":":0"}}' class="mw-ref reference">
					<a data-mw-group="g1"><span class="mw-reflink-text"><span class="cite-bracket">[</span>g1 1<span class="cite-bracket">]</span></span></a>
				</sup>
				 Whee
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"
				<a href=\\"./Bar\\" rel=\\"mw:WikiLink\\">Bar
				</a>"},"attrs":{"name":"bar"}}' class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
				 Yay
				${ /* This reference has .body.id instead of .body.html */'' }
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"id":"mw-cite-3","html":"No name"},"attrs":{"group":"g1"}}' class="mw-ref reference">
					<a data-mw-group="g1"><span class="mw-reflink-text"><span class="cite-bracket">[</span>g1 2<span class="cite-bracket">]</span></span></a>
				</sup>
				 Quux
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"Different content"},"attrs":{"name":"bar"}}' class="mw-ref reference">
					<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
				</sup>
				 Foo
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"group":"g1","name":"foo"}}' class="mw-ref reference">
					<a data-mw-group="g1"><span class="mw-reflink-text"><span class="cite-bracket">[</span>g1 3<span class="cite-bracket">]</span></span></a>
				</sup>
			</p>
			${ /* The HTML below is enriched to wrap reference contents in <span id="mw-cite-[...]"> */'' }
			${ /* which Parsoid doesn't do yet, but T88290 asks for */'' }
			<div typeof="mw:Extension/references"
				 data-mw='{"name":"references","attrs":{"group":"g1"},"body":{
				"html":"<sup typeof=\\"mw:Extension/ref\\"
				 data-mw=&apos;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;group&amp;quot;:&amp;quot;g1&amp;quot;,&amp;quot;name&amp;quot;:&amp;quot;foo&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;html&amp;quot;:&amp;quot;Ref in refs&amp;quot;}}
				&apos; class=\\"mw-ref reference\\"><a data-mw-group=\\"g1\\"><span class=\\"mw-reflink-text\\"><span class=\\"cite-bracket\\">[</span>g1 3<span class=\\"cite-bracket\\">]</span></span></a></sup>"}}'>
					<ol class="mw-references references" data-mw-group="g1">
						<li style='--footnote-number: "1.";'>
							<a rel="mw:referencedBy" data-mw-group="g1"><span class="mw-linkback-text">↑ </span></a>
								 ${ ve.dm.ConverterTestCases.refListItemClipboard( 'Quux' ) }
						</li>
						<li style='--footnote-number: "2.";'>
							<a rel="mw:referencedBy" data-mw-group="g1"><span class="mw-linkback-text">↑ </span></a>
								 ${ ve.dm.ConverterTestCases.refListItemClipboard( 'No name' ) }
						</li>
						<li style='--footnote-number: "3.";'>
							<a rel="mw:referencedBy" data-mw-group="g1"><span class="mw-linkback-text">↑ </span></a>
								 ${ ve.dm.ConverterTestCases.refListItemClipboard( 'Ref in refs' ) }
						</li>
					</ol>
			</div>
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
					mw: { name: 'ref', attrs: { name: 'bar' } },
					originalMw: '{"name":"ref","attrs":{"name":"bar"}}',
					contentsUsed: false
				}
			},
			{ type: '/mwReference' },
			' ', 'B', 'a', 'z',
			{
				type: 'mwReference',
				attributes: {
					listIndex: 1,
					listGroup: 'mwReference/g1',
					listKey: 'literal/:0',
					refGroup: 'g1',
					mw: { name: 'ref', body: { html: 'Quux' }, attrs: { group: 'g1', name: ':0' } },
					originalMw: '{"name":"ref","body":{"html":"Quux"},"attrs":{"group":"g1","name":":0"}}',
					contentsUsed: true
				}
			},
			{ type: '/mwReference' },
			' ', 'W', 'h', 'e', 'e',
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
			' ', 'Y', 'a', 'y',
			{
				type: 'mwReference',
				attributes: {
					listIndex: 2,
					listGroup: 'mwReference/g1',
					listKey: 'auto/0',
					refGroup: 'g1',
					mw: { name: 'ref', body: { id: 'mw-cite-3' }, attrs: { group: 'g1' } },
					originalMw: '{"name":"ref","body":{"id":"mw-cite-3"},"attrs":{"group":"g1"}}',
					contentsUsed: true,
					refListItemId: 'mw-cite-3'
				}
			},
			{ type: '/mwReference' },
			' ', 'Q', 'u', 'u', 'x',
			{
				type: 'mwReference',
				attributes: {
					listIndex: 0,
					listGroup: 'mwReference/',
					listKey: 'literal/bar',
					refGroup: '',
					mw: { name: 'ref', body: { html: 'Different content' }, attrs: { name: 'bar' } },
					originalMw: '{"name":"ref","body":{"html":"Different content"},"attrs":{"name":"bar"}}',
					contentsUsed: false
				}
			},
			{ type: '/mwReference' },
			' ', 'F', 'o', 'o',
			{
				type: 'mwReference',
				attributes: {
					listGroup: 'mwReference/g1',
					listIndex: 3,
					listKey: 'literal/foo',
					refGroup: 'g1',
					mw: { name: 'ref', attrs: { group: 'g1', name: 'foo' } },
					originalMw: '{"name":"ref","attrs":{"group":"g1","name":"foo"}}',
					contentsUsed: false
				}
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: { group: 'g1' },
						body: {
							html: ve.dm.example.singleLine`
								<sup about="#mwt8" class="mw-ref reference" data-mw='{&quot;name&quot;:&quot;ref&quot;,&quot;body&quot;:{&quot;html&quot;:&quot;Ref in refs&quot;},&quot;attrs&quot;:{&quot;group&quot;:&quot;g1&quot;,&quot;name&quot;:&quot;foo&quot;}}' rel="dc:references" typeof="mw:Extension/ref">
									<a href="#cite_note-foo-3">[3]</a>
								</sup>
							`
						}
					},
					originalMw: '{"name":"references","body":{"html":"<sup about=\\"#mwt8\\" class=\\"mw-ref reference\\" data-mw=\'{&quot;name&quot;:&quot;ref&quot;,&quot;body&quot;:{&quot;html&quot;:&quot;Ref in refs&quot;},&quot;attrs&quot;:{&quot;group&quot;:&quot;g1&quot;,&quot;name&quot;:&quot;foo&quot;}}\' rel=\\"dc:references\\" typeof=\\"mw:Extension/ref\\"><a href=\\"#cite_note-foo-3\\">[3]</a></sup>"},"attrs":{"group":"g1"}}',
					listGroup: 'mwReference/g1',
					refGroup: 'g1',
					isResponsive: true,
					templateGenerated: false
				}
			},
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			{
				type: 'mwReference',
				attributes: {
					contentsUsed: true,
					listGroup: 'mwReference/g1',
					listIndex: 3,
					listKey: 'literal/foo',
					mw: { name: 'ref', attrs: { group: 'g1', name: 'foo' }, body: { html: 'Ref in refs' } },
					originalMw: '{"name":"ref","body":{"html":"Ref in refs"},"attrs":{"group":"g1","name":"foo"}}',
					refGroup: 'g1'
				}
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
			{ type: '/mwReferencesList' },
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
			{ type: 'internalItem', attributes: { originalHtml: 'Quux' } },
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			'Q', 'u', 'u', 'x',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: 'internalItem', attributes: { originalHtml: 'No name' } },
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			'N', 'o', ' ', 'n', 'a', 'm', 'e',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: 'internalItem', attributes: { originalHtml: 'Ref in refs' } },
			{ type: 'paragraph', internal: { generated: 'wrapper' } },
			'R', 'e', 'f', ' ', 'i', 'n', ' ', 'r', 'e', 'f', 's',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: '/internalList' }
		]
	},
	'Template generated reflist': {
		body: ve.dm.example.singleLine`
			<p><sup about="#mwt2" class="mw-ref reference" id="cite_ref-1" rel="dc:references" typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{"group":"notes"}}'><a href="./Main_Page#cite_note-1" data-mw-group="notes"><span class="mw-reflink-text"><span class="cite-bracket">[</span>notes 1<span class="cite-bracket">]</span></span></a></sup></p>
			<div class="mw-references-wrap" typeof="mw:Extension/references mw:Transclusion" about="#mwt4" data-mw='{"parts":[{"template":{"target":{"wt":"echo","href":"./Template:Echo"},"params":{"1":{"wt":"<references group=\\"notes\\" />"}},"i":0}}]}'>
				<ol class="mw-references references" data-mw-group="notes">
					<li about="#cite_note-1" id="cite_note-1"><a href="./Main_Page#cite_ref-1" data-mw-group="notes" rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span id="mw-reference-text-cite_note-1" class="mw-reference-text">Foo</span></li>
				</ol>
			</div>
		`,
		fromDataBody: ve.dm.example.singleLine`
			<p><sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{"group":"notes"}}'></sup></p>
			<span typeof="mw:Transclusion" data-mw='{"parts":[{"template":{"target":{"wt":"echo","href":"./Template:Echo"},"params":{"1":{"wt":"<references group=\\"notes\\" />"}},"i":0}}]}'></span>
		`,
		clipboardBody: ve.dm.example.singleLine`
			<p><sup typeof="mw:Extension/ref" data-mw='{"attrs":{"group":"notes"},"body":{"id":"mw-reference-text-cite_note-1","html":"Foo"},"name":"ref"}' class="mw-ref reference"><a data-mw-group="notes"><span class="mw-reflink-text"><span class="cite-bracket">[</span>notes 1<span class="cite-bracket">]</span></span></a></sup></p>
			<div typeof="mw:Extension/references" data-mw='{"parts":[{"template":{"params":{"1":{"wt":"<references group=\\"notes\\" />"}},"target":{"wt":"echo","href":"./Template:Echo"},"i":0}}],"name":"references"}'>
				${ /* TODO: This should list should get populated on copy */'' }
				<ol class="mw-references references"></ol>
			</div>
		`,
		previewBody: false,
		data: [
			{ type: 'paragraph' },
			{
				type: 'mwReference',
				attributes: {
					contentsUsed: true,
					listGroup: 'mwReference/notes',
					listIndex: 0,
					listKey: 'auto/0',
					mw: {
						attrs: {
							group: 'notes'
						},
						body: {
							id: 'mw-reference-text-cite_note-1'
						},
						name: 'ref'
					},
					originalMw: '{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{"group":"notes"}}',
					refGroup: 'notes',
					refListItemId: 'mw-reference-text-cite_note-1'
				}
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						parts: [ {
							template: {
								params: {
									1: { wt: '<references group="notes" />' }
								},
								target: { wt: 'echo', href: './Template:Echo' },
								i: 0
							}
						} ]
					},
					originalMw: '{"parts":[{"template":{"target":{"wt":"echo","href":"./Template:Echo"},"params":{"1":{"wt":"<references group=\\"notes\\" />"}},"i":0}}]}',
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: true
				}
			},
			{ type: '/mwReferencesList' },
			{ type: 'internalList' },
			{ type: 'internalItem', attributes: { originalHtml: 'Foo' } },
			{
				internal: {
					generated: 'wrapper'
				},
				type: 'paragraph'
			},
			'F', 'o', 'o',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: '/internalList' }
		]
	},
	'Simple reference with stored original content': {
		// T400052
		// Given the following wikitext:
		// <ref>Foo</ref>
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
				},
				originalDomElementsHash: 'hca9b910d9d8a6fd3'
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
				originalDomElementsHash: 'h6fe86bb317c3b124',
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
					originalHtml: 'Foo'
				}
			},
			{
				type: 'paragraph',
				internal: {
					generated: 'wrapper'
				}
			},
			'F',
			'o',
			'o',
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
		modify: undefined,
		body: ve.dm.example.singleLine`<p><sup about="#mwt1" class="mw-ref reference" id="cite_ref-1" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}"><a href="./Test#cite_note-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>
<div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt2" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwBw"><ol class="mw-references references" id="mwCA"><li about="#cite_note-1" id="cite_note-1"><span class="mw-cite-backlink" id="mwCQ"><a href="./Test#cite_ref-1" rel="mw:referencedBy" id="mwCg"><span class="mw-linkback-text" id="mwCw">↑ </span></a></span> <span id="mw-reference-text-cite_note-1" class="mw-reference-text reference-text">Foo</span></li>
</ol></div>`,
		clipboardBody: ve.dm.example.singleLine`<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;,&quot;html&quot;:&quot;Foo&quot;}}" class="mw-ref reference" about="#mwt1" id="cite_ref-1" rel="dc:references"><a href="./Test#cite_note-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>
<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Foo</p></span></div></span></li></ol></div>`,
		previewBody: ve.dm.example.singleLine`<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}" class="mw-ref reference" about="#mwt1" id="cite_ref-1" rel="dc:references"><a href="./Test#cite_note-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>
<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">Foo</p></span></div></span></li></ol></div>`,
		storeItems: {
			// Footnote
			hca9b910d9d8a6fd3: $.parseHTML( ve.dm.example.singleLine`
                                <sup about="#mwt1" class="mw-ref reference" id="cite_ref-1" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;id&quot;:&quot;mw-reference-text-cite_note-1&quot;}}"><a href="./Test#cite_note-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>
                        ` ),
			// Reflist
			h6fe86bb317c3b124: $.parseHTML( ve.dm.example.singleLine`
                                <div class="mw-references-wrap" typeof="mw:Extension/references" about="#mwt2" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;autoGenerated&quot;:true}" id="mwBw"><ol class="mw-references references" id="mwCA"><li about="#cite_note-1" id="cite_note-1"><span class="mw-cite-backlink" id="mwCQ"><a href="./Test#cite_ref-1" rel="mw:referencedBy" id="mwCg"><span class="mw-linkback-text" id="mwCw">↑ </span></a></span> <span id="mw-reference-text-cite_note-1" class="mw-reference-text reference-text">Foo</span></li>
</ol></div>
                        ` )
		}
	},
	'Simple list defined reference without stored original mwReferencesList HTML': {
		// T400052
		// Given the following wikitext:
		// <ref name="ldr" />
		// <references>
		// <ref name="ldr">FooBar</ref>
		// </references>
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
								name: 'ldr'
							}
						},
						originalMw: '{"name":"ref","attrs":{"name":"ldr"}}',
						listIndex: 0,
						listGroup: 'mwReference/',
						listKey: 'literal/ldr',
						refGroup: '',
						contentsUsed: false
					},
					originalDomElementsHash: 'ha53e3e34e508260f'
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
								html: "\n<sup about=\"#mwt2\" class=\"mw-ref reference\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-parsoid='{\"dsr\":[32,60,16,6]}' data-mw='{\"name\":\"ref\",\"attrs\":{\"name\":\"ldr\"},\"body\":{\"id\":\"mw-reference-text-cite_note-ldr-1\"}}'><a href=\"./Example/CiteDetailsReferencesLoss#cite_note-ldr-1\" data-parsoid=\"{}\"><span class=\"mw-reflink-text\" data-parsoid=\"{}\"><span class=\"cite-bracket\" data-parsoid=\"{}\">[</span>1<span class=\"cite-bracket\" data-parsoid=\"{}\">]</span></span></a></sup>\n"
							}
						},
						originalMw: "{\"name\":\"references\",\"attrs\":{},\"body\":{\"html\":\"\\n<sup about=\\\"#mwt2\\\" class=\\\"mw-ref reference\\\" rel=\\\"dc:references\\\" typeof=\\\"mw:Extension/ref\\\" data-parsoid='{\\\"dsr\\\":[32,60,16,6]}' data-mw='{\\\"name\\\":\\\"ref\\\",\\\"attrs\\\":{\\\"name\\\":\\\"ldr\\\"},\\\"body\\\":{\\\"id\\\":\\\"mw-reference-text-cite_note-ldr-1\\\"}}'><a href=\\\"./Example/CiteDetailsReferencesLoss#cite_note-ldr-1\\\" data-parsoid=\\\"{}\\\"><span class=\\\"mw-reflink-text\\\" data-parsoid=\\\"{}\\\"><span class=\\\"cite-bracket\\\" data-parsoid=\\\"{}\\\">[</span>1<span class=\\\"cite-bracket\\\" data-parsoid=\\\"{}\\\">]</span></span></a></sup>\\n\"}}",
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
								name: 'ldr'
							},
							body: {
								id: 'mw-reference-text-cite_note-ldr-1'
							}
						},
						originalMw: '{"name":"ref","attrs":{"name":"ldr"},"body":{"id":"mw-reference-text-cite_note-ldr-1"}}',
						listIndex: 0,
						listGroup: 'mwReference/',
						listKey: 'literal/ldr',
						refGroup: '',
						contentsUsed: true,
						refListItemId: 'mw-reference-text-cite_note-ldr-1'
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
						originalHtml: 'FooBar'
					}
				},
				{
					type: 'paragraph',
					internal: {
						generated: 'wrapper'
					}
				},
				'F',
				'o',
				'o',
				'B',
				'a',
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
			ve.dm.example.singleLine`<p><sup about=\"#mwt1\" class=\"mw-ref reference\" id=\"cite_ref-ldr_1-0\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldr&quot;}}\"><a href=\"./Example/CiteDetailsReferencesLoss#cite_note-ldr-1\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup></p>\n<div class=\"mw-references-wrap\" typeof=\"mw:Extension/references\" about=\"#mwt3\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup about=\\&quot;#mwt2\\&quot; class=\\&quot;mw-ref reference\\&quot; rel=\\&quot;dc:references\\&quot; typeof=\\&quot;mw:Extension/ref\\&quot; data-parsoid='{\\&quot;dsr\\&quot;:[32,60,16,6]}' data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;ldr\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-ldr-1\\&quot;}}'&gt;&lt;a href=\\&quot;./Example/CiteDetailsReferencesLoss#cite_note-ldr-1\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot; data-parsoid=\\&quot;{}\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}\" id=\"mwBw\"><ol class=\"mw-references references\" id=\"mwCA\"><li about=\"#cite_note-ldr-1\" id=\"cite_note-ldr-1\"><span class=\"mw-cite-backlink\" id=\"mwCQ\"><a href=\"./Example/CiteDetailsReferencesLoss#cite_ref-ldr_1-0\" rel=\"mw:referencedBy\" id=\"mwCg\"><span class=\"mw-linkback-text\" id=\"mwCw\">↑ </span></a></span> <span id=\"mw-reference-text-cite_note-ldr-1\" class=\"mw-reference-text reference-text\">FooBar</span></li>\n</ol></div>`,
		fromDataBody:
			ve.dm.example.singleLine`<p>
				<sup about=\"#mwt1\" class=\"mw-ref reference\" id=\"cite_ref-ldr_1-0\" rel=\"dc:references\" typeof=\"mw:Extension/ref\" data-mw=\"{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldr&quot;}}\"><a href=\"./Example/CiteDetailsReferencesLoss#cite_note-ldr-1\" id=\"mwAw\"><span class=\"mw-reflink-text\" id=\"mwBA\"><span class=\"cite-bracket\" id=\"mwBQ\">[</span>1<span class=\"cite-bracket\" id=\"mwBg\">]</span></span></a></sup></p>\n
				<div typeof=\"mw:Extension/references\" data-mw=\"{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n<sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw='{\\&quot;name\\&quot;:\\&quot;ref\\&quot;,\\&quot;attrs\\&quot;:{\\&quot;name\\&quot;:\\&quot;ldr\\&quot;},\\&quot;body\\&quot;:{\\&quot;id\\&quot;:\\&quot;mw-reference-text-cite_note-ldr-1\\&quot;}}'&gt;&lt;/sup&gt;\\n&quot;}}\">
					<ol>
						<li><span id=\"mw-reference-text-cite_note-ldr-1\" typeof=\"mw:Extension/ref\">FooBar</span></li>
					</ol>
				</div>`,
		clipboardBody:
			'<p><sup typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldr&quot;}}" class="mw-ref reference" about="#mwt1" id="cite_ref-ldr_1-0" rel="dc:references"><a href="./Example/CiteDetailsReferencesLoss#cite_note-ldr-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup></p>\n<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;,&quot;attrs&quot;:{},&quot;body&quot;:{&quot;html&quot;:&quot;\\n&lt;sup typeof=\\&quot;mw:Extension/ref\\&quot; data-mw=\\&quot;{&amp;quot;name&amp;quot;:&amp;quot;ref&amp;quot;,&amp;quot;attrs&amp;quot;:{&amp;quot;name&amp;quot;:&amp;quot;ldr&amp;quot;},&amp;quot;body&amp;quot;:{&amp;quot;id&amp;quot;:&amp;quot;mw-reference-text-cite_note-ldr-1&amp;quot;,&amp;quot;html&amp;quot;:&amp;quot;FooBar&amp;quot;}}\\&quot; class=\\&quot;mw-ref reference\\&quot; about=\\&quot;#mwt2\\&quot;&gt;&lt;a&gt;&lt;span class=\\&quot;mw-reflink-text\\&quot;&gt;&lt;span class=\\&quot;cite-bracket\\&quot;&gt;[&lt;/span&gt;1&lt;span class=\\&quot;cite-bracket\\&quot;&gt;]&lt;/span&gt;&lt;/span&gt;&lt;/a&gt;&lt;/sup&gt;\\n&quot;}}"><ol class="mw-references references"><li style="--footnote-number: &quot;1.&quot;;"><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">FooBar</p></span></div></span></li></ol></div>',
		previewBody: false,
		innerWhitespace:
			[
				undefined,
				undefined
			],
		preserveAnnotationDomElements:
			true,
		storeItems:
			{
				ha53e3e34e508260f:
					$.parseHTML( ve.dm.example.singleLine`<sup about="#mwt1" class="mw-ref reference" id="cite_ref-ldr_1-0" rel="dc:references" typeof="mw:Extension/ref" data-mw="{&quot;name&quot;:&quot;ref&quot;,&quot;attrs&quot;:{&quot;name&quot;:&quot;ldr&quot;}}"><a href="./Example/CiteDetailsReferencesLoss#cite_note-ldr-1" id="mwAw"><span class="mw-reflink-text" id="mwBA"><span class="cite-bracket" id="mwBQ">[</span>1<span class="cite-bracket" id="mwBg">]</span></span></a></sup>` )
			}
	},
	'Template generated reflist (div wrapped)': {
		body: ve.dm.example.singleLine`
			<p><sup about="#mwt2" class="mw-ref reference" id="cite_ref-1" rel="dc:references" typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{}}'><a href="./Main_Page#cite_note-1"><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>
			<div about="#mwt3" typeof="mw:Transclusion" data-mw='{"parts":[{"template":{"target":{"wt":"reflist","href":"./Template:Reflist"},"params":{},"i":0}}]}'>
				<div typeof="mw:Extension/references" about="#mwt5" data-mw='{"name":"references","attrs":{}}'>
					<ol class="mw-references references">
						<li about="#cite_note-1" id="cite_note-1"><a href="./Main_Page#cite_ref-1" rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span id="mw-reference-text-cite_note-1" class="mw-reference-text">Foo</span></li>
					</ol>
				</div>
			</div>
		`,
		fromDataBody: ve.dm.example.singleLine`
			<p><sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{}}'></sup></p>
			<span typeof="mw:Transclusion" data-mw='{"name":"references","attrs":{}}'></span>
		`,
		clipboardBody: ve.dm.example.singleLine`
			<p><sup typeof="mw:Extension/ref" data-mw='{"attrs":{},"body":{"id":"mw-reference-text-cite_note-1","html":"Foo"},"name":"ref"}' class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a></sup></p>
			<div typeof="mw:Extension/references" data-mw='{"name":"references","attrs":{}}'>
				<ol class="mw-references references">
					<li style='--footnote-number: "1.";'>
						<a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a>
							 ${ ve.dm.ConverterTestCases.refListItemClipboard( 'Foo' ) }
					</li>
				</ol>
			</div>
		`,
		previewBody: false,
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
							id: 'mw-reference-text-cite_note-1'
						},
						name: 'ref'
					},
					originalMw: '{"name":"ref","body":{"id":"mw-reference-text-cite_note-1"},"attrs":{}}',
					refGroup: '',
					refListItemId: 'mw-reference-text-cite_note-1'
				}
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
			{
				type: 'mwReferencesList',
				attributes: {
					mw: {
						name: 'references',
						attrs: {}
					},
					originalMw: '{"name":"references","attrs":{}}',
					refGroup: '',
					listGroup: 'mwReference/',
					isResponsive: true,
					templateGenerated: true
				}
			},
			{ type: '/mwReferencesList' },
			{ type: 'internalList' },
			{ type: 'internalItem', attributes: { originalHtml: 'Foo' } },
			{
				internal: {
					generated: 'wrapper'
				},
				type: 'paragraph'
			},
			'F', 'o', 'o',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: '/internalList' }
		]
	},
	'Deletion of main ref does not orphan subref': {
		// Start with this content:
		// <ref name="book">Miller 2025</ref>
		// <ref name="book" details="page 1" />
		//
		// Delete the main ref.  Subref should become a main+details.
		data: [
			{ type: 'paragraph' },
			{
				attributes: {
					contentsUsed: true,
					listGroup: 'mwReference/',
					listIndex: 0,
					listKey: 'literal/book',
					mw: {
						attrs: { name: 'book' },
						body: { id: 'mw-reference-text-cite_note-book-1' },
						name: 'ref'
					},
					originalMw: '{"name":"ref","attrs":{"name":"book"},"body":{"id":"mw-reference-text-cite_note-book-1"}}',
					refGroup: '',
					refListItemId: 'mw-reference-text-cite_note-book-1'
				},
				type: 'mwReference'
			},
			{ type: '/mwReference' },
			{
				attributes: {
					contentsUsed: true,
					mainRefKey: 'literal/book',
					listGroup: 'mwReference/',
					listIndex: 1,
					listKey: 'auto/0',
					mw: {
						attrs: { details: 'page 1' },
						body: { id: 'mw-reference-text-cite_note-2' },
						mainRef: 'book',
						name: 'ref'
					},
					originalMw: '{"name":"ref","attrs":{"details":"page 1"},"mainRef":"book","body":{"id":"mw-reference-text-cite_note-2"}}',
					refGroup: '',
					refListItemId: 'mw-reference-text-cite_note-2'
				},
				type: 'mwReference'
			},
			{ type: '/mwReference' },
			{ type: '/paragraph' },
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
			{ type: '/mwReferencesList' },
			{
				type: 'internalList'
			},
			{
				attributes: { originalHtml: 'Miller 2025' },
				type: 'internalItem'
			},
			{
				internal: { generated: 'wrapper' },
				type: 'paragraph'
			},
			'M', 'i', 'l', 'l', 'e', 'r', ' ', '2', '0', '2', '5',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{
				attributes: { originalHtml: 'page 1' },
				type: 'internalItem'
			},
			{
				internal: { generated: 'wrapper' },
				type: 'paragraph'
			},
			'p', 'a', 'g', 'e', ' ', '1',
			{ type: '/paragraph' },
			{ type: '/internalItem' },
			{ type: '/internalList' }
		],
		modify: ( doc ) => {
			doc.commit( ve.dm.TransactionBuilder.static.newFromRemoval(
				doc,
				new ve.Range( 1, 3 )
			) );
		},
		// FIXME: Main ref and main footnote body has disappeared.
		normalizedBody: ve.dm.example.singleLine`
			<p>
			<sup typeof="mw:Extension/ref" data-mw='{"attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2"},"mainRef":"book","name":"ref"}' class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup></p>
			<div typeof="mw:Extension/references" about="#mwt3" data-mw='{"name":"references","attrs":{},"autoGenerated":true}'>
				<ol>
					<li class="ve-ce-mwReferencesListNode-missingRef">
						<span></span>
						<ol>
							<li><span typeof="mw:Extension/ref" id="mw-reference-text-cite_note-2">page 1</span></li>
						</ol>
					</li>
				</ol>
			</div>
		`,
		clipboardBody: ve.dm.example.singleLine`
			<p>
				<sup typeof="mw:Extension/ref" data-mw='{"attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2","html":"page 1"},"mainRef":"book","name":"ref"}' class="mw-ref reference"><a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a></sup>
			</p>
			<div typeof="mw:Extension/references" data-mw='{"name":"references","attrs":{},"autoGenerated":true}'><ol class="mw-references references">
				<li style='--footnote-number: "1.";' class="ve-ce-mwReferencesListNode-missingRef"><span rel="mw:referencedBy"></span> <span class="ve-ce-mwReferencesListNode-muted">cite-ve-referenceslist-missing-parent</span><ol>
					<li style='--footnote-number: "1.1.";'><a rel="mw:referencedBy"><span class="mw-linkback-text">↑ </span></a> <span class="reference-text"><div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output"><span class="ve-ce-branchNode ve-ce-internalItemNode"><p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode ve-ce-generated-wrapper">page 1</p></span></div></span></li>
				</ol></li>
			</ol></div>
		`
	}
};
