## Webird full application stack

Webird was created to merge the latest PHP and Node.js innovations into a single application stack.

The PHP foundation is comprised of Phalcon and Composer, which are used to create a HMVC foundation that offers everything that is expected of a modern PHP server side framework.
The Javascript foundation is built with Webpack 3, Vue 2 and NPM, which when used together are able to produce highly efficient single page applications.
Node.js is required for development only and is not required once a project has been built.

#### Key technologies of Webird:
* PHP 7.2
* [Phalcon](http://phalconphp.com/en/) 3
* [Webpack](http://webpack.github.io/) 3
* [Vue](https://vuejs.org/) 2
* [Babel](https://babeljs.io/) 6
* [PostCSS](https://github.com/postcss/postcss)
* [nginx](http://nginx.org/)
* [Composer](https://getcomposer.org/)
* [Nodejs](https://nodejs.org)
* [Ratchet](http://socketo.me/)
* [gettext](http://www.gnu.org/software/gettext/gettext.html)
* [MariaDB](https://mariadb.org/) 10.1
* [Docker](https://www.docker.com/)

#### Notable aspects of Webird:
* PHP CLI utilities for many tasks
* Manage all third party dependencies with Composer and NPM
* Bash provisioning and local installation scripts for configuring system
* A single PHP command that starts development processes across PHP and Nodejs
* Live reloading (and waiting) ES6 module and CommonJS front end environment
* Google OAuth2 login
* Integration gettext .po environment for both PHP and Webpack
* Create a final PHP and Javascript source protected distribution for deployment to the server
* Vue 2.0 example integration
* Includes Dockerfile

### Install Requirements:
* PHP >= 7.2
* Phalcon >= 3.3.0
* MariaDB >= 10.1
* Node.js >= 8.0

**Installation Instructions:**
```
# Ubuntu 18.04 Bionic

# System provisioning
sudo ./setup/provision-system.sh ubuntu-bionic

# Local install of NPM and Composer packages
./setup/install-local-packages.sh

# mariadb setup
# set DATABASE_PASSWORD to password for webird user.
mysqladmin --user=webird --password=DATABASE_PASSWORD create webird
mysql --user=webird --password=DATABASE_PASSWORD webird < ./etc/schema.sql

# Development setting configuration
cp ./etc/templates/dev_config.json ./etc/dev.json
# configure setting for local database password. (default is webird:open)
vi ./etc/dev.json
# Create a Webird user
# Change the email and password
./dev/run useradd --activate --password 'openopen' 'Your Name <yourname@gmail.com>' Administrators

# nginx setup
./dev/run nginx | sudo tee /etc/nginx/sites-available/dev.webird.io
sudo ln -s /etc/nginx/sites-available/dev.webird.io /etc/nginx/sites-enabled/dev.webird.io
sudo systemctl restart nginx

# /etc/hosts setup
echo -e "\n127.0.0.1 dev.webird.io" | sudo tee -a /etc/hosts
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

## Distribution Usage:

#### Create dist environment:
1. Copy `./etc/templates/dist_config.json` to `./etc/dist.json`
2. Configure `./etc/dist.json` to override settings from `./etc/dist_defaults.json`.  These two files will be merged to form `./build/etc/config.json`.
3. Create the dist environment: `./dev/run build`
4. Enter into dist directory `cd ./build`
5. Add `127.0.0.1 dist.webird.io` to `/etc/hosts`
6. Follow following instructions within dist environments

#### Configure final dist environment:

**Attention**: At this point it will be assumed that you are inside of the portable `dist` directory wherever it is now located (or named).

1. Generate nginx configuration with : `./run nginx | sudo tee /etc/nginx/sites-available/dist.webird.io`.
2. Enable nginx file: `sudo ln -s /etc/nginx/sites-available/dist.webird.io /etc/nginx/sites-enabled/dist.webird.io`
3. Restart web server
4. Import database schema located at `./etc/schema.sql`
5. Run server processes: `./run` (for websockets, beanstalkd loop, etc)
6. If something is wrong modify `./config.json` and repeated steps 1-3.  To make changes more permanent for dist releases you may go back and modify the original `./etc/dist.json` file and then rebuild the dist environment.
7. Visit https://dist.webird.io

The nginx configuration must be rebuilt if the distribution environment directory is moved or renamed.  It is recommended to use the `./run nginx` command to rebuild the configuration instead of manually editing the generated nginx configuration.  If more advanced custom settings are required it is recommended to first modify the source `./app/phalcon/common/views/simple/nginx/dist.volt` file and then rebuild the dist environment.

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
    ├── config.json (you can make this .json or .js)
    ├── commons (common code to be run by multiple entry points)
    ├── entries (specific code entry points)
    └── modules (general ES2016 and commonjs modules)
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
./build
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

```
./setup
├── provision-system.sh (takes a parameter $distro to provision system)
├── install-local-packages.sh (installs local packages into ./dev/)
├── os/ (operating specific scripts for provision-system.sh)
└── functions/ (helpers)
```

Compare the `./app` directory to a built `./build` directory to notice the differences between the app code and dev environment and the built system.

You may also view the build system routine at `app/phalcon/modules/cli/tasks/DevTask.php`

**Note**: The `./built` directory contains only optimized and uglified JS resources and if Ion Cube has been enabled then the build process will use it to protect the PHP code.

**Note**: A Vue template and single file component gettext extractor does not current exist as it has yet to be made.
