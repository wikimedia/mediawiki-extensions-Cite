/*!
 * Grunt file
 *
 * @package Cite
 */

/*jshint node:true */
module.exports = function ( grunt ) {
	'use strict';
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-jscs' );
	grunt.loadNpmTasks( 'grunt-jsonlint' );
	grunt.loadNpmTasks( 'grunt-banana-checker' );
	grunt.initConfig( {
		jshint: {
			options: {
				jshintrc: true
			},
			all: [
				'*.js',
				'{.jsduck,build}/**/*.js',
				'modules/**/*.js'
			]
		},
		banana: {
			core: [ 'i18n/' ]
		},
		jscs: {
			fix: {
				options: {
					fix: true
				},
				src: '<%= jshint.all %>'
			},
			main: {
				src: '<%= jshint.all %>'
			}
		},
		jsonlint: {
			all: [
				'**/*.json',
				'!node_modules/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'jshint', 'jscs:main', 'jsonlint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
