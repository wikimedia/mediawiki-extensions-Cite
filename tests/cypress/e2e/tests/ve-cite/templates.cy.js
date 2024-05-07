import * as helpers from './../../utils/functions.helper.js';

function getTestString( prefix = 'CiteTest-templates' ) {
	return prefix;
}
const title = getTestString( 'CiteTest-title' );

const wikiText1 = 'This is reference #1:';
const refText1 = 'This is citation #1 for reference #1';

const wikiText = `${ wikiText1 } <ref name="a">${ refText1 }</ref><br> ` +
	'<references />';

describe( 'Re-using refs in Visual Editor using templates', () => {

	before( () => {
		cy.clearCookies();
		helpers.loginAsAdmin();

		cy.window().should( 'have.property', 'mw' ).and( 'have.property', 'loader' ).and( 'have.property', 'using' );
		cy.window().then( async ( win ) => {
			await win.mw.loader.using( 'mediawiki.api' );
			const response = await new win.mw.Api().postWithEditToken( {
				action: 'edit',
				title: title,
				text: wikiText,
				formatversion: '2'
			} );

			expect( response.edit.result ).to.equal( 'Success' );

			await new win.mw.Api().postWithEditToken( {
				action: 'edit',
				title: 'MediaWiki:Cite-tool-definition.json',

				text: JSON.stringify(
					[
						{
							name: 'Webseite',
							icon: 'ref-cite-web',
							template: 'Internetquelle'
						},
						{
							name: 'Literatur',
							icon: 'ref-cite-book',
							template: 'Literatur'
						}
					] ),
				formatversion: '2'
			} );
			expect( response.edit.result ).to.equal( 'Success' );
			cy.log( 'SUCCESS' );
			// Disable welcome dialog when entering edit mode
			win.localStorage.setItem( 've-beta-welcome-dialog', 1 );
		} );

	} );

	beforeEach( () => {
		helpers.visitTitle( title );
		helpers.waitForMWLoader();
		cy.window().then( async ( win ) => {
			await win.mw.loader.using( 'mediawiki.base' ).then( async function () {
				await win.mw.hook( 'wikipage.content' ).add( function () { } );
			} );
		} );

		// Open VE edit mode
		helpers.visitTitle( title, { veaction: 'edit', vehidebetadialog: 1 } );
		helpers.waitForVEToLoad();
	} );

	it.skip( 'should add a template reference and verify correct content in both saved and edit mode', () => {
		cy.contains( '.mw-reflink-text', '[1]' ).type( '{rightarrow}' );

		cy.contains( '.oo-ui-labelElement-label', 'Cite' ).click();
		cy.contains( '.oo-ui-tool-name-cite-Webseite', 'Webseite' )
			.should( 'be.visible' );
		cy.contains( '.oo-ui-tool-name-cite-Literatur', 'Literatur' )
			.should( 'be.visible' );

		cy.get( '.oo-ui-tool-name-cite-Webseite' ).click();

		// Tempalte dialog is displayed with correct content
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
		cy.get( '.ve-ui-toolbar-saveButton' ).click();
		// Click save changes button
		cy.get( '.ve-ui-mwSaveDialog .oo-ui-processDialog-navigation .oo-ui-flaggedElement-primary .oo-ui-buttonElement-button' ).click();

		// Success notification should be visible
		cy.get( '.mw-notification-visible .oo-ui-icon-success' ).should( 'be.visible' );

		// Ref has been added to references section and has correct content
		helpers.getRefFromReferencesSection( 2 ).find( '.reference-text' ).should( 'have.text', 'Template:Internetquelle' );
	} );
} );
