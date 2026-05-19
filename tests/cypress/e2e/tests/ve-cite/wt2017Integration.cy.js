require( '@cypress/skip-test/support' );
import * as helper from './../../utils/functions.helper.js';
import * as veHelper from './../../utils/ve.helper.js';

let usesCitoid;

describe( 'VisualEditor Cite with WT2017 Editor', () => {
	before( () => {
		veHelper.checkModuleDependencies().then( ( deps ) => {
			cy.skipOn( !deps.visualEditor );
			usesCitoid = deps.citoid;
		} );

		helper.loginAsAdmin();
		helper.editPage( 'MediaWiki:Cite-tool-definition.json', JSON.stringify( [
			{
				name: 'web',
				title: 'Webseite',
				template: 'Internetquelle'
			},
			{
				name: 'book',
				title: 'Literatur',
				template: 'Literatur'
			}
		] ) );
	} );

	beforeEach( () => {
		cy.session( 've-cite-wt2017-integration', () => {
			cy.clearCookies();
			veHelper.setVECookiesToDisableDialogs();
		} );

		const title = helper.getTestString( 'CiteTest-wt2017' );
		veHelper.openVEForSourceEditingReferences( title, usesCitoid );
	} );

	it( 'should be able to create a basic reference', () => {
		if ( usesCitoid ) {
			veHelper.openVECitoidSourceSelector();
			cy.get( '.ve-ui-citeSourceSelectWidget-basic' ).click();
		} else {
			cy.get( '.ve-ui-toolbar-group-cite' ).click();
			cy.get( '.oo-ui-popupToolGroup-active-tools .oo-ui-tool-title' )
				.should( 'be.visible' )
				.contains( 'Basic' ).click();
		}

		cy.get( '.ve-ui-mwReferenceDialog .mw-content-ltr' ).type( 'Basic ref' );
		// Save changes
		cy.get( '.ve-ui-mwReferenceDialog .oo-ui-flaggedElement-primary' ).click();

		// Ref tag appears with correct content in edit source mode
		cy.get( '.ve-ui-mwWikitextSurface' ).should( 'contain.text', '<ref>Basic ref</ref>' );
	} );
} );
