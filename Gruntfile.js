//Gruntfile
module.exports = function(grunt) {

//Initializing the configuration object
grunt.initConfig({

    // Paths variables
    paths: {


      // Development where put LESS files, etc
      assets: {
        less: 'httpdocs/assets/less/',
        css: 'httpdocs/assets/css/',
        js: 'httpdocs/assets/js/src/',
        vendor: 'httpdocs/assets/vendor/'
      },

      // Production where Grunt output the files      
      css: 'httpdocs/assets/css/',
      js: 'httpdocs/assets/js/'

    },

    // Task configuration
    concat: {  
      options: {
        separator: ';',
      },
      js_frontend: {
        src: [
          '<%= paths.assets.vendor %>jquery/dist/jquery.js',
          '<%= paths.assets.vendor %>jquery-ui/jquery-ui.js',
          '<%= paths.assets.js %>frontend.js'
        ],
        dest: '<%= paths.js %>frontend.js',
      },
      js_backend: {
        src: [
          '<%= paths.assets.vendor %>jquery/dist/jquery.js',
          '<%= paths.assets.vendor %>jquery-ui/jquery-ui.js',
          '<%= paths.assets.js %>backend.js'
        ],
        dest: '<%= paths.js %>backend.js',
      }
    },  

    // ------------------------------------------------------------------------
    less: {
        core: {
          options: {
            paths: ["<%= paths.css %>"],
            cleancss: true,
            plugins: [
              new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]})
            ]
          },
          files: {
            "<%= paths.css %>frontend/frontend.css": "<%= paths.assets.less %>frontend.less",
            "<%= paths.css %>backend/backend.css": "<%= paths.assets.less %>backend.less",
            "<%= paths.css %>core/api.css": "<%= paths.assets.less %>api.less",
            "<%= paths.css %>core/email.css": "<%= paths.assets.less %>email.less",
          }

        },
        api: {
          options: {
            paths: ["<%= api.css %>"],
            cleancss: true,
            modifyVars: {
            }
          },
          files: {
            "<%= paths.css %>core/api.css": "<%= paths.assets.less %>api.less",
          }
        },
    },  



    uglify: {
      options: {
        mangle: false  // Use if you want the names of your functions and variables unchanged
      },
      frontend: {
        files: {
          '<%= paths.js %>frontend.min.js': '<%= paths.js %>frontend.js',
        }
      },
      backend: {
        files: {
          '<%= paths.js %>backend.min.js': '<%= paths.js %>backend.js',
        }
      },
    },  
      
    watch: {
      less: {
        files: ['<%= paths.assets.less %>*.less', '<%= paths.assets.less %>/mobile/*.less'],       //watched files
        tasks: ['less:core'],                            
        options: {
          livereload: true                               
        }
      }
    }  
});

  // Plugin loading
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  

  // Task definition
  grunt.registerTask('default', ['watch']);
  grunt.registerTask('build', ['less:core']);
};