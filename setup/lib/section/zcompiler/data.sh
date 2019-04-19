
ZCOMPILER_DEFAULT_VERSION=0.11.12

ZCOMPILER_DEFAULT=phar:$ZCOMPILER_DEFAULT_VERSION

# Zephir menu
MENU_ZCOMPILER_NAME=Zephir
MENU_ZCOMPILER_GIT_BRANCH_DEFAULT=development
MENU_ZCOMPILER_GIT_URL_DEFAULT=https://github.com/phalcon/zephir.git
MENU_ZCOMPILER_BRANCHES=https://github.com/phalcon/zephir/branches
MENU_ZCOMPILER_VERSIONS=https://github.com/phalcon/zephir/releases
MENU_ZCOMPILER_TARBALL_EXAMPLES="
  $ZCOMPILER_DEFAULT_VERSION
  https://github.com/phalcon/zephir/archive/v${ZCOMPILER_DEFAULT_VERSION}.tar.gz
  file://${HOME}/v${ZCOMPILER_DEFAULT_VERSION}.tar.gz
"
MENU_ZCOMPILER_PHAR_EXAMPLES="
  $ZCOMPILER_DEFAULT_VERSION
  https://github.com/phalcon/zephir/releases/download/${ZCOMPILER_DEFAULT_VERSION}/zephir.phar
  file://${HOME}/zephir.phar
"
