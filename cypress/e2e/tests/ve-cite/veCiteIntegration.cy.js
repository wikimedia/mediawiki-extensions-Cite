import * as helpers from './../../utils/functions.helper.js';
const title = helpers.getTestString( 'CiteTest-title' );

const refText1 = 'This is citation #1 for reference #1 and #2';
const refText2 = 'This is citation #2 for reference #3';

const wikiText = `This is reference #1: <ref name="a">${ refText1 }</ref><br> ` +
	'This is reference #2 <ref name="a" /><br>' +
	`This is reference #3 <ref>${ refText2 }</ref><br>` +
	'<references />';

describe( 'Visual Editor Cite Integration', () => {
	before( () => {
		cy.visit( '/index.php' );
		helpers.editPage( title, wikiText );
	} );

	beforeEach( () => {
		helpers.visitTitle( title );

		cy.window().then( async ( win ) => {
			win.localStorage.setItem( 've-beta-welcome-dialog', 1 );
		} );

		helpers.visitTitle( title, { veaction: 'edit' } );
	} );

	it( 'should edit and verify reference content in Visual Editor', () => {
		helpers.getVEFootnoteMarker( 'a', 1, 1 ).click();

		// Popup appears containing ref content
		helpers.getVEReferencePopup()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );

		// Open edit popup
		cy.contains( '.oo-ui-buttonElement-button', 'Edit' ).click();

		// Dialog appears with ref content
		helpers.getVEDialog()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );
	} );

	it( 'should display existing references in the Cite re-use dialog', () => {
		helpers.openVECiteReuseDialog();

		// Assert reference content for the first reference
		helpers.getCiteReuseDialogRefName( 1 ).should( 'contain.text', 'a' );
		helpers.getCiteReuseDialogRefNumber( 1 ).should( 'contain.text', '[1]' );
		helpers.getCiteReuseDialogRefText( 1 ).should( 'have.text', refText1 );

		// Assert reference content for the second reference
		helpers.getCiteReuseDialogRefName( 2 ).should( 'contain.text', '' );
		helpers.getCiteReuseDialogRefNumber( 2 ).should( 'contain.text', '[2]' );
		helpers.getCiteReuseDialogRefText( 2 ).should( 'have.text', refText2 );

	} );

} );
