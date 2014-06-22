 module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    less: {
      dev: {
        options: {
          compress: false,
          yuicompress: false,
          optimization: 2,
          sourceMap: true,
          sourceMapFilename: "assets/css/bootstrap_base.css.map",
          sourceMapBasepath: "assets/css/"
        },
        files: {
          "assets/css/bootstrap_base.css": "assets/less/bootstrap_base.less"
        }
      },
      production: {
        options: {
          cleancss: true,
        },
        files: {
          "assets/css/bootstrap_base.css": "assets/less/bootstrap_base.less"
        }
      }
    },
    watch: {
      files: ['assets/less/bootstrap_base.less'],
      tasks: ['less:dev', 'notify'],
    },
    notify: {
      options: {
        message: "Compilation done!",
        title: "Less"
      },
      files: {
        "assets/css/bootstrap_base.css": "assets/less/bootstrap_base.less"
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-watch');
  
  // REMOVE THE LINE BELOW IF YOU CAN'T BE BOTHERED TO INSTALL GROWL
  grunt.loadNpmTasks('grunt-notify');

  grunt.registerTask('default', ['less:production']);

};