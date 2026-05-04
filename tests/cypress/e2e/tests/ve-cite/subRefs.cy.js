require( '@cypress/skip-test/support' );
import * as helper from './../../utils/functions.helper.js';
import * as veHelper from './../../utils/ve.helper.js';

const wikiText =
	'MainRef1<ref name="MainRef1">Content of MainRef1</ref> ' +
	'Reused MainRef1<ref name="MainRef1" /><br>' +

	'MainRef2<ref name="MainRef2">Content of MainRef2</ref> ' +
	'Subref of MainRef2<ref name="MainRef2" details="Details of MainRef2"/><br>' +

	'MainPlusDetailsRef <ref name="MainPlusDetails" details="DetailsContent of MainPlusDetails">' +
	'Content of MainPlusDetails</ref>';

let usesCitoid;

describe( 'VisualEditor Cite with sub-references', () => {

	before( () => {
		veHelper.checkModuleDependencies().then( ( deps ) => {
			cy.skipOn( !deps.visualEditor );
			usesCitoid = deps.citoid;
		} );
	} );

	beforeEach( () => {
		cy.session( 've-cite-subRefs', () => {
			cy.clearCookies();
			veHelper.setVECookiesToDisableDialogs();
		} );

		const title = helper.getTestString( 'CiteTest-subRefs' );
		helper.editPage( title, wikiText );
		veHelper.openVEForEditingReferences( title, usesCitoid );
	} );

	it( 'should convert references into sub-references', () => {
		veHelper.getRefsFromArticleSection()
			.eq( 0 )
			.click();

		cy.contains( '.ve-ui-mwReferenceContextItem-addDetailsButton a', 'Add details' ).click();

		cy.get( '.ve-ui-mwReferenceEditPanel .ve-ce-documentNode' )
			.type( 'Details of MainRef1' );

		// Assert the checkbox is initially unchecked
		cy.get( '.ve-ui-mwReferenceEditPanel .oo-ui-checkboxInputWidget input[type="checkbox"]' )
			.should( 'not.be.checked' );
		cy.get( '.ve-ui-mwReferenceEditPanel .oo-ui-checkboxInputWidget input[type="checkbox"]' )
			.check();
		cy.get( '.ve-ui-mwReferenceEditPanel .oo-ui-checkboxInputWidget input[type="checkbox"]' )
			.should( 'be.checked' );

		// Click the "Insert" button in the add-details dialog
		cy.get( '.oo-ui-processDialog-actions-primary .oo-ui-buttonElement-button' ).click();

		// Close context item popup
		cy.get( '#bodyContent' ).click();

		// ARTICLE SECTION
		// Both references have the same footnote number
		veHelper.getRefsFromArticleSection()
			.eq( 0 )
			.should( 'have.text', '[1.1]' );

		veHelper.getRefsFromArticleSection()
			.eq( 1 )
			.should( 'have.text', '[1.1]' );

		// Main and details content are in Reflist
		veHelper.getRefFromReferencesSection( 1 )
			.find( '> .reference-text' )
			.should( 'have.text', 'Content of MainRef1' );
		veHelper.getRefFromReferencesSection( 1 )
			.find( 'ol > li > .reference-text' )
			.should( 'have.text', 'Details of MainRef1' );

		// Switch to WikiText Editor
		cy.get( '#p-views #ca-edit' ).click();

		// Assert that the Wikitext contains the updated main and sub-references
		cy.get( 'textarea[name="wpTextbox1"]' )
			.should( 'contain.value',
				'MainRef1<ref name="MainRef1" details="Details of MainRef1">Content of MainRef1</ref> Reused MainRef1<ref name="MainRef1" details="Details of MainRef1" />'
			);
	} );

	it( 'should update edited Main+Details ref content', () => {
		// Main + Details Ref exists in article section
		cy.get( '#mw-content-text p sup' ).eq( 4 );
		veHelper.getRefsFromArticleSection().eq( 4 ).should( 'contain', '3.1' );

		// And in reference section
		veHelper.getRefFromReferencesSection( 4 ).find( 'ol > li > .reference-text' )
			.should( 'have.text', 'DetailsContent of MainPlusDetails' );

		// Open context item
		veHelper.getRefsFromArticleSection().eq( 4 ).click();

		cy.get( '.ve-ui-context-menu .ve-ui-mwReferenceContextItem-detailsPreview p' )
			.should( 'have.text', 'DetailsContent of MainPlusDetails' );

		// Open edit details dialog
		veHelper.getVEReferenceContextItemDetailsEdit().click();

		cy.get( '.ve-ui-mwReferenceEditPanel .ve-ce-documentNode' )
			.should( 'have.text', 'DetailsContent of MainPlusDetails' );

		// Modify the sub-reference content of Main + Details Ref
		cy.get( '.ve-ui-mwReferenceEditPanel .ve-ce-documentNode' )
			.type( ' Edited' );

		// Save changes
		cy.get( '.oo-ui-processDialog-actions-primary .oo-ui-buttonElement-button' ).click();

		// Context item has updated details content
		cy.get( '.ve-ui-mwReferenceContextItem-detailsPreview p' )
			.should( 'have.text', 'DetailsContent of MainPlusDetails Edited' );

		// Close context item popup
		cy.get( '#bodyContent' ).click();

		// Reflist has updated details content
		veHelper.getRefFromReferencesSection( 4 )
			.find( 'ol > li > .reference-text' )
			.should( 'have.text', 'DetailsContent of MainPlusDetails Edited' );

		// Switch to WikiText Editor
		cy.get( '#p-views #ca-edit' ).click();

		// Assert that the Wikitext contains Main+Details Ref with updated details content
		cy.get( 'textarea[name="wpTextbox1"]' )
			.should( 'contain.value', '<ref name="MainPlusDetails" details="DetailsContent of MainPlusDetails Edited">Content of MainPlusDetails</ref>' );
	} );

	it( 'should move main ref content to the subRef when the main ref is removed', () => {
		// Assert for main ref with its subRef in article section
		veHelper.getRefsFromArticleSection()
			.eq( 2 )
			.should( 'have.text', '[2]' );

		// Delete main ref linked by sub-ref
		veHelper.getRefsFromArticleSection()
			.eq( 2 )
			.should( 'have.text', '[2]' )
			.type( '{del}' );

		// Open 2.1 ref context item
		veHelper.getRefsFromArticleSection()
			.eq( 2 )
			.click();

		// Main and details content are present in the context item
		cy.get( '.ve-ui-linearContextItem-body > .ve-ui-previewElement p' )
			.should( 'have.text', 'Content of MainRef2' );
		cy.get( '.ve-ui-mwReferenceContextItem-detailsPreview .ve-ui-previewElement p' )
			.should( 'have.text', 'Details of MainRef2' );

		// Close context item popup
		cy.get( '#bodyContent' ).click();

		// Main and details content are in Reflist
		veHelper.getRefFromReferencesSection( 2 )
			.find( '> .reference-text' )
			.should( 'have.text', 'Content of MainRef2' );
		veHelper.getRefFromReferencesSection( 2 )
			.find( 'ol > li .reference-text' )
			.should( 'have.text', 'Details of MainRef2' );

		// Switch to WikiText Editor
		cy.get( '#p-views #ca-edit' ).click();

		// Assert that the Wikitext contains Main+Details Ref with updated details content
		cy.get( 'textarea[name="wpTextbox1"]' )
			.should( 'contain.value', 'Subref of MainRef2<ref name="MainRef2" details="Details of MainRef2">Content of MainRef2</ref>' );
	} );

} );
