var assert = require( 'assert' ),
	Api = require( 'wdio-mediawiki/Api' ),
	CitePage = require( '../pageobjects/cite.page' ),
	Util = require( 'wdio-mediawiki/Util' ),
	VersionPage = require( '../pageobjects/version.page' );

describe( 'Cite', function () {
	var title;

	before( function () {
		title = Util.getTestString( 'CiteTest-title-' );

		browser.call( function () {
			return Api.edit(
				title,
				'abc<ref name="a">reftext</ref>\n\ndef<ref name="a" />\n\n<references />'
			);
		} );
	} );

	it( 'is configured correctly', function () {
		VersionPage.open();
		assert( VersionPage.extension.isExisting() );
	} );

	it( 'highlights the jump mark symbol in the reference list', function () {
		CitePage.openTitle( title );

		browser.pause( 300 ); // make sure JS is loaded

		CitePage.getCiteLink( 2 ).click();
		assert(
			CitePage.getCiteSubBacklink( 2 ).getAttribute( 'class' )
				.indexOf( 'mw-cite-targeted-backlink' ) !== -1,
			'the jump mark symbol of the backlink is highlighted'
		);
	} );
} );
