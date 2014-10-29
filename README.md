## Webird full application stack

Webird was created to merge the latest PHP and Node.js innovations into a single application stack.

The PHP foundation is comprised of Phalcon and Composer, which are used to create a HMVC foundation that offers everything that is expected of a modern PHP server side framework.
The front end resources are bundled with Webpack, which solves many problems associated with modern web development by bringing the Node.js popularized CommonJS module system to the browser.
Node.js is required for development only and is not required once a project has been built.

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
* [Docker](https://www.docker.com/) container

#### Key components of Webird:
* [nginx](http://nginx.org/)
* [Phalcon](http://phalconphp.com/en/)
* [Coffeescript](http://coffeescript.org/)
* [Composer](https://getcomposer.org/) - PHP package manager
* [npm](https://www.npmjs.org/) - Node.js package manager
* [Bower](http://bower.io/) - Node.js based package manager specifically for front end resources
* [Gulp.js](http://gulpjs.com/) - Streaming build system
* [Webpack](http://webpack.github.io/) - CommonJS build environment
* [Ratchet](http://socketo.me/) - PHP websockets
* [gettext](http://www.gnu.org/software/gettext/gettext.html) - Translations
* [MariaDB 10.0](https://mariadb.org/) - MySQL fork
* [Docker](https://www.docker.com/) - Docker container for easy deployment
* [Ion Cube](http://www.ioncube.com/) - optional PHP source protection

### Install Requirements:
* PHP >= 5.5
* MariaDB >= 10.0
* Node.js


**Installation Instructions:**
```
# Ubuntu 14.04

# System provisioning
sudo setup/provision-system.sh ubuntu1404

# Local install of npm, bower and composer packages
setup/install-local-packages.sh

# nginx setup
sudo ./dev/webird.php nginx > /etc/nginx/sites-available/dev.webird.io
sudo ln -s /etc/nginx/sites-available/dev.webird.io /etc/nginx/sites-enabled/dev.webird.io

# /etc/hosts setup
sudo echo "\n127.0.0.1       dev.webird.io" > /etc/hosts

# mariadb setup
# set DB_ROOT_PW to
mysqladmin --user=root --password=DB_ROOT_PW create webird
mysql --user=root --password=DB_ROOT_PW webird < ./etc/setup/schema.sql

```

### Poedit Localization editor:
In order to modify the localization messages you will need to configure the [Poedit](http://poedit.net/) GNU gettext frontend.

The advantage to using gettext is that it supports a wide variety of languages, multiple plural forms and it can extract the translation strings from many types of source code and templates.

The Phalcon Volt templates will compile to phtml format with extension `.volt.php`. Recent versions of `xgettext` are able to parse this by default.
However to allow Handlebar templates Poedit must be configured to use the Node.js/npm globally installed `xgettext-template` program.
To configure Poedit for Handlebar templates you may view the directions on the [xgettext-template](https://github.com/gmarty/xgettext) page or follow the instructions below:

##### Poedit Configuration Instructions:
Go to File - Preferences... in Poedit and add a new parser in the Parsers tab:

![Poedit parser configuration](http://gmarty.github.io/xgettext/Poedit.png)

* Language: `Handlebars`
* List of extensions...: `*.hbs`
* Parser Command: `xgettext-template --force-po -o %o %C %K %F`
* An item in keywords list: `-k %k`
* An item in input files list: `%f`
* Source code charset: `--from-code=%c`

**Note**: In order to use the Poedit Update feature to extract gettext translation messages you must first build a `dist` environment.  If you fail to first build the `dist` environment then many strings will show up in the *Obsolete Strings* tab.  If this happens then just hit cancel, but don't worry because regardless the old string translations will be preserved within the .po file.  This is currently required since there is no xgettext Volt/Twig parser and so the compiled Volt templates must be accessed for these strings.


## Development Usage:

1. Configure `./etc/dev.json` for local database password
2. Create a Webird user with `./dev/webird.php useradd`
3. Run server processes: `./dev/webird.php [server]` and wait until webpack-dev-server has finished building
4. Visit http://dev.webird.io

If you see the local host file not configured page then add `127.0.0.1 dev.webird.io` to your `/etc/hosts` file.

## Distribution Usage:

#### Create dist environment:
1. Configure `./etc/dist.json` to override settings from `./etc/dist_defaults.json`.  These two files will be merged to form `./dist/etc/config.json`.
2. Create the dist environment: `./dev/webird.php build`
3. Visit https://dist.webird.io

At this point you may run Poedit from any locale file in `./dev/locale` to extract all of the gettext strings from the final `./dist` build.

#### Configure final dist environment:

**Warning**: At this point it will be assumed that you are inside of the portable `dist` directory wherever it is now located (or named).

1. Generate nginx configuration with : `./webird.php nginx` and save (by `>` redirection and enable the output).
2. Import database schema located at `./etc/schema.sql`
3. Run server processes: `./webird.php`
4. If something is wrong modify `./config.json` and repeated steps 1-3.  To make changes more permanent for dist releases you may go back and modify the original `dist.json` file and then rebuild the dist environment.

The nginx configuration must be rebuilt if the distribution environment directory is moved or renamed.  It is recommended to use the `webird.php nginx` command to rebuild the configuration instead of manually editing the generated nginx configuration.  If custom settings are required it is recommended to first modify the `./etc/template/nginx_dist` file.

**Note**: Node.js is no longer a dependency at this point since it is only used to build the browser facing content into static bundles.

## Project Structure:

#### `./app`:

```
./app
├── locale/ (contains the gettext locale .po files uses by Phalcon and Webpack)
├── theme/ (theme files to be read as-is and also processed by Webpack)
├── phalcon
│   ├── bootstrap_cli.php
│   ├── bootstrap_web.php
│   ├── common/
│   ├── config/
│   └── modules/
└── webpack
    ├── config.litcoffee (you can make this .json, .js or .coffee)
    ├── commons (common code to be run by multiple entry points)
    ├── entries (specific code entry points)
    └── modules (custom commonjs modules)
```

#### `./dev`:
```
./dev
├── webird.php (CLI entry for dev environment)
├── public/
│   └── index.php (Web entry for dev environment)
├── cmd_overrides/ (dev specific command overrides for CLI interface)
├── packages.json (npm configuration)
├── bower.json (Bower configuration)
├── vendor.json (Composer configuration)
├── gulpfile.coffee (Gulp streaming build system configuration)
├── gulpfile.webpack.coffee (Webpack configuration)
├── node_modules/
├── bower_components/
└── vendor/
```

#### `./dist`:
```
./dist
├── webird.php (CLI entry for dist environment)
├── public/
│   └── index.php (Web entry for dist environment)
├── etc/
├── locale/ (locales in machine readable .mo format)
├── cache-static/
├── phalcon/
└── vendor/ (Composer packages)
```

#### `./setup`:
```
./setup
├── provision-system.sh (takes a parameter $distro to provision system)
├── install-local-packages.sh (installs local packages into ./dev/)
├── distro/ (distro specific scripts for provision-system.sh)
└── functions/ (helpers)
```

Compare the `./app` directory to a built `./dist` directory to notice the differences between the app code and dev environment and the finalized dist environment.

You may also view the build system routine at `app/phalcon/modules/cli/tasks/DevTask.php`

**Note**: The `./dist` directory does not contain any Node.js/Webpack related code and everything is in a finalized, optimized and protected form.  If Ion Cube has been enabled then the build process will use it to protect the PHP code.

### TODO and the WAITING:
* At the moment only basic websocket support is supported since [Ratchet](http://socketo.me/) does not support the [WAMP](http://wamp.ws/) v2 protocol and newer Javascript libraries such as [Autobahn|JS](http://autobahn.ws/js/) are now WAMP v2 only and the older v1 versions don't play nice with the CommonJS module system.  Ratchet development stalled out with the WAMP v2 feature, but there is hope since the [Thruway](https://github.com/voryx/Thruway) team is building upon the Ratchet code base and is hard at work to suport a WAMP v2 protocol.  There is much colloborative and blessings between the two projects so this looks positive.
* The Dockerfile is currently not complete.  It currently installs all of the dependencies but fails to start relevant services and it is not yet configuring an initial user.
* The computed ACL data is not being serialized to disk because there is no current solution for allowing the user ACL permissions to be modified and saved for a built dist system.
