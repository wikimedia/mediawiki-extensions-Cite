var assert = require( 'assert' ),
	VersionPage = require( '../pageobjects/version.page' );

describe( 'Cite', function () {
	it( 'is configured correctly', function () {
		VersionPage.open();
		assert( VersionPage.extension.isExisting() );
	} );
} );
