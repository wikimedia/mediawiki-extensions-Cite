require( '@cypress/skip-test/support' );
import * as helper from './../../utils/functions.helper.js';
import * as veHelper from './../../utils/ve.helper.js';

const refText1 = 'Citation #1';
const refTextNew = 'Citation #1 NEW';
const refText2 = 'Citation #2';

const wikiText = `# <ref name="foo">${ refText1 }</ref>\n` +
	'# <ref name="foo" />\n' +
	`# <ref>${ refText2 }</ref>`;

const wikiTextEdited = `# <ref name="foo">${ refTextNew }</ref>\n` +
	'# <ref name="foo" />\n' +
	`# <ref>${ refText2 }</ref>`;

const resuedWikiText = `# <ref name="foo">${ refText1 }</ref>\n` +
	'# <ref name="foo" />\n' +
	`# <ref name=":0">${ refText2 }</ref><ref name=":0" />`;

let usesCitoid;

describe( 'VisualEditor Cite', () => {
	before( () => {
		veHelper.checkModuleDependencies().then( ( deps ) => {
			cy.skipOn( !deps.visualEditor );
			usesCitoid = deps.citoid;
		} );
	} );

	beforeEach( () => {
		cy.session( 've-cite-integration', () => {
			cy.clearCookies();
			veHelper.setVECookiesToDisableDialogs();
		} );

		const title = helper.getTestString( 'CiteTest-title' );
		helper.editPage( title, wikiText );
		veHelper.openVEForEditingReferences( title, usesCitoid );
	} );

	it( 'should be able to edit and verify reference content in Visual Editor', () => {
		veHelper.getVEFootnoteMarker( 'foo', 1, 1 ).click();

		// Context popup appears containing ref content
		veHelper.getVEReferenceContextItem()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );

		// Open reference edit dialog
		veHelper.getVEReferenceContextItemEdit().click();

		// Dialog appears with ref content
		veHelper.getVEReferenceEditDialog()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );

		cy.get( '.ve-ui-mwReferenceEditPanel .ve-ce-documentNode' )
			.type( ' NEW' );

		// Click the "Apply" button in the edit dialog
		cy.get( '.oo-ui-processDialog-actions-primary .oo-ui-buttonElement-button' ).click();

		// Use the save dialog
		veHelper.saveEdits();

		// Switch to WikiText Editor
		cy.get( '#p-views #ca-edit' ).click();

		// Assert that the Wikitext contains Main+Details Ref with updated details content
		cy.get( 'textarea[name="wpTextbox1"]' )
			.should( 'contain.value', wikiTextEdited );
	} );

	it( 'should be able to reuse existing references via the reuse dialog', () => {
		// Currently there are 3 refs in the article
		veHelper.getRefsFromArticleSection().should( 'have.length', 3 );

		// Place cursor next to ref #2 in order to add reuse next to it
		cy.contains( '.ve-ui-surface .mw-reflink-text', '[2]' ).type( '{rightarrow}' );

		if ( usesCitoid ) {
			veHelper.openVECitoidReuseDialog();

		} else {
			veHelper.openVECiteReuseDialog();
		}

		// Assert reference content and name for the first reference
		veHelper.getCiteReuseDialogRefResultName( 1 ).should( 'have.text', 'foo' );
		veHelper.getCiteReuseDialogRefResultCitation( 1 ).should( 'have.text', '[1]' );
		veHelper.getCiteReuseDialogRefText( 1 ).should( 'have.text', refText1 );

		// Assert reference content for the second reference
		veHelper.getCiteReuseDialogRefResultName( 2 ).should( 'have.text', '' );
		veHelper.getCiteReuseDialogRefResultCitation( 2 ).should( 'have.text', '[2]' );
		veHelper.getCiteReuseDialogRefText( 2 ).should( 'have.text', refText2 );

		// Reuse the second reference
		veHelper.getCiteReuseDialogRefResult( 2 ).click();

		// Now there are 4 refs in the article
		veHelper.getRefsFromArticleSection().should( 'have.length', 4 );

		// The context dialog on the second reference shows it's being used twice
		cy.contains( '.ve-ui-surface .mw-reflink-text', '[2]' ).click();
		cy.get( '.oo-ui-popupWidget-popup .ve-ui-mwReferenceContextItem-reuse' )
			.should( 'have.text', 'Used twice' );

		// Switch to WikiText Editor
		cy.get( '#p-views #ca-edit' ).click();

		// Assert that the Wikitext contains Main+Details Ref with updated details content
		cy.get( 'textarea[name="wpTextbox1"]' )
			.should( 'contain.value', resuedWikiText );
	} );
} );
