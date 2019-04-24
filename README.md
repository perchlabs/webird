## Webird full application stack

#### NOTE: Webird is currently undergoing massive code improvements, dependency updates, keeping up with modern language standards, etc. Don't expect things to work well at the moment but if you are interested then there should be a fair amount happening in the near future.  I've historically used Webird to test new technologies and I will continue to use it for that.  By the end of this effort things should be looking quite good.


Webird was created to merge the latest PHP and Node.js innovations into a single application stack.

The PHP foundation is comprised of Phalcon and Composer, which are used to create a HMVC foundation that offers everything that is expected of a modern PHP server side framework.
The Javascript foundation is built with Webpack 3, Vue 2 and NPM, which when used together are able to produce highly efficient single page applications.
Node.js is required for development only and is not required once a project has been built.

#### Key technologies of Webird:
* PHP 7.2
* [Phalcon](http://phalconphp.com/en/) 3
* [Webpack](http://webpack.github.io/) 4
* [Vue](https://vuejs.org/) 2
* [Babel](https://babeljs.io/) 7
* [PostCSS](https://github.com/postcss/postcss)
* [nginx](http://nginx.org/)
* [Composer](https://getcomposer.org/)
* [Nodejs](https://nodejs.org)
* [Ratchet](http://socketo.me/)
* [gettext](http://www.gnu.org/software/gettext/gettext.html)
* [MariaDB](https://mariadb.org/) 10.4
* [Docker](https://www.docker.com/)

#### Notable aspects of Webird:
* PHP CLI utilities for many tasks
* Manage all third party dependencies with Composer and NPM
* Bash provisioning and local installation scripts for configuring system (based on [setupify](https://github.com/perchlabs/setupify))
* A single PHP command that starts development processes across PHP and Nodejs
* Live reloading ES6 module front end environment
* Google OAuth2 login
* Integrate gettext .po environment for both PHP and Webpack
* Vue 2 example integration
* Includes Dockerfile skeleton

### Install Requirements:
* PHP >= 7.2
* Phalcon >= 3.4.0
* MariaDB >= 10.4
* Node.js >= 10.2

**Installation Instructions:**
```
# Ubuntu 18.04 Bionic

# System provisioning
sudo ./setup/install ubuntu1804

# mariadb setup
sudo mysqladmin --protocol=socket create webird
sudo mysql --protocol=socket webird < ./etc/schema.sql

# Create a Webird user. You can use this user to create more users via the web interface.
./dev/run useradd --activate --password 'openopen' 'Your Name' 'yourname@gmail.com' Administrators
```

### Poedit Localization editor:
In order to modify the localization messages you will need to configure the [Poedit](http://poedit.net/) GNU gettext frontend since it does not come with the tools necessary to parse Volt and Vue templates.  The provision script will have installed a node script called xgettext-template.

##### Poedit Configuration Instructions:
Go to File - Preferences... in Poedit and add a new parser in the Parsers tab:

* **Volt**
  * Language: `Volt`
  * List of extensions...: `*.volt`
  * Parser Command: `xgettext-template -L Volt --force-po -o %o %C %K %F`
  * An item in keywords list: `-k %k`
  * An item in input files list: `%f`
  * Source code charset: `--from-code=%c`
* **Vue**
  * TODO

## Development Usage:
1. Run server processes: `./dev/run [server]` and wait until webpack-dev-server has finished building
2. Visit http://dev.webird.io

If you see the local host file not configured page then add `127.0.0.1 dev.webird.io` to your `/etc/hosts` file.

## Production Usage:

#### Create prod (production) environment:
1. Configure `./etc/prod.json` to override settings from `./etc/prod_defaults.json`.  These two files will be merged to form `./build/etc/config.json`.
2. Create the prod environment: `./dev/run build`
3. Enter into prod directory `cd ./prod`
4. Run `./run nginx | sudo tee /etc/nginx/sites-available/prod.webird.io 1> /dev/null`
5. Run `sudo ln -fs /etc/nginx/sites-available/prod.webird.io /etc/nginx/sites-enabled/prod.webird.io`
6. Add `127.0.0.1 prod.webird.io` to `/etc/hosts`
7. Follow following instructions within prod environments

#### Run final prod environment:

**Attention**: At this point it will be assumed that you are inside of the portable `prod` directory wherever it is now located (or named).

1. Import database schema located at `./etc/schema.sql`
2. Run server processes: `./run` (for websockets, beanstalkd loop, etc)
3. Visit https://prod.webird.io

The nginx configuration must be rebuilt if the production environment directory is moved or renamed.  It is recommended to use the `./run nginx` command to rebuild the configuration instead of manually editing the generated nginx configuration.  If more advanced custom settings are required it is recommended to first modify the source `./app/phalcon/common/views/simple/nginx/prod.volt` file and then rebuild the prod environment.

**Note**: Node.js is no longer a dependency at this point since it is only used to build the browser facing content into static bundles.

## Project Structure:

```
./setup
├── install (takes a parameter $osName to provision system)
└── menu (provides a lightbar menu interface to installation)
```

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
    ├── config.json (you can make this .json or .js)
    ├── entries (code entry points)
    └── modules (application ES6 modules)
        └── commons (common code to be run by multiple entry points)
```

```
./dev
├── run (CLI entry for dev environment)
├── public/
│   └── index.php (Web entry for dev environment)
├── cmd_overrides/ (dev specific command overrides for CLI interface)
└── webpack.js (Webpack script)
```

```
./prod
├── run (CLI entry for built system)
├── public/
│   ├── index.php (Web entry for built system)
│   └── static resources copied from app directory
├── etc/
├── cache-static/
│   ├── locale/ (localization files in machine readable .mo format)
│   └── volt/ (compiled Volt templates)
├── phalcon/
└── vendor/ (Composer packages)
```

Compare the `./app` directory to a built `./prod` directory to notice the differences between the app code and dev environment and the built system.

You may also view the build system routine at `app/phalcon/modules/cli/tasks/DevTask.php`

**Note**: The `./prod` directory contains only optimized and uglified JS resources and if Ion Cube has been enabled then the build process will use it to protect the PHP code.

**Note**: A Vue template and single file component gettext extractor does not current exist as it has yet to be made.
