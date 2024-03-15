export function waitForVEToLoad() {
	cy.get( '.ve-init-mw-desktopArticleTarget-toolbar-open', { timeout: 7000 } )
		.should( 'be.visible' );
}

export function getMWSuccessNotification() {
	return cy.get( '.mw-notification-visible .oo-ui-icon-success' );
}

export function getReference( num ) {
	return cy.get( `#mw-content-text .reference:nth-of-type(${ num })` );

}

export function getCiteSubBacklink( num ) {
	return cy.get( `.mw-cite-backlink sup:nth-of-type(${ num }) a` );
}

export function getCiteMultiBacklink( num ) {
	return cy.get( `.references li:nth-of-type(${ num }) .mw-cite-up-arrow-backlink` );
}

export function getCiteSingleBacklink( num ) {
	return cy.get( `.references li:nth-of-type(${ num }) .mw-cite-backlink a` );
}

export function getFragmentFromLink( linkElement ) {
	return linkElement.invoke( 'attr', 'href' ).then( ( href ) => {
		return href.split( '#' )[ 1 ];
	} );
}
export function getVEFootnoteMarker( refName, sequenceNumber, index ) {
	return cy.get( `sup.ve-ce-mwReferenceNode#cite_ref-${ refName }_${ sequenceNumber }-${ index - 1 }` );
}

export function getVEReferencePopup() {
	return cy.get( '.oo-ui-popupWidget-popup .ve-ui-mwReferenceContextItem .mw-content-ltr' );
}

export function getVEDialog() {
	return cy.get( '.oo-ui-dialog-content .oo-ui-fieldsetLayout .ve-ui-mwTargetWidget .ve-ce-generated-wrapper' );
}

export function openVECiteReuseDialog() {
	cy.contains( '.oo-ui-labelElement-label', 'Cite' ).click();
	cy.get( '.oo-ui-tool-name-reference-existing > a.oo-ui-tool-link' )
		.contains( 'Re-use' ).click();
}

export function getVEUIToolbarSaveButton() {
	return cy.get( '.ve-ui-toolbar-saveButton' );
}

export function getSaveChangesDialogConfirmButton() {
	return cy.contains( '.oo-ui-labelElement-label', 'Save changes' );
}

export function getCiteReuseDialogRefWidget( rowNumber ) {
	return cy.get( '.ve-ui-mwReferenceSearchWidget .oo-ui-selectWidget .ve-ui-mwReferenceResultWidget' ).eq( rowNumber - 1 );
}

export function getCiteReuseDialogRefName( rowNumber ) {
	return cy.get( '.oo-ui-widget.oo-ui-widget-enabled .ve-ui-mwReferenceResultWidget .ve-ui-mwReferenceSearchWidget-name' ).eq( rowNumber - 1 );
}

export function getCiteReuseDialogRefNumber( rowNumber ) {
	return cy.get( '.oo-ui-widget.oo-ui-widget-enabled .ve-ui-mwReferenceResultWidget .ve-ui-mwReferenceSearchWidget-citation' ).eq( rowNumber - 1 );
}

export function getCiteReuseDialogRefText( rowNumber ) {
	return cy.get( '.oo-ui-widget.oo-ui-widget-enabled .ve-ui-mwReferenceResultWidget .ve-ce-paragraphNode' ).eq( rowNumber - 1 );
}

export function backlinksIdShouldMatchFootnoteId( supIndex, backlinkIndex, rowNumber ) {
	return cy.get( '#mw-content-text p sup' )
		.eq( supIndex )
		.invoke( 'attr', 'id' )
		.then( ( id ) => {
			getRefFromReferencesSection( rowNumber )
				.find( '.mw-cite-backlink a' )
				.eq( backlinkIndex )
				.invoke( 'attr', 'href' )
				.should( 'eq', `#${ id }` );
		} );
}

// Article Section
export function getRefsFromArticleSection() {
	return cy.get( '#mw-content-text p sup' );
}

export function articleSectionRefMarkersContainCorrectRefName( refMarkerContent ) {
	return getRefsFromArticleSection()
		.find( `a:contains('[${ refMarkerContent }]')` ) // Filter by refMarkerContent
		.each( ( $el ) => {
			cy.wrap( $el )
				.should( 'have.text', `[${ refMarkerContent }]` )
				.and( 'have.attr', 'href', `#cite_note-a-${ refMarkerContent }` );
		} );
}

// References Section
export function getRefsFromReferencesSection() {
	return cy.get( '#mw-content-text .references li' );
}

export function getRefFromReferencesSection( rowNumber ) {
	return cy.get( `#mw-content-text .references li:eq(${ rowNumber - 1 })` );
}

export function referenceSectionRefIdContainsRefName( rowNumber, refName ) {
	const id = refName !== null ? `cite_note-${ refName }-${ rowNumber }` : `cite_note-${ rowNumber }`;
	return getRefFromReferencesSection( rowNumber ).should( 'have.attr', 'id', id );
}

export function verifyBacklinkHrefContent( refName, rowNumber, index ) {
	const expectedHref = `#cite_ref-${ refName }_${ rowNumber }-${ index }`;
	return getRefFromReferencesSection( rowNumber )
		.find( '.mw-cite-backlink a' )
		.eq( index )
		.should( 'have.attr', 'href', expectedHref );
}

export function abandonReference( id ) {
	cy.get( `:not(.reference-text) > #${ id } a` )
		.trigger( 'mouseout' );
	// Wait for the 300ms default ABANDON_END_DELAY.
	// eslint-disable-next-line cypress/no-unnecessary-waiting
	cy.wait( 500 );
}

export function dwellReference( id ) {
	cy.get( `:not(.reference-text) > #${ id } a` )
		.trigger( 'mouseover' );
}

export function assertPreviewIsScrollable( isScrollable ) {
	cy.get( '.mwe-popups-extract .mwe-popups-scroll' )
		.should( ( $el ) => isScrollable === ( $el.prop( 'scrollHeight' ) > $el.prop( 'offsetHeight' ) ) );
}
