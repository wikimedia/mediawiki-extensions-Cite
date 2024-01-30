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
