require( '@cypress/skip-test/support' );
import * as helper from './../../utils/functions.helper.js';
import * as veHelper from './../../utils/ve.helper.js';

const refText1 = 'This is citation #1 for reference #1 and #2';
const refText2 = 'This is citation #2 for reference #3';

const wikiText = `This is reference #1: <ref name="a">${ refText1 }</ref><br> ` +
	'This is reference #2 <ref name="a" /><br>' +
	`This is reference #3 <ref>${ refText2 }</ref><br>` +
	'<references />';

let usesCitoid;

describe( 'VisualEditor Cite', () => {
	before( () => {
		veHelper.checkModuleDependencies().then( ( deps ) => {
			cy.skipOn( !deps.visualEditor );
			usesCitoid = deps.citoid;
		} );
	} );

	beforeEach( () => {
		cy.clearCookies();
		veHelper.setVECookiesToDisableDialogs();

		const title = helper.getTestString( 'CiteTest-title' );
		helper.editPage( title, wikiText );
		veHelper.openVEForEditingReferences( title, usesCitoid );
	} );

	it( 'should be able to edit and verify reference content in Visual Editor', () => {
		veHelper.getVEFootnoteMarker( 'a', 1, 1 ).click();

		// Popup appears containing ref content
		veHelper.getVEReferenceContextItem()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );

		// Open reference edit dialog
		veHelper.getVEReferenceContextItemEdit().click();

		// Dialog appears with ref content
		veHelper.getVEReferenceEditDialog()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );
	} );

	it( 'should be able to reuse existing references via the reuse dialog', () => {
		// Currently there are 3 refs in the article
		helper.getRefsFromArticleSection().should( 'have.length', 3 );

		// Place cursor next to ref #2 in order to add reuse next to it
		cy.contains( '.ve-ui-surface .mw-reflink-text', '[2]' ).type( '{rightarrow}' );

		if ( usesCitoid ) {
			veHelper.openVECitoidReuseDialog();

		} else {
			veHelper.openVECiteReuseDialog();
		}

		// Assert reference content and name for the first reference
		veHelper.getCiteReuseDialogRefResultName( 1 ).should( 'have.text', 'a' );
		veHelper.getCiteReuseDialogRefResultCitation( 1 ).should( 'have.text', '[1]' );
		veHelper.getCiteReuseDialogRefText( 1 ).should( 'have.text', refText1 );
		veHelper.getCiteReuseDialogRefResultName( 1 ).should( 'have.text', 'a' );

		// Assert reference content for the second reference
		veHelper.getCiteReuseDialogRefResultName( 2 ).should( 'have.text', '' );
		veHelper.getCiteReuseDialogRefResultCitation( 2 ).should( 'have.text', '[2]' );
		veHelper.getCiteReuseDialogRefText( 2 ).should( 'have.text', refText2 );

		// Reuse the second reference
		veHelper.getCiteReuseDialogRefResult( 2 ).click();

		// The context dialog on the second reference shows it's being used twice
		cy.contains( '.ve-ui-surface .mw-reflink-text', '[2]' ).click();
		cy.get( '.oo-ui-popupWidget-popup .ve-ui-mwReferenceContextItem-reuse' )
			.should( 'have.text', 'Used twice' );

		veHelper.saveEdits();

		// ARTICLE SECTION
		// Ref has been added to article, there are now 4 refs in the article
		helper.getRefsFromArticleSection().should( 'have.length', 4 );

		// Ref #2 appears twice in the article, corresponding IDs match the backlinks in
		// the references section
		helper.backlinksIdShouldMatchFootnoteId( 2, 0, 2 );
		helper.backlinksIdShouldMatchFootnoteId( 3, 1, 2 );

		// Both references have the same footnote number
		cy.get( '#mw-content-text p sup a' ).eq( 2 ).should( 'have.text', '[2]' );
		cy.get( '#mw-content-text p sup a' ).eq( 3 ).should( 'have.text', '[2]' );

		// REFERENCES SECTION
		// References section contains a list item for each reference
		helper.getRefsFromReferencesSection().should( 'have.length', 2 );

		// Ref content should match
		helper.getRefFromReferencesSection( 1 ).should( 'contain', refText1 );
		helper.getRefFromReferencesSection( 2 ).find( '.reference-text' ).should( 'have.text', refText2 );
	} );
} );
