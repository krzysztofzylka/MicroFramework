module.exports = function(grunt) {

    grunt.initConfig({
        concat: {
            js: {
                src: [
                    'resources/jquery.min.js',
                    'resources/jquery-ui.min.js',
                    'src/public/js/*.js',
                    'resources/**/*.js'
                ],
                dest: 'storage/tmp/app.js',
            },
            css: {
                src: [
                    'src/public/css/*.css',
                    'resources/**/*.css',
                    '!src/public/css/tailwind.css'
                ],
                dest: 'storage/tmp/app.css',
            },
        },
        uglify: {
            js: {
                files: {
                    'public/app.js': ['./storage/tmp/app.js']
                }
            }
        },
        cssmin: {
            options: {
                level: {
                    1: {
                        specialComments: 0
                    }
                }
            },
            target: {
                files: [{
                    src: 'storage/tmp/app.css',
                    dest: 'public/app.css',
                },{
                    src: 'storage/tmp/theme.css',
                    dest: 'public/theme.css',
                }]
            }
        },
        shell: {
            tailwind: {
                command: 'npx tailwindcss -i ./src/public/css/tailwind.css -o ./storage/tmp/theme.css'
            },
            tailwind_watch: {
                command: 'npx tailwindcss -i ./src/public/css/tailwind.css -o ./public/theme.css --watch'
            }
        }
    });

    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask(
        'default',
        [
            'concat',
            'uglify',
            'shell:tailwind',
            'cssmin'
        ]
    );

    grunt.registerTask(
        'dev',
        [
            'default',
            'shell:tailwind_watch'
        ]
    );

};