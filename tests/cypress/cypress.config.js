/* eslint-env node */
const { defineConfig } = require( 'cypress' );

const envLogDir = process.env.LOG_DIR ? process.env.LOG_DIR + '/Cite' : null;

module.exports = defineConfig( {
	e2e: {
		allowCypressEnv: false,
		supportFile: false,
		specPattern: 'tests/cypress/e2e/**/*.cy.js',
		baseUrl: process.env.MW_SERVER + process.env.MW_SCRIPT_PATH,
		mediawikiAdminUsername: process.env.MEDIAWIKI_USER,
		mediawikiAdminPassword: process.env.MEDIAWIKI_PASSWORD,
		// Skip specs whose required extensions aren't loaded, before Cypress
		// boots a browser. Runtime cy.skipOn() in the specs is the fallback.
		async setupNodeEvents( on, config ) {
			try {
				// fetch is stable in Node 20+; CI runs 24.
				// eslint-disable-next-line n/no-unsupported-features/node-builtins
				const res = await fetch( config.baseUrl +
					'/api.php?action=query&meta=siteinfo&siprop=extensions&format=json' );
				const { query } = await res.json();
				if ( !query.extensions.some( ( e ) => e.name === 'Popups' ) ) {
					config.excludeSpecPattern = [ '**/referencePreviews/**' ];
				}
			} catch ( e ) { /* Use what we have */ }
			return config;
		}
	},

	retries: 2,
	defaultCommandTimeout: 5000, // ms; default is 4000ms

	screenshotsFolder: envLogDir || 'tests/cypress/screenshots',
	video: true,
	videosFolder: envLogDir || 'tests/cypress/videos',
	downloadsFolder: envLogDir || 'tests/cypress/downloads'
} );
