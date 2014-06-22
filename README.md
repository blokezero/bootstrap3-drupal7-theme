#Bootstrap base theme.
Bootstrap base Theme site theme based on Bootstrap 3.1.1, and as such is a 'mobile first' base theme.

## How to use.
### Dependencies.

  1. You'll need to have node.js, npm and grunt installed.
    To install node just download the installer: http://nodejs.org/download/
    
    This will install node.js and the node.js package manager `npm` which is a command line tool.
    
  2. To install grunt globally type:

        npm install -g grunt-cli

  4. Install all the dependencies by `cd` to base directory for theme and typing
  
    `npm install`
    
  Go to http://gruntjs.com/getting-started#working-with-an-existing-grunt-project for more details.

### Running Grunt to compile the LESS to CSS.
To compile the Less to CSS type `grunt less:dev` or `grunt less:production` to compile it one off or `grunt watch` to have the file auto compile on save.

### Removing unneeded styles
Please note that ALL the Bootstrap styles are included.  If you don't need them all then just comment out the import statements.  Don't commit that change though.

## Creating a sub theme
Do this as usual - see [Creating a sub-theme](https://drupal.org/node/225125).

### Using LESS in a sub theme

I suggest you use the same directory structure as the base theme.  In which case you'll have `assets/less/theme.less` compiling to `assets/css/theme.less`.

#### Initialise the Grunt stuff

Note, installing the notify package has other dependencies you will have to install and you may just want to REMOVE it from both the base_theme and the sub-theme. See https://github.com/dylang/grunt-notify#os-x-notification-system In which case don't bother installing it below and remove the line `grunt.loadNpmTasks('grunt-notify');` from `Gruntfile.js`.

  1. To start a new Grunt project type `npm init` in the root of the sub-theme.
  2. Copy the Gruntfile.js to the sub-theme root.
  3. Install the Grunt packages:
    
        npm install grunt-contrib-less --save-dev
        npm install grunt-contrib-watch --save-dev
        npm install grunt-notify --save-dev
        

#### Using the Boostrap LESS files in a sub theme

If you've set up the folders as suggested then to use the LESS mixins and variables just add this to the top of the .less file

    @import "../../../bauer-base/assets/less/bootstrap/mixins";
    @import "../../../bauer-base/assets/less/bootstrap/variables";