## Webird full application stack

Webird was created to merge the latest PHP and Node.js innovations into a single application stack.

The PHP foundation is comprised of Phalcon and Composer, which are used to create a HMVC foundation that offers everything that is expected of a modern PHP server side framework.
The front end resources are bundled with Webpack, which solves many problems associated with modern web development by bringing the Node.js popularized CommonJS module system to the browser.
Node.js is required for development only and is not required once a project has been built.

#### Key features of Webird:
* HTML5 IE11+
* PHP 5.4+
* PHP [Ratchet](http://socketo.me/) websockets that offer read-only access to the PHP session data
* Google OAuth2 login
* PHP CLI utilities for many tasks
* Manage most third party dependencies with Composer, NPM and Bower
* Bash provisioning and local installation scripts for configuring system
* A single PHP command that starts development processes across PHP and Node.js
* [Webpack](http://webpack.github.io/) (CommonJS) build environment
* Live reloading (and waiting) CommonJS build environment
* Program in Javascript ES5, Javascript ES2015 ([Babel](https://babeljs.io/)) or Coffeescript
* Theme with CSS ([PostCSS](https://github.com/postcss/postcss)), SCSS or LESS
* Complete integration of gettext .po translation data between the PHP and Webpack (Javascript) environments
* Create a final PHP and Javascript source protected distribution for deployment to the server
* AngularJS and Backbone/Marionette Webpack examples
* [Docker](https://www.docker.com/) container

#### Key components of Webird:
* [nginx](http://nginx.org/)
* [Phalcon](http://phalconphp.com/en/)
* [Babel](https://babeljs.io/) - Javascript ES2015 to ES5
* [Coffeescript](http://coffeescript.org/)
* [PostCSS](https://github.com/postcss/postcss)
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
* PHP >= 5.4
* Phalcon >= 2.0.1
* MariaDB >= 10.0
* Node.js >= 0.12.0

**Installation Instructions:**
```
# Ubuntu 14.04

# System provisioning
sudo ./setup/provision-system.sh ubuntu1404

# Local install of npm, bower and composer packages
./setup/install-local-packages.sh

# mariadb setup
# set DB_ROOT_PW to
mysqladmin --user=root --password=DB_ROOT_PW create webird
mysql --user=root --password=DB_ROOT_PW webird < ./etc/schema.sql

# Development setting configuration
cp ./etc/templates/dev_config.yml ./etc/dev.yml
# configure setting for local database password. (default is root:root)
vi ./etc/dev.yml
# Create a Webird user
# Change the email and password
./dev/webird.php useradd --activate --password 'openopen' 'Your Name <yourname@gmail.com>' Administrators

# nginx setup
./dev/webird.php nginx | sudo tee /etc/nginx/sites-available/dev.webird.io
sudo ln -s /etc/nginx/sites-available/dev.webird.io /etc/nginx/sites-enabled/dev.webird.io
sudo service nginx restart

# /etc/hosts setup
echo -e "\n127.0.0.1 dev.webird.io" | sudo tee -a /etc/hosts
```

### Poedit Localization editor:
In order to modify the localization messages you will need to configure the [Poedit](http://poedit.net/) GNU gettext frontend since it does not come with the tools necessary to parse Nunjucks and Volt templates.  The provision script will have installed a nodejs script called xgettext-template.

##### Poedit Configuration Instructions:
Go to File - Preferences... in Poedit and add a new parser in the Parsers tab:

* **Nunjucks**
  * Language: `Nunjucks`
  * List of extensions...: `*.nunj`
  * Parser Command: `xgettext-template -L Swig --force-po -o %o %C %K %F`
  * An item in keywords list: `-k %k`
  * An item in input files list: `%f`
  * Source code charset: `--from-code=%c`
* **Volt**
  * Language: `Volt`
  * List of extensions...: `*.volt`
  * Parser Command: `xgettext-template -L Volt --force-po -o %o %C %K %F`
  * An item in keywords list: `-k %k`
  * An item in input files list: `%f`
  * Source code charset: `--from-code=%c`


## Development Usage:
1. Run server processes: `./dev/webird.php [server]` and wait until webpack-dev-server has finished building
2. Visit http://dev.webird.io

If you see the local host file not configured page then add `127.0.0.1 dev.webird.io` to your `/etc/hosts` file.

## Distribution Usage:

#### Create dist environment:
1. Copy `./etc/templates/dist_config.yml` to `./etc/dist.yml`
2. Configure `./etc/dist.yml` to override settings from `./etc/dist_defaults.yml`.  These two files will be merged to form `./dist/etc/config.yml`.
3. Create the dist environment: `./dev/webird.php build`
4. Enter into dist directory `cd ./dist`
5. Add `127.0.0.1 dist.webird.io` to `/etc/hosts`
6. Follow following instructions within dist environments

#### Configure final dist environment:

**Warning**: At this point it will be assumed that you are inside of the portable `dist` directory wherever it is now located (or named).

1. Generate nginx configuration with : `./webird.php nginx | sudo tee /etc/nginx/sites-available/dist.webird.io`.
2. Enable nginx file: `sudo ln -s /etc/nginx/sites-available/dist.webird.io /etc/nginx/sites-enabled/dist.webird.io`
3. Restart web server
4. Import database schema located at `./etc/schema.sql`
5. Run server processes: `./webird.php` (for websockets, beanstalkd loop, etc)
6. If something is wrong modify `./config.yml` and repeated steps 1-3.  To make changes more permanent for dist releases you may go back and modify the original `./etc/dist.yml` file and then rebuild the dist environment.
7. Visit https://dist.webird.io

The nginx configuration must be rebuilt if the distribution environment directory is moved or renamed.  It is recommended to use the `./webird.php nginx` command to rebuild the configuration instead of manually editing the generated nginx configuration.  If more advanced custom settings are required it is recommended to first modify the source `./app/phalcon/common/views/simple/nginx/dist.volt` file and then rebuild the dist environment.

**Note**: Node.js is no longer a dependency at this point since it is only used to build the browser facing content into static bundles.

## Project Structure:

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
    ├── config.json (you can make this .json, .yml, .js or .coffee)
    ├── commons (common code to be run by multiple entry points)
    ├── entries (specific code entry points)
    └── modules (custom commonjs modules)
```

```
./dev
├── webird.php (CLI entry for dev environment)
├── public/
│   └── index.php (Web entry for dev environment)
├── cmd_overrides/ (dev specific command overrides for CLI interface)
├── packages.json (npm configuration)
├── bower.json (Bower configuration)
├── vendor.json (Composer configuration)
├── gulpfile.js (Gulp streaming build system configuration)
├── gulpfile.webpack.js (Webpack configuration)
├── node_modules/
├── bower_components/
└── vendor/
```

```
./dist
├── webird.php (CLI entry for dist environment)
├── public/
│   └── index.php (Web entry for dist environment)
├── etc/
├── cache-static/
│   ├── locale/ (localization files in machine readable .mo format)
│   └── volt/ (compiled Volt templates)
├── phalcon/
└── vendor/ (Composer packages)
```

```
./setup
├── provision-system.sh (takes a parameter $distro to provision system)
├── install-local-packages.sh (installs local packages into ./dev/)
├── distro/ (distro specific scripts for provision-system.sh)
└── functions/ (helpers)
```

Compare the `./app` directory to a built `./dist` directory to notice the differences between the app code and dev environment and the finalized dist environment.

You may also view the build system routine at `app/phalcon/modules/cli/tasks/DevTask.php`

**Note**: The `./dist` directory contains only optimized and uglified JS resources and if Ion Cube has been enabled then the build process will use it to protect the PHP code.

### TODO and the WAITING:
* At the moment only basic websocket support is supported since [Ratchet](http://socketo.me/) does not support the [WAMP](http://wamp.ws/) v2 protocol and newer Javascript libraries such as [Autobahn|JS](http://autobahn.ws/js/) are now WAMP v2 only and the older v1 versions don't play nice with the CommonJS module system.  Ratchet development stalled out with the WAMP v2 feature, but there is hope since the [Thruway](https://github.com/voryx/Thruway) team is building upon the Ratchet code base and is hard at work to suport a WAMP v2 protocol.  There is much colloborative and blessings between the two projects so this looks positive.
* The Dockerfile is currently not complete.  It currently installs all of the dependencies but fails to start relevant services and it is not yet configuring an initial user.
* The computed ACL data is not being serialized to disk because there is no current solution for allowing the user ACL permissions to be modified and saved for a built dist system.
