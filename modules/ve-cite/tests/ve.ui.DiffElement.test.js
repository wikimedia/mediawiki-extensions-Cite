/*!
 * VisualEditor Cite-specific DiffElement tests.
 *
 * @copyright 2011-2018 VisualEditor Team and others; see http://ve.mit-license.org
 */

QUnit.module( 've.ui.DiffElement (Cite)' );

QUnit.test( 'Diffing', function ( assert ) {
	var spacer = '<div class="ve-ui-diffElement-spacer">⋮</div>',
		ref = function ( text, num ) {
			var dataMw = {
				name: 'ref',
				body: { html: text }
				// attrs doesn't get set in preview mode
			};

			return '<sup typeof="mw:Extension/ref" data-mw="' + JSON.stringify( dataMw ).replace( /"/g, '&quot;' ) + '" class="mw-ref reference">' +
						'<a style="counter-reset: mw-Ref ' + num + ';"><span class="mw-reflink-text">[' + num + ']</span></a>' +
					'</sup>';
		},
		cases = [
			{
				msg: 'Simple ref change',
				oldDoc:
					'<p>' + ref( 'Foo' ) + ref( 'Bar' ) + ref( 'Baz' ) + '</p>' +
					'<h2>Notes</h2>' +
					'<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;}"></div>',
				newDoc:
					'<p>' + ref( 'Foo' ) + ref( 'Bar ish' ) + ref( 'Baz' ) + '</p>' +
					'<h2>Notes</h2>' +
					'<div typeof="mw:Extension/references" data-mw="{&quot;name&quot;:&quot;references&quot;}"></div>',
				expected:
					spacer +
					'<h2 data-diff-action="none">Notes</h2>' +
					'<div>' +
						'<ol start="1">' +
							'<li><p data-diff-action="none">Foo</p></li>' +
						'</ol>' +
						'<ol start="2">' +
							'<li>Bar<ins data-diff-action="insert"> ish</ins></li>' +
						'</ol>' +
						'<ol start="3">' +
							'<li><p data-diff-action="none">Baz</p></li>' +
						'</ol>' +
					'</div>'
			}
		];

	cases.forEach( function ( caseItem ) {
		ve.test.utils.runDiffElementTest( assert, caseItem );
	} );

} );
