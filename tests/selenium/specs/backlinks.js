var assert = require( 'assert' ),
	Api = require( 'wdio-mediawiki/Api' ),
	CitePage = require( '../pageobjects/cite.page' ),
	Util = require( 'wdio-mediawiki/Util' );

describe( 'Cite backlinks', function () {
	var title;

	before( function () {
		title = Util.getTestString( 'CiteTest-title-' );

		browser.call( function () {
			return Api.edit(
				title,
				'abc<ref name="a">reftext</ref>\n\ndef<ref name="a" />\n\n' +
				'ghi<ref>reftext2</ref>\n\n' +
				'<references />'
			);
		} );
	} );

	beforeEach( function () {
		CitePage.openTitle( title );
		browser.pause( 300 ); // make sure JS is loaded
	} );

	it( 'are highlighted in the reference list when there are multiple used references', function () {
		CitePage.getReference( 2 ).click();
		assert(
			CitePage.getCiteSubBacklink( 2 ).getAttribute( 'class' )
				.indexOf( 'mw-cite-targeted-backlink' ) !== -1,
			'the jump mark symbol of the backlink is highlighted'
		);
	} );

	it( 'clickable up arrow is hidden by default when there are multiple backlinks', function () {
		assert(
			!CitePage.getCiteBacklink( 1 ).isVisible(),
			'the up-pointing arrow in the reference line is not linked'
		);
	} );

	it( 'clickable up arrow shows when jumping to multiple used references', function () {
		CitePage.getReference( 2 ).click();
		assert(
			CitePage.getCiteBacklink( 1 ).isVisible(),
			'the up-pointing arrow in the reference line is linked'
		);

		assert.strictEqual(
			CitePage.getFragmentFromLink( CitePage.getCiteBacklink( 1 ) ),
			CitePage.getReference( 2 ).getAttribute( 'id' ),
			'the up-pointing arrow in the reference line is linked to the clicked reference'
		);
	} );

	it( 'use the last clicked target for the clickable up arrow on multiple used references', function () {
		CitePage.getReference( 2 ).click();
		CitePage.getReference( 1 ).click();

		assert.strictEqual(
			CitePage.getFragmentFromLink( CitePage.getCiteBacklink( 1 ) ),
			CitePage.getReference( 1 ).getAttribute( 'id' ),
			'the up-pointing arrow in the reference line is linked to the last clicked reference'
		);
	} );

	it( 'clickable up arrow is hidden when jumping back from multiple used references', function () {
		CitePage.getReference( 2 ).click();
		CitePage.getCiteBacklink( 1 ).click();

		assert(
			!CitePage.getCiteBacklink( 1 ).isVisible(),
			'the up-pointing arrow in the reference line is not linked'
		);
	} );
} );
