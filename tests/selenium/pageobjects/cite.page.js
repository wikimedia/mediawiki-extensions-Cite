const Page = require( 'wdio-mediawiki/Page' );

class CitePage extends Page {
	getCiteLink( num ) { return browser.elements( '#mw-content-text .reference a' ).value[ num - 1 ]; }
	getCiteSubBacklink( num ) { return browser.element( '.mw-cite-backlink sup:nth-of-type(' + num + ') a' ); }
}

module.exports = new CitePage();
