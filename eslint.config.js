'use strict';

const { defineConfig } = require( 'eslint/config' );
const wikimedia = require( 'eslint-config-wikimedia/flat' );
const cypress = require( 'eslint-plugin-cypress/flat' );

// Directories with their own config, which other configs don't apply to.
// These were directories with `"root": true` in .eslintrc.json.
const rootDirs = {
	modules: [ 'modules/**' ],
	cypress: [ 'tests/cypress/**' ],
	qunit: [ 'tests/qunit/**' ]
};

module.exports = defineConfig( [
	// Gruntfile.js, etc.
	{
		ignores: Object.values( rootDirs ).flat(),
		extends: [ wikimedia( 'server' ) ]
	},

	// Code which runs in MediaWiki
	{
		files: rootDirs.modules,
		extends: [ wikimedia( 'client', 'jquery', 'mediawiki' ) ],
		rules: {
			'no-jquery/no-done-fail': 'off'
		}
	},
	{
		files: [ 'modules/**/*.vue' ],
		extends: [ wikimedia( 'client/common', 'vue3/es6' ) ],
		rules: {
			'max-len': 'off',
			'template-curly-spacing': 'off',
			'vue/component-name-in-template-casing': [
				'error',
				'kebab-case'
			],
			'vue/max-len': [
				'error',
				{
					code: 120,
					template: 120,
					comments: 120
				}
			]
		}
	},
	{
		files: [ 'modules/ext.cite.referencePreviews/**' ],
		languageOptions: {
			sourceType: 'commonjs',
			globals: {
				global: 'readonly',
				'ext.popups': 'readonly'
			}
		}
	},
	{
		files: [ 'modules/ve-cite/**' ],
		languageOptions: {
			globals: {
				ve: 'readonly'
			}
		},
		rules: {
			'max-len': [ 'error', 120 ]
		}
	},
	{
		files: [ 'modules/ve-cite/i18n/*.json' ],
		rules: {
			'max-len': 'off'
		}
	},

	// Tests
	{
		files: rootDirs.cypress,
		extends: [ wikimedia( 'server' ), cypress.configs.recommended ],
		languageOptions: {
			// Cypress sets 2019, but eslintrc used the server config's 2022
			ecmaVersion: 2022,
			sourceType: 'module'
		}
	},
	{
		files: rootDirs.qunit,
		extends: [ wikimedia( 'client/common', 'language/es2018', 'jquery', 'mediawiki', 'qunit' ) ],
		rules: {
			'no-jquery/no-class-state': 'off'
		}
	},
	{
		files: [ 'tests/qunit/ve-cite/**' ],
		languageOptions: {
			globals: {
				sinon: 'readonly',
				ve: 'readonly'
			}
		}
	}
] );
