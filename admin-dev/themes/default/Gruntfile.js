module.exports = function(grunt) {
    grunt.initConfig({
        copy: {
            main: {
                files: [{
                    expand: true,
                    cwd: 'node_modules/PrestaKit/dist/js',
                    src: '*.js',
                    dest: 'js'
                },
								{
										expand: true,
										cwd: 'node_modules/PrestaKit/dist/css',
										src: '*.css',
										dest: 'css'
								},
								{
										expand: true,
										cwd: 'node_modules/PrestaKit/dist/fonts',
										src: '*',
										dest: 'fonts'
								}]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.registerTask('default', ['copy']);
};
