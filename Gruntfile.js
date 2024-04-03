/*!
 * Grunt file
 *
 * @package Cite
 */

'use strict';

module.exports = function ( grunt ) {
	const conf = grunt.file.readJSON( 'extension.json' );

	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.loadNpmTasks( 'grunt-eslint' );
	grunt.loadNpmTasks( 'grunt-stylelint' );

	grunt.initConfig( {
		eslint: {
			options: {
				cache: true,
				fix: grunt.option( 'fix' )
			},
			all: [
				'**/*.{js,json}',
				'!{docs,vendor,node_modules}/**',
				'!tests/cypress/screenshots/**/*.js'
			]
		},
		banana: conf.MessagesDirs,
		stylelint: {
			all: [
				'**/*.css',
				'**/*.less',
				'!{docs,node_modules,vendor}/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'eslint', 'stylelint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
