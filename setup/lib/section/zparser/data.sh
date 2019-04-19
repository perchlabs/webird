
ZPARSER_DEFAULT_VERSION=1.2.0

ZPARSER_DEFAULT=tarball:$ZPARSER_DEFAULT_VERSION

# zephir_parser menu.
MENU_ZPARSER_NAME=zephir_parser
MENU_ZPARSER_GIT_BRANCH_DEFAULT=development
MENU_ZPARSER_GIT_URL_DEFAULT=https://github.com/phalcon/php-zephir-parser.git
MENU_ZPARSER_BRANCHES=https://github.com/phalcon/php-zephir-parser/branches
MENU_ZPARSER_VERSIONS=https://github.com/phalcon/php-zephir-parser/releases
MENU_ZPARSER_TARBALL_EXAMPLES="
  $ZPARSER_DEFAULT_VERSION
  https://github.com/phalcon/php-zephir-parser/archive/v${ZPARSER_DEFAULT_VERSION}.tar.gz
  file://${HOME}/v${ZPARSER_DEFAULT_VERSION}.tar.gz
"
