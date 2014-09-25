## Webird full application stack

Webird was created to merge the latest PHP and Node.js innovations into a single application stack.

Phalcon and Composer form the backbone of the PHP side and are used to create a HMVC foundation that offers everything that is expected of a modern PHP server side framework.  Webpack assembles the browser resources and solves many problem associated with modern web development by bringing the Node.js popularized CommonJS module system to the browser.  Node.js is a requirement for development and is no longer required once a project is built.

Webird is hipster forward facing project that is designed to run on a server OS and browser released in 2014 and later.  Currently Ubuntu 14.04 is supported.

#### Key features of Webird:
* HTML5 IE11+
* PHP 5.5+
* PHP [Ratchet](http://socketo.me/) websockets that offer read-only access to the PHP session data
* Google OAuth2 login
* PHP CLI utilities for many tasks
* Manage most third party dependencies with Composer, NPM and Bower
* Single PHP installation script for installing local dependencies or type the commands manually
* Single command that starts various development processes across PHP and Node.js
* [Webpack](http://webpack.github.io/) (CommonJS) build environment
* Live reloading (and waiting) CommonJS build environment
* Mix and match any combination of languages and theming (per file) including; CSS, SCSS, LESS, Stylus, Javascript and Coffeescript
* Complete integration of gettext .po translation data between the PHP and Webpack (Javascript) environments
* Create a final PHP and Javascript source protected distribution for deployment to the server
* AngularJS and Backbone/Marionette Webpack examples

#### Key components of Webird:
* [nginx](http://nginx.org/)
* [Phalcon](http://phalconphp.com/en/)
* [Composer](https://getcomposer.org/) (PHP package manager)
* [npm](https://www.npmjs.org/) (Node.js package manager)
* [Bower](http://bower.io/) (Node.js based package manager specifically for front end resources)
* [Webpack](http://webpack.github.io/) (CommonJS build environment)
* [Ratchet](http://socketo.me/) (PHP websockets)
* [Coffeescript](http://coffeescript.org/)
* [gettext](http://www.gnu.org/software/gettext/gettext.html) (translations)
* [MariaDB](https://mariadb.org/) (MySQL fork)
* [Ion Cube](http://www.ioncube.com/) (optional PHP source protection)

## Installation:
Webird uses several Node.js `npm` packages that must be installed globally with root access.  Webird also requires some PHP extensions that are not usually installed.

##### Install Base Requirements (this will vary across Linux distributions):
Ubuntu 14.04:
```
# Localization
sudo apt-get install poedit

# Node
sudo apt-get install nodejs nodejs-legacy

# PHP
sudo apt-get install gcc php-pear php5-dev php5-mysql libpcre3-dev libzmq3 libzmq3-dev
sudo pecl install zmq
sudo pecl install libevent

# Install Phalcon PHP extension using one method below:

# Phalcon from source
git clone --depth=1 git://github.com/phalcon/cphalcon.git
cd cphalcon/build
sudo ./install

# Phalcon from Ubuntu PPA
sudo apt-add-repository ppa:phalcon/stable
sudo apt-get update
sudo apt-get install php5-phalcon
```

##### Install Requirements (Stage 2):

At this point the initial dependencies have been installed.  Now you have two options for installing the remainder of the extensions.

1) To install the next stage of dependencies you may run the following script:

```
php dev/setup/install.php
```

2) or instead type the commands manually:

```
# Change to your local Webird dev directory
cd dev
# Add execute permissions to Webird dev script for less typing
chmod u+x webird.php

# Composer program download
# It is recommended (but not required) that you install composer globally to /usr/local/bin/composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node NPM package global installs
sudo npm install -g coffee
sudo npm install -g bower
sudo npm install -g gulp
sudo npm install -g xgettext-template

# At this point all of the global utilities have been installed and now you will leverage
# the three package managers npm, bower and composer.  Once the initial global setup has been done
# you will be able to setup a new installation by simply running these three commands;

# Note: do NOT run these commands with root access:
composer install
npm install
bower install
```

#### Configure Localization tools:
Now all of the Webird hard dependencies have been installed but to be able to edit the localization files you will need to configure the GNU gettext frontend [Poedit](http://poedit.net/) translation program.

The advantage to using gettext is that it supports a wide variety of languages, multiple plural forms and it can extract the translation strings from many types of source code and templates.

The Phalcon Volt templates will compile to PHP embedded in HTML (phtml) and recent versions of the command line gettext `xgettext` are able to parse this by default.  However, to allow Handlebar templates Poedit will need to be configured to use the Nodejs xgettext-template program that you installed globally via npm.  To configure Poedit for Handlebar templates you may view the directions on the [xgettext-template](https://github.com/gmarty/xgettext) page or follow the instructions below:

##### Poedit Configuration Instructions:
Go to File - Preferences... in Poedit and add a new parser in the Parsers tab:

![Poedit parser configuration](http://gmarty.github.io/xgettext/Poedit.png)

* Language: `Handlebars`
* List of extensions...: `*.hbs`
* Parser Command: `xgettext-template --force-po -o %o %C %K %F`
* An item in keywords list: `-k %k`
* An item in input files list: `%f`
* Source code charset: `--from-code=%c`

## Usage (Development):
1. Configure `dev.json` with local development settings
2. Create and install nginx configuration: `dev/webird.php nginx > nginx_dev`
3. Import database schema located at `dev/setup/database.sql`
4. Run server processes: `dev/webird.php`
5. Profit

Development files are stored in the `dev` directory and the system is designed so that you should be able to largely forget about this directory once you get into a work flow.  The `app` folder is designed to contain all custom user code and it should never be written to by any automated script or build environment.

**Note**: In order to use the Poedit Update feature to extract gettext translation messages you must first build a `dist` environment.  If you fail to first build the `dist` environment then many strings will show up in the *Obsolete Strings* tab.  If this happens then just hit cancel, but don't worry because regardless the old string translations will be preserved within the .po file.  This is currently required since there is no xgettext Volt/Twig parser and so the compiled Volt templates must be accessed for these strings.

## Usage (Distribution):

#### Create dist environment:
1. Configure `./dist.json` with final development settings to override settings from `./dev/setup/dist/config_defaults.json`.  These two files will be merged to form `./dist/config.json`.
2. Create the dist environment: `./dev/webird.php build`

#### Configure final dist environment:

**Warning**: At this point it will be assumed that you are inside of the portable `dist` directory wherever it is now located (or named)

1. Create and install (location dependent) nginx configuration: `./webird.php nginx > nginx_dist`
2. Import database schema located at `./setup/database.sql`
3. Run server processes: `./webird.php`
4. If something is wrong modify `./config.json` and repeated steps 1-3.  To make changes more permanent for dist releases you may go back and modify the original `dist.json` file and then rebuild the dist environment.

The nginx configuration must be rebuilt if the distribution environment directory is moved or renamed.  It is recommended to use the `webird.php nginx` command to rebuild the configuration instead of manually editing the generated nginx configuration.  If custom settings are required it is recommended to first modify the `./setup/nginx_template` file.

**Note**: Node.js is no longer a dependency at this point since it is only used to build the browser facing content into static bundles.

## Project Structure:

The `app` directory:
```
- locale (contains the gettext locale .po files uses by Phalcon and Webpack)
- public (the web server root is pointed here.  The files here should be minimal and call bootstrap files at a lower level)
- theme (theme files to be read as-is and also processed by Webpack)
- phalcon
  - ...
- webpack
  - commons (common code to be run by multiple entry points)
  - entries (specific code entry points)
  - helpers (global helper modules that export a single function)
  - modules (custom commonjs modules)
```

Compare the `app` directory to a built `dist` directory to notice the differences between the code that you will be working with and the final production output.  You may also view the build system routine at `app/phalcon/modules/cli/tasks/DevTask.php`

**Note**: The `dist` directory does not contain any Node.js/Webpack related code and everything is in a finalized, optimized and protected form.  If Ion Cube has been enabled then the build process will use it to protect the PHP code.

### TODO and the WAITING:
At the moment only basic websocket support is supported since [Ratchet](http://socketo.me/) does not support the [WAMP](http://wamp.ws/) v2 protocol and newer Javascript libraries such as [Autobahn|JS](http://autobahn.ws/js/) are now WAMP v2 only and the older v1 versions don't play nice with the CommonJS module system.  Ratchet development stalled out with the WAMP v2 feature, but there is hope since the [Thruway](https://github.com/voryx/Thruway) team is building upon the Ratchet code base and is hard at work to suport a WAMP v2 protocol.  There is much colloborative and blessings between the two projects so this looks positive.

