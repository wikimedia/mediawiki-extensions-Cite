const Page = require( 'wdio-mediawiki/Page' );

class CitePage extends Page {
	getReference( num ) { return browser.elements( '#mw-content-text .reference' ).value[ num - 1 ]; }
	getCiteBacklink( num ) { return browser.element( '.references li:nth-of-type(' + num + ') .mw-cite-up-arrow-backlink' ); }
	getCiteSubBacklink( num ) { return browser.element( '.mw-cite-backlink sup:nth-of-type(' + num + ') a' ); }

	getFragmentFromLink( linkElement ) {
		// the href includes the full url so slice the fragment from it
		let href = linkElement.getAttribute( 'href' );
		return href.slice( href.indexOf( '#' ) + 1 );
	}
}

module.exports = new CitePage();
