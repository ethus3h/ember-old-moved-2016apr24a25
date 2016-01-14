module.exports = function(grunt) {
    grunt.loadNpmTasks('grunt-uncss');
    grunt.initConfig({
        uncss: {
            dist: {
                src: ['index.html'],
                dest: 'md.css',
                options: {
                    report: 'min' // optional: include to report savings
                }
            }
        }
    });
    grunt.registerTask('default', ['uncss']);
};