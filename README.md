#Bootstrap base theme.
Bootstrap base Theme site theme based on Bootstrap 3.1.1, and as such is a 'mobile first' base theme.

## How to use.

### 1.Creating a sub theme
Do this as usual - see [Creating a sub-theme](https://drupal.org/node/225125).

### 2. Set up to compile LESS

I suggest you use the same directory structure as the base theme.  In which case you'll have `assets/less/theme.less` compiling to `assets/css/theme.less`.

  1. Copy `Gruntfule.js` and `package.json` to your sub-theme.  Edit `Gruntfule.js` to reflect the sub-theme directory structure (see above).
  2. You'll need to have node.js, npm and grunt installed.
    To install node just download the installer: http://nodejs.org/download/

    This will install node.js and the node.js package manager `npm` which is a command line tool.

  3. To install grunt globally type:

        npm install -g grunt-cli

  4. Install all the dependencies by `cd` to base directory for sub-theme and type:

    `npm install`

  Go to [http://gruntjs.com/](http://gruntjs.com/getting-started#working-with-an-existing-grunt-project) for more details.

  5. If you've set up the folders as suggested then to use the LESS mixins and variables just add this to the top of the .less file

        @import "../../../bootstrap_base/assets/less/bootstrap/mixins";
        @import "../../../bootstrap_base/assets/less/bootstrap/variables";

### 3. Run Grunt to compile the LESS to CSS.
To compile the Less to CSS type `grunt less:dev` or `grunt less:production` to compile it one off or `grunt watch` to have the file auto compile on save.

### Removing unneeded styles
Please note that not ALL the Bootstrap modules are included.  If you don't need them all, or want some more then comment/uncomment as needed, or even better include them in the sub-theme like this:

    @import "../../../bootstrap_base/assets/less/bootstrap/carousel";

