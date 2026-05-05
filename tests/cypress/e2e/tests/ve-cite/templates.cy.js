require( '@cypress/skip-test/support' );
import * as helper from './../../utils/functions.helper.js';
import * as veHelper from './../../utils/ve.helper.js';

const expectedWikiText = '<ref>{{Internetquelle|foo=bar}}</ref>';

let usesCitoid;
let title;

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

		title = helper.getTestString( 'CiteTest-wt2017' );
	} );

	it( 'should be able to add a new template in VE', () => {
		veHelper.openVEForEditingReferences( title, usesCitoid );

		if ( usesCitoid ) {
			cy.get( '.ve-ui-toolbar-group-citoid' ).click();

			cy.get( '.oo-ui-tabSelectWidget' ).should( 'be.visible' );
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
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-input' ).type( 'foo' );
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-input .oo-ui-actionFieldLayout-button .oo-ui-buttonElement-button' ).click();
		cy.get( '.ve-ui-mwParameterPage-field' ).type( 'bar' );
		// Click on insert button
		cy.get( '.ve-ui-mwTemplateDialog .oo-ui-processDialog-actions-primary .oo-ui-buttonElement-button' ).click();

		// Switch to WikiText Editor
		cy.get( '#p-views #ca-edit' ).click();

		// Assert that the Wikitext contains Main+Details Ref with updated details content
		cy.get( 'textarea[name="wpTextbox1"]' )
			.should( 'contain.value', expectedWikiText );
	} );

	it( 'should be able to add a new template in VE WT2017 Editor', () => {
		veHelper.openVEForSourceEditingReferences( title, usesCitoid );

		if ( usesCitoid ) {
			cy.get( '.ve-ui-toolbar-group-citoid' ).click();
			cy.get( '.oo-ui-tabSelectWidget .oo-ui-labelElement-label', { timeout: 5000 } ).should( 'be.visible' ).contains( 'Manual' ).click();
			cy.get( '.oo-ui-labelElement-label' ).contains( 'Webseite' ).click();
		} else {
			cy.get( '.ve-ui-toolbar-group-cite' ).click();
			cy.get( '.oo-ui-popupToolGroup-active-tools .oo-ui-tool-title', { timeout: 5000 } ).should( 'be.visible' ).contains( 'Webseite' ).click();
		}

		// Template dialog is displayed with correct content
		cy.get( '.ve-ui-mwTemplateDialog .oo-ui-processDialog-title' )
			.should( 'have.text', 'Webseite' );
		cy.get( '.ve-ui-mwTemplateDialog .ve-ui-mwTemplatePage .oo-ui-labelElement-label' )
			.should( 'have.text', 'Internetquelle' );

		// Add undocumented parameter
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-header' ).click();
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-input' ).type( 'foo' );
		cy.get( '.ve-ui-mwTransclusionDialog-addParameterFieldset-input .oo-ui-actionFieldLayout-button .oo-ui-buttonElement-button' ).click();
		cy.get( '.ve-ui-mwParameterPage-field' ).type( 'bar' );
		// Click on insert button
		cy.get( '.ve-ui-mwTemplateDialog .oo-ui-processDialog-actions-primary .oo-ui-buttonElement-button' ).click();

		// Ref tag with template and added parameter has been created
		cy.get( '.ve-ui-mwWikitextSurface' ).should( 'contain.text', expectedWikiText );
	} );

} );
