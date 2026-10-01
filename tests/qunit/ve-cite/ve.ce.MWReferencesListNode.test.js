'use strict';

/*!
 * VisualEditor ContentEditable MWReferencesList tests.
 *
 * @copyright See AUTHORS.txt
 */
{
	QUnit.module( 've.ce.MWReferencesListNode (Cite)' );

	QUnit.test( 'getRenderedContents', ( assert ) => {
		const doc = ve.dm.citeExample.createExampleDocument( 'subReferencing' );
		const uiSurface = ve.test.utils.createSurfaceFromDocument( doc );
		const reflistCeNode = uiSurface.view.documentView.documentNode.children[ 1 ];
		assert.strictEqual( reflistCeNode.type, 'mwReferencesList' );
		reflistCeNode.update();

		const expectedHtml = ve.dm.example.singleLine`
	<li data-mw-footnote-number="1">
		<span rel="mw:referencedBy"></span> <span class="reference-text">
			<div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output">
				<span class="ve-ce-branchNode ve-ce-internalItemNode">
					<p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode">
						List-defined
					</p>
				</span>
			</div>
		</span>
		<ol class="mw-subreference-list">
			<li data-mw-footnote-number="1.1">
				<a rel="mw:referencedBy">
					<span class="mw-linkback-text">↑</span>
				</a> <span class="reference-text">
					<div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output">
						<span class="ve-ce-branchNode ve-ce-internalItemNode">
							<p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode">
								Subref
							</p>
						</span>
					</div>
				</span>
			</li>
		</ol>
	</li>
	<li data-mw-footnote-number="2">
		<a rel="mw:referencedBy">
			<span class="mw-linkback-text">↑</span>
		</a> <span class="reference-text">
			<div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output">
				<span class="ve-ce-branchNode ve-ce-internalItemNode">
					<p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode">
						Other
					</p>
				</span>
			</div>
		</span>
	</li>
	<li data-mw-footnote-number="3">
		<span rel="mw:referencedBy"></span> <span class="reference-text">
			<div class="mw-content-ltr ve-ui-previewElement ve-ui-mwPreviewElement mw-body-content mw-parser-output">
				<span class="ve-ce-branchNode ve-ce-internalItemNode">
					<p class="ve-ce-branchNode ve-ce-contentBranchNode ve-ce-paragraphNode">
						Main no node
					</p>
				</span>
			</div>
		</span>
		<ol class="mw-subreference-list">
			<li class="ve-ce-mwReferencesListNode-missingRef" data-mw-footnote-number="3.1">
				<a rel="mw:referencedBy">
					<span class="mw-linkback-text">↑</span>
				</a> <span class="ve-ce-mwReferencesListNode-muted">
					cite-ve-referenceslist-missingref-in-list
				</span>
			</li>
		</ol>
	</li>
	`;

		assert.equalDomElement(
			$( '<div>' ).html( reflistCeNode.$reflist.html() )[ 0 ],
			$( '<div>' ).html( expectedHtml )[ 0 ]
		);
	} );
}
