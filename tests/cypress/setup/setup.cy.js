import * as helper from './../e2e/utils/functions.helper.js';

it( 'writes MediaWiki:Cite-tool-definition.json', () => {
	helper.loginAsAdmin();
	helper.editPage( 'MediaWiki:Cite-tool-definition.json', JSON.stringify( [
		{ name: 'web', title: 'Webseite', template: 'Internetquelle' },
		{ name: 'book', title: 'Literatur', template: 'Literatur' }
	] ) );
} );
