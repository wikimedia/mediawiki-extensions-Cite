import * as helpers from './functions.helper.js';

export function openVEForEditingReferences( title, usesCitoid ) {
	helpers.visitTitle( title, { veaction: 'edit' } );
	waitForVECiteToLoad();
	if ( usesCitoid ) {
		waitForVECitoidToLoad();
	}
}

export function waitForVECiteToLoad() {
	cy.get( '.ve-init-mw-desktopArticleTarget-toolbar-open', { timeout: 7000 } )
		.should( 'be.visible' );
	cy.window()
		.should( 'have.property', 'mw' )
		.and( 'have.property', 'loader' )
		.and( 'have.property', 'getState' );
	cy.window()
		.should(
			( win ) => win.mw.loader.getState( 'ext.cite.visualEditor' ) === 'ready'
		);
}

export function waitForVECitoidToLoad() {
	cy.window()
		.should( 'have.property', 'mw' )
		.and( 'have.property', 'loader' )
		.and( 'have.property', 'getState' );
	cy.window()
		.should(
			( win ) => win.mw.loader.getState( 'ext.citoid.visualEditor' ) === 'ready'
		);
}

export function getVEFootnoteMarker( refName, sequenceNumber, index ) {
	return cy.get( `sup.ve-ce-mwReferenceNode#cite_ref-${ refName }_${ sequenceNumber }-${ index - 1 }` );
}

export function getVEReferenceContextItem() {
	return cy.get( '.ve-ui-context-menu .ve-ui-mwReferenceContextItem' );
}

export function getVEReferenceContextItemEdit() {
	return cy.get( '.ve-ui-context-menu .ve-ui-mwReferenceContextItem .oo-ui-buttonElement-button' );
}

export function getVEReferenceEditDialog() {
	return cy.get( '.ve-ui-mwReferenceDialog' );
}

export function openVECiteReuseDialog() {
	helpers.clickUntilVisible(
		cy.get( '.ve-ui-toolbar-group-cite' ),
		'.ve-ui-toolbar .oo-ui-tool-name-reference-existing'
	);
	cy.get( '.ve-ui-toolbar .oo-ui-tool-name-reference-existing' ).click();
}

export function openVECiteoidReuseDialog() {
	cy.get( '.ve-ui-toolbar-group-citoid' ).click();
	// TODO: Sometimes enabling the tab does not work right away.
	// eslint-disable-next-line cypress/no-unnecessary-waiting
	cy.wait( 500 );
	cy.get( '.oo-ui-labelElement-label' ).contains( 'Re-use' ).click();
}

export function getVEUIToolbarSaveButton() {
	return cy.get( '.ve-ui-toolbar-saveButton' );
}

export function getSaveChangesDialogConfirmButton() {
	return cy.get( '.ve-ui-mwSaveDialog .oo-ui-processDialog-actions-primary .oo-ui-buttonWidget' );
}

export function getCiteReuseDialogRefResult( rowNumber ) {
	return cy.get( '.ve-ui-mwReferenceSearchWidget .ve-ui-mwReferenceResultWidget' )
		.eq( rowNumber - 1 );
}

export function getCiteReuseDialogRefResultName( rowNumber ) {
	return cy.get( '.ve-ui-mwReferenceSearchWidget .ve-ui-mwReferenceResultWidget .ve-ui-mwReferenceSearchWidget-name' )
		.eq( rowNumber - 1 );
}

export function getCiteReuseDialogRefResultCitation( rowNumber ) {
	return cy.get( '.ve-ui-mwReferenceSearchWidget .ve-ui-mwReferenceResultWidget .ve-ui-mwReferenceSearchWidget-citation' )
		.eq( rowNumber - 1 );
}

export function getCiteReuseDialogRefText( rowNumber ) {
	return cy.get( '.oo-ui-widget.oo-ui-widget-enabled .ve-ui-mwReferenceResultWidget .ve-ce-paragraphNode' )
		.eq( rowNumber - 1 );
}
