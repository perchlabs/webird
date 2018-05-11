#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

# If Phalcon compile is not set then don't compile.
[[ -z "$PHALCON_COMPILE" ]] && exit 0

# You can skip compiling Phalcon by manually exporting this.
[[ "$SKIP_PHALCON" = true ]] && exit 0


# Install Zephir
echo "Installing Zephir"
ZEPHIR_INSTALL_DIR=/opt/zephir
rm -Rf "$ZEPHIR_INSTALL_DIR"
cd "$TEMP_DIR"
wget "https://github.com/phalcon/zephir/archive/${ZEPHIR_VERSION}.tar.gz"
[[ $? -ne 0 ]] && exit $?
tar -xf "${ZEPHIR_VERSION}.tar.gz"
[[ $? -ne 0 ]] && exit $?
mv "$TEMP_DIR/zephir-${ZEPHIR_VERSION}" "$ZEPHIR_INSTALL_DIR"
cd "$ZEPHIR_INSTALL_DIR"
./install -c > /dev/null

# Install Zephir parser
echo "Installing zephir_parser extension "
cd "$TEMP_DIR"
wget "https://github.com/phalcon/php-zephir-parser/archive/v${ZEPHIR_PARSER_VERSION}.tar.gz"
[[ $? -ne 0 ]] && exit $?
tar -xf "v${ZEPHIR_PARSER_VERSION}.tar.gz"
[[ $? -ne 0 ]] && exit $?
cd "$TEMP_DIR/php-zephir-parser-${ZEPHIR_PARSER_VERSION}"
./install
php-extension-enable zephir_parser 50
echo "Zephir parser extension installed."

# Install Phalcon
echo "Installing phalcon extension"
cd "$TEMP_DIR"
case "$PHALCON_COMPILE" in
"tree")
  git clone --depth=1 -b "$PHALCON_VERSION" git://github.com/phalcon/cphalcon.git "$TEMP_DIR/cphalcon" > /dev/null
  [[ $? -ne 0 ]] && exit $?
  cd "$TEMP_DIR/cphalcon"
  ;;
"tarball")
  wget "https://github.com/phalcon/cphalcon/archive/v${PHALCON_VERSION}.tar.gz"
  [[ $? -ne 0 ]] && exit $?
  tar -xf "v${PHALCON_VERSION}.tar.gz"
  [[ $? -ne 0 ]] && exit $?
  cd "$TEMP_DIR/cphalcon-${PHALCON_VERSION}/"
  ;;
*)
  echo "Invalid Phalcon compile source.  Aborting."
  exit 1
  ;;
esac
zephir build
[[ $? -ne 0 ]] && exit $?
echo "Phalcon extension installed."


php-extension-enable phalcon 50

exit 0
