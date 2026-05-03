require( '@cypress/skip-test/support' );
import * as helper from './../../utils/functions.helper.js';
import * as veHelper from './../../utils/ve.helper.js';

let usesCitoid;

describe( 'VisualEditor Cite with citation templates', () => {

	before( () => {
		veHelper.checkModuleDependencies().then( ( deps ) => {
			cy.skipOn( !deps.visualEditor || !deps.templateData );
			usesCitoid = deps.citoid;
		} );

		cy.clearCookies();
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
		cy.session( 've-cite-templates', () => {
			cy.clearCookies();
			veHelper.setVECookiesToDisableDialogs();
		} );

		const title = helper.getTestString( 'CiteTest-templates' );
		veHelper.openVEForEditingReferences( title, usesCitoid );
	} );

	it( 'should be able to add a new template in VE', () => {
		if ( usesCitoid ) {
			cy.get( '.ve-ui-toolbar-group-citoid' ).click();

			// Switch to Manual tab
			// TODO: Sometimes enabling the tab does not work right away.
			// eslint-disable-next-line cypress/no-unnecessary-waiting
			cy.wait( 500 );
			cy.get( '.oo-ui-tabSelectWidget .oo-ui-labelElement-label' ).contains( 'Manual' ).click();

			cy.get( '.oo-ui-labelElement-label' ).contains( 'Literatur' )
				.should( 'be.visible' );
			cy.get( '.oo-ui-labelElement-label' ).contains( 'Webseite' ).click();

		} else {
			cy.get( '.ve-ui-toolbar-group-cite' ).click();
			cy.get( '.oo-ui-tool-name-cite-book' ).contains( 'Literatur' )
				.should( 'be.visible' );
			cy.get( '.oo-ui-tool-name-cite-web' ).contains( 'Webseite' ).click();
		}

		// Template dialog is displayed with correct content
		cy.get( '.ve-ui-mwTemplateDialog .oo-ui-processDialog-title' )
			.should( 'have.text', 'Webseite' );
		cy.get( '.ve-ui-mwTemplateDialog .ve-ui-mwTemplatePage .oo-ui-labelElement-label' )
			.should( 'have.text', 'Internetquelle' );

		// Add undocumented parameter
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-header' ).click();
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-input' ).type( 'test' );
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-input .oo-ui-actionFieldLayout-button .oo-ui-buttonElement-button' ).click();
		cy.get( '.ve-ui-mwParameterPage-field' ).type( 'test' );
		// Click on insert button
		cy.get( '.ve-ui-mwTemplateDialog .oo-ui-processDialog-actions-primary .oo-ui-buttonElement-button' ).click();

		// Save changes
		veHelper.saveEdits();

		// Ref has been added to references section and has correct content
		helper.getRefFromReferencesSection( 1 ).find( '.reference-text' ).should( 'have.text', 'Template:Internetquelle' );
	} );
} );
