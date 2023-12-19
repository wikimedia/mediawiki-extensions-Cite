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
