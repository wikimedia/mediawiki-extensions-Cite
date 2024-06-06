import * as helpers from './../../utils/functions.helper.js';
import * as veHelpers from './../../utils/ve.helper.js';

const title = helpers.getTestString( 'CiteTest-title' );

const refText1 = 'This is citation #1 for reference #1 and #2';
const refText2 = 'This is citation #2 for reference #3';

const wikiText = `This is reference #1: <ref name="a">${ refText1 }</ref><br> ` +
	'This is reference #2 <ref name="a" /><br>' +
	`This is reference #3 <ref>${ refText2 }</ref><br>` +
	'<references />';

let usesCitoid;

describe( 'Visual Editor Cite Integration', () => {
	before( () => {
		helpers.editPage( title, wikiText );
	} );

	beforeEach( () => {
		helpers.visitTitle( title );
		helpers.waitForMWLoader();

		cy.window().then( async ( win ) => {
			await win.mw.loader.using( 'mediawiki.base' ).then( async () => {
				await win.mw.hook( 'wikipage.content' ).add( () => { } );
			} );
			usesCitoid = win.mw.loader.getModuleNames().includes( 'ext.citoid.visualEditor' );
		} );

		veHelpers.setVECookiesToDisableDialogs();
		veHelpers.openVEForEditingReferences( title, usesCitoid );
	} );

	it( 'should edit and verify reference content in Visual Editor', () => {
		veHelpers.getVEFootnoteMarker( 'a', 1, 1 ).click();

		// Popup appears containing ref content
		veHelpers.getVEReferenceContextItem()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );

		// Open reference edit dialog
		veHelpers.getVEReferenceContextItemEdit().click();

		// Dialog appears with ref content
		veHelpers.getVEReferenceEditDialog()
			.should( 'be.visible' )
			.should( 'contain.text', refText1 );
	} );

	it( 'should display existing references in the Cite re-use dialog', () => {
		if ( usesCitoid ) {
			veHelpers.openVECiteoidReuseDialog();

		} else {
			veHelpers.openVECiteReuseDialog();
		}

		// Assert reference content for the first reference
		veHelpers.getCiteReuseDialogRefResultName( 1 ).should( 'have.text', 'a' );
		veHelpers.getCiteReuseDialogRefResultCitation( 1 ).should( 'have.text', '[1]' );
		veHelpers.getCiteReuseDialogRefText( 1 ).should( 'have.text', refText1 );

		// Assert reference content for the second reference
		veHelpers.getCiteReuseDialogRefResultName( 2 ).should( 'have.text', '' );
		veHelpers.getCiteReuseDialogRefResultCitation( 2 ).should( 'have.text', '[2]' );
		veHelpers.getCiteReuseDialogRefText( 2 ).should( 'have.text', refText2 );

	} );

} );
