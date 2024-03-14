import * as helpers from './../../utils/functions.helper.js';

const title = helpers.getTestString( 'CiteTest-title' );

describe( 'Visual Editor Cite Integration', () => {
	before( () => {
		cy.visit( '/index.php' );

		const wikiText = 'This is reference #1: <ref name="a">This is citation #1 for reference #1 and #2</ref><br> ' +
			'This is reference #2: <ref name="a" /><br>' +
			'<references />';

		helpers.editPage( title, wikiText );
		cy.window().then( async ( win ) => {
			win.localStorage.setItem( 've-beta-welcome-dialog', 1 );
		} );
	} );

	it( 'should edit and verify reference content in Visual Editor', () => {
		helpers.visitTitle( title, { veaction: 'edit' } );

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
