const Page = require( 'wdio-mediawiki/Page' );

class VersionPage extends Page {
	get extension() { return browser.element( '#mw-version-ext-parserhook-Cite' ); }

	open() {
		super.openTitle( 'Special:Version' );
	}
}

module.exports = new VersionPage();
