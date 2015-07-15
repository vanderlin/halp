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
          '<%= paths.assets.vendor %>bootstrap/dist/js/bootstrap.js',
          '<%= paths.assets.js %>frontend.js'
        ],
        dest: '<%= paths.js %>frontend.js',
      },
      js_backend: {
        src: [
          '<%= paths.assets.vendor %>jquery/dist/jquery.js',
          '<%= paths.assets.vendor %>jquery-ui/jquery-ui.js',
          '<%= paths.assets.vendor %>bootstrap/dist/js/bootstrap.js',
          '<%= paths.assets.js %>backend.js'
        ],
        dest: '<%= paths.js %>backend.js',
      }
    },  


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
          }

        },
        email: {
          options: {
            paths: ["<%= paths.css %>"],
            cleancss: true,
            modifyVars: {
            }
          },
          files: {
            "<%= paths.css %>core/email.css": "<%= paths.assets.less %>email.less",
          }
        },
        bootstrap: {
          options: {
            paths: ["<%= paths.css %>"],
            cleancss: true,
            modifyVars: {
            }
          },
          files: {
            "<%= paths.css %>core/bootstrap.css": "<%= paths.assets.less %>bootstrap.less",
          }
        },
        bootstrap_2: {
          options: {
            paths: ["<%= paths.css %>"],
            cleancss: true,
            modifyVars: {
            }
          },
          files: {
            "<%= paths.css %>core/bootstrap_2.css": "<%= paths.assets.less %>/bootstrap/bootstrap_2.less",
          }
        }
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
        tasks: ['less:core'],                            //tasks to run
        options: {
          livereload: true                               //reloads the browser
        }
      },
      less_bootstrap: {
        files: ['<%= paths.assets.less %>bootstrap.less'],  
        tasks: ['less:bootstrap'], 
        options: {
          livereload: true              
        }
      },
      less_bootstrap: {
        files: ['<%= paths.assets.less %>email.less'],  
        tasks: ['less:email'], 
        options: {
          livereload: true              
        }
      },
      less_bootstrap_2: {
        files: ['<%= paths.assets.less %>/bootstrap/*.less'],  
        tasks: ['less:bootstrap_2'], 
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

};