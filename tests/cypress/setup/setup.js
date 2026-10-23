/* eslint-env node */
// Write the wiki-wide templates and JSON configurations once, before the
// parallel workers start. A plain API client is used because a Cypress run
// for two API calls costs one more browser start in CI.

const api = process.env.MW_SERVER + process.env.MW_SCRIPT_PATH + '/api.php';
const cookies = new Map();

// Page name and content fixtures
const citeBookTemplateName = 'Literatur';
const autonameTemplateName = 'Book Autoname Test';
const citeBookTemplate = `
<cite class="book">A book reference: [{{{lastname}}} {{{title}}}]</cite>
<noinclude>
<templatedata>
${ JSON.stringify( { params: { lastname: { type: 'line', suggested: true }, title: { type: 'line', suggested: true } } } ) }
</templatedata>
</noinclude>
`;
const pageFixtures = [
	// Tool definition for Cite templates
	{
		name: 'MediaWiki:Cite-tool-definition.json',
		content: JSON.stringify( [
			{ name: 'web', title: 'Webseite', template: 'Internetquelle' },
			{ name: 'book', title: 'Literatur', template: citeBookTemplateName }
		] )
	},
	{
		name: `Template:${ citeBookTemplateName }`,
		content: citeBookTemplate
	},
	{
		name: `Template:${ autonameTemplateName }`,
		content: '{{{lastname}}}-{{{title}}}-'
	},
	// Mapping of cite to autoname templates
	{
		name: 'MediaWiki:cite-autoname-template-map.json',
		content: JSON.stringify( { [ autonameTemplateName ]: [ citeBookTemplateName ] } )
	},
	// Community Config setting to enable autonames, in case CC is active in test environment
	{
		name: 'MediaWiki:Cite-VisualEditor-Autonames.json',
		content: JSON.stringify( { enable: true, $version: '1.0.0' } )
	}
];

async function call( params, post = false ) {
	const query = new URLSearchParams( { format: 'json', formatversion: '2', ...params } );
	// fetch is stable in Node 20+; CI runs 24.
	// eslint-disable-next-line n/no-unsupported-features/node-builtins
	const res = await fetch( post ? api : api + '?' + query, {
		method: post ? 'POST' : 'GET',
		body: post ? query : undefined,
		headers: { Cookie: [ ...cookies.values() ].join( '; ' ) }
	} );
	for ( const cookie of res.headers.getSetCookie() ) {
		const pair = cookie.split( ';' )[ 0 ];
		cookies.set( pair.split( '=' )[ 0 ], pair );
	}
	return res.json();
}

async function main() {
	const loginToken = ( await call( { action: 'query', meta: 'tokens', type: 'login' } ) )
		.query.tokens.logintoken;
	const login = await call( {
		action: 'login',
		lgname: process.env.MEDIAWIKI_USER,
		lgpassword: process.env.MEDIAWIKI_PASSWORD,
		lgtoken: loginToken
	}, true );
	if ( login.login.result !== 'Success' ) {
		throw new Error( 'Login failed: ' + JSON.stringify( login ) );
	}

	const csrfToken = ( await call( { action: 'query', meta: 'tokens' } ) ).query.tokens.csrftoken;
	const fixtureResults = await Promise.all( pageFixtures.map( async ( fixture ) => {
		const edit = await call( {
			action: 'edit',
			title: fixture.name,
			text: fixture.content,
			token: csrfToken
		}, true );
		if ( !edit.edit || edit.edit.result !== 'Success' ) {
			throw new Error( 'Edit failed: ' + JSON.stringify( edit ) );
		}
	} ) );
	console.log( `inserted ${ fixtureResults.length } fixtures` );
}

main().catch( ( e ) => {
	console.error( e );
	process.exitCode = 1;
} );
