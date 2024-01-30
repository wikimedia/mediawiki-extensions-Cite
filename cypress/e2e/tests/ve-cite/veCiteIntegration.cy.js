import * as helpers from './../../utils/functions.helper.js';

const title = getTestString( 'CiteTest-title' );
const encodedTitle = encodeURIComponent( title );

function getTestString( prefix = '' ) {
	return prefix + Math.random().toString();
}

describe( 'Visual Editor Cite Integration', () => {
	before( () => {
		cy.visit( '/index.php' );

		const wikiText = 'This is reference #1: <ref name="a">This is citation #1 for reference #1 and #2</ref><br> ' +
			'This is reference #2: <ref name="a" /><br>' +
			'<references />';

		// Rely on the retry behavior of Cypress assertions to use this as a "wait" until the specified conditions are met.
		cy.window().should( 'have.property', 'mw' ).and( 'have.property', 'loader' ).and( 'have.property', 'using' );
		// Create a new page containing a reference
		cy.window().then( async ( win ) => {
			await win.mw.loader.using( 'mediawiki.api' );
			const response = await new win.mw.Api().create( title, {}, wikiText );
			expect( response.result ).to.equal( 'Success' );
		} );

	} );

	it( 'should edit and verify reference content in Visual Editor', () => {
		cy.visit( `/index.php?title=${ encodedTitle }` );
		// Open VE
		cy.get( 'li#ca-ve-edit a' ).click();
		cy.contains( '.oo-ui-buttonElement-button', 'Start editing' ).click();
		cy.url().should( 'include', 'veaction=edit' );

		helpers.getVEFootnoteMarker( 'a', 1, 1 ).click();
		// Popup appears containing ref content
		helpers.getVEReferencePopup()
			.should( 'be.visible' )
			.should( 'have.text', 'This is citation #1 for reference #1 and #2' );

		// Open edit popup
		cy.contains( '.oo-ui-buttonElement-button', 'Edit' ).click();

		// Dialog appears with ref content
		helpers.getVEDialog()
			.should( 'be.visible' )
			.should( 'have.text', 'This is citation #1 for reference #1 and #2' );
	} );

} );
