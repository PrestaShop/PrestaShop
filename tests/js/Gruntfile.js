/* jshint node: true */
module.exports = function (grunt) {
	"use strict";
	
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		jshint: {
			all: [
			      "../../js/cldr.js",
			      "spec/**/*.js",
			],
			options: {
				jshintrc: '.jshintrc',
			},
		},
		jasmine: {
			src: [
			      "../../js/cldr.js",
			],
			options: {
				specs: "spec/**/*.js",
				vendor: [
				         "../../js/jquery/jquery-1*.min.js",
				         "../../js/vendor/node_modules/**/dist/**/*.js",
				         "!../../js/vendor/node_modules/**/dist/**/node[_-]main.js",
				]
			},
		}
	});
	
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-jasmine');
	grunt.registerTask('test', ['jasmine', 'jshint']);
	grunt.registerTask('default', ['test']);
	
}