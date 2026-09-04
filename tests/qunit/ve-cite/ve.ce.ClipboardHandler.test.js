'use strict';

/*!
 * VisualEditor Cite-specific ContentEditable ClipboardHandler tests.
 *
 * @copyright See AUTHORS.txt
 * @license The MIT License (MIT); see LICENSE.txt
 */
{
	QUnit.module( 've.ce.ClipboardHandler (Cite)', ve.test.utils.newMwEnvironment() );

	QUnit.test( 'beforePaste/afterPaste', ( assert ) => {
		const cases = [
			{
				msg: 'Legacy parser read mode references stripped',
				documentHtml: '<p></p>',
				rangeOrSelection: new ve.Range( 1 ),
				pasteHtml: ve.dm.example.singleLine`
				a
					<sup id="cite_ref-1" class="reference">
						<a href="./Article#cite_note-1">[1]</a>
					</sup>
				b
			`,
				expectedRangeOrSelection: new ve.Range( 3 ),
				expectedHtml: '<p>ab</p>'
			},
			{
				msg: 'Parsoid read mode references stripped',
				documentHtml: '<p></p>',
				rangeOrSelection: new ve.Range( 1 ),
				pasteHtml: ve.dm.example.singleLine`
				a
				<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{},"body":{"id":"mw-reference-text-cite_note-1"}}' class="mw-ref reference" about="#mwt1" id="cite_ref-foo-0" rel="dc:references">
					<a href="./Article#cite_note-foo-0"><span class="mw-reflink-text">[1]</span></a>
				</sup>
				b
			`,
				expectedRangeOrSelection: new ve.Range( 3 ),
				expectedHtml: '<p>ab</p>'
			},
			{
				msg: 'VE references not stripped',
				documentHtml: '<p></p>',
				rangeOrSelection: new ve.Range( 1 ),
				pasteHtml: ve.dm.example.singleLine`
				a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...some reference HTML..."}}' class="mw-ref reference" about="#mwt1" id="cite_ref-foo-0" rel="dc:references">
						<a href="./Article#cite_note-foo-0"><span class="mw-reflink-text ve-pasteProtect">[1]</span></a>
					</sup>
				b
			`,
				expectedRangeOrSelection: new ve.Range( 5 ),
				expectedHtml: ve.dm.example.singleLine`
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...some reference HTML..."}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
					</sup>
					b
				</p>
			`
			},
			{
				msg: 'VE external reference with conflicting name disambiguated',
				// ref with name foo
				documentHtml: ve.dm.example.singleLine`
				<p>
					a
						<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"foo"}}' class="mw-ref reference">
							<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
						</sup>
				</p>
			`,
				rangeOrSelection: new ve.Range( 4 ),
				// ref with name foo
				pasteHtml: ve.dm.example.singleLine`
				b
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...another reference HTML..."},"attrs":{"name":"foo"}}' class="mw-ref reference" about="#mwt1" id="cite_ref-foo-0" rel="dc:references">
						<a href="./Article#cite_note-foo-0"><span class="mw-reflink-text ve-pasteProtect">[1]</span></a>
					</sup>
			`,
				expectedRangeOrSelection: new ve.Range( 7 ),
				// 2nd ref with name foo2
				expectedHtml: ve.dm.example.singleLine`
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"foo"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
					</sup>
					b
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...another reference HTML..."},"attrs":{"name":"foo2"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2<span class="cite-bracket">]</span></span></a>
					</sup>
				</p>
			`
			},
			{
				msg: 'VE internal reference with same name deduplicated',
				// ref with name foo
				documentHtml: ve.dm.example.singleLine`
				<p>
					a
						<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"foo"}}' class="mw-ref reference">
							<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
						</sup>
				</p>
			`,
				internalSourceRangeOrSelection: new ve.Range( 0, 6 ),
				rangeOrSelection: new ve.Range( 6 ),
				expectedRangeOrSelection: new ve.Range( 10 ),
				// 2nd ref with same name
				expectedHtml: ve.dm.example.singleLine`
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"foo"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
					</sup>
				</p>
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"name":"foo"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
					</sup>
				</p>
			`
			},
			{
				msg: 'VE internal sub-reference with same name deduplicated',
				// sub-ref with name book
				documentHtml: ve.dm.example.singleLine`
				<p>
					a
						<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2","html":"page 1"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","mainBodyHtml":"...2nd reference HTML..."}' class="mw-ref reference">
							<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a>
						</sup>
				</p>
			`,
				internalSourceRangeOrSelection: new ve.Range( 0, 6 ),
				rangeOrSelection: new ve.Range( 6 ),
				expectedRangeOrSelection: new ve.Range( 10 ),
				// 2nd ref with same name
				expectedHtml: ve.dm.example.singleLine`
				<p>
					a
						<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2","html":"page 1"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","mainBodyHtml":"...2nd reference HTML..."}' class="mw-ref reference">
							<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a>
						</sup>
				</p>
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"name":"book"},"body":{"html":"page 1"},"mainRef":"book"}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a>
					</sup>
				</p>
			`
			},
			{
				msg: 'VE external sub-reference with conflicting name disambiguated',
				// ref with name book
				documentHtml: ve.dm.example.singleLine`
				<p>
					a
						<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"book"}}' class="mw-ref reference">
							<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
						</sup>
				</p>
			`,
				rangeOrSelection: new ve.Range( 4 ),
				// sub-ref with name book
				pasteHtml: ve.dm.example.singleLine`
				b
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2","html":"page 1"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","mainBodyHtml":"...2nd reference HTML..."}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a>
					</sup>
			`,
				expectedRangeOrSelection: new ve.Range( 7 ),
				// FIXME 2nd ref should be `book2` `...2nd reference HTML...` see #T418324
				expectedHtml: ve.dm.example.singleLine`
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"book"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
					</sup>
					b
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"name":"book"},"body":{"html":"page 1"},"mainRef":"book","mainBodyHtml":"page 1"}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2.1<span class="cite-bracket">]</span></span></a>
					</sup>
				</p>
			`
			},
			{
				msg: 'VE external reference with conflicting sub-reference name disambiguated',
				// sub-ref with name book
				documentHtml: ve.dm.example.singleLine`
				<p>
					a
						<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2","html":"page 1"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","mainBodyHtml":"...2nd reference HTML..."}' class="mw-ref reference">
							<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a>
						</sup>
				</p>
			`,
				rangeOrSelection: new ve.Range( 4 ),
				// ref with name book
				pasteHtml: ve.dm.example.singleLine`
				b
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"book"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1<span class="cite-bracket">]</span></span></a>
					</sup>
			`,
				expectedRangeOrSelection: new ve.Range( 7 ),
				// FIXME 2nd ref should be `book2` see #T418324
				expectedHtml: ve.dm.example.singleLine`
				<p>
					a
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","attrs":{"details":"page 1","name":"book"},"body":{"id":"mw-reference-text-cite_note-2","html":"page 1"},"mainRef":"book","mainBody":"mw-reference-text-cite_note-book-1","mainBodyHtml":"...2nd reference HTML..."}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>1.1<span class="cite-bracket">]</span></span></a>
					</sup>
					b
					<sup typeof="mw:Extension/ref" data-mw='{"name":"ref","body":{"html":"...original reference HTML..."},"attrs":{"name":"book"}}' class="mw-ref reference">
						<a><span class="mw-reflink-text"><span class="cite-bracket">[</span>2<span class="cite-bracket">]</span></span></a>
					</sup>
				</p>
			`
			}
		];

		const done = assert.async();
		( async function () {
			for ( const caseItem of cases ) {
				await ve.test.utils.runSurfacePasteTest( assert, caseItem );
			}
			done();
		}() );
	} );
}
