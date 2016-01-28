/* jshint node: true */

// Put here src files that we want to use for jasmine and jshint.
var strictSrc = [
	"../../js/cldr.js",
	"../../admin-dev/themes/default/js/bundle/**/*.js",
];

// Put here src files that we want to use with jasmine, but do not want to validate under jshint
var legacySrc = ["../../js/tools.js"
                 
    ];

// Put here all jasmine helpers files
var helpers = [ "helper/**/*.js"
                
    ];

// Put here all jasmine specs files
var specs = ["spec/**/*.js"
             
    ];

// Put here all vendor libraries to include
var vendor = ["../../js/jquery/jquery-1*.min.js",
              "node_modules/jquery-mockjax/dist/jquery.mockjax.min.js",
              /* From here, must load these files in the right order to work properly */
	              "../../js/vendor/node_modules/cldrjs/dist/**/*.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize/message.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize/number.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize/plural.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize/currency.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize/date.js",
	              "../../js/vendor/node_modules/globalize/dist/globalize/relative-time.js",
              /* to here */
              "../../js/vendor/node_modules/**/dist/**/*.js",
              "!../../js/vendor/node_modules/**/dist/**/node[_-]main.js"
    ];

module.exports = function (grunt) {
	"use strict";
	
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		jshint: {
			all: strictSrc.concat(helpers).concat(specs),
			options: {
				jshintrc: '.jshintrc',
				force: false
			}
		},
		jasmine: {
			short: {
				src: strictSrc.concat(legacySrc),
				options: {
					specs: specs,
					helpers: helpers,
					vendor: vendor,
					display: 'short'
				}
			},
			def: {
				src: strictSrc.concat(legacySrc),
				options: {
					specs: specs,
					helpers: helpers,
					vendor: vendor
				}
			},
			verbose: {
				src: strictSrc.concat(legacySrc),
				options: {
					specs: specs,
					helpers: helpers,
					vendor: vendor,
					captureExceptions: true,
					summary: true
				}
			}
		}
	});
	
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-jasmine');
	grunt.registerTask('test', ['jasmine:def', 'jshint']);
	grunt.registerTask('test-short', ['jasmine:short', 'jshint']);
	grunt.registerTask('test-verbose', ['jasmine:verbose', 'jshint']);
	grunt.registerTask('default', ['test']);
	
}