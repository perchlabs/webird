#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ "$SKIP_PHALCON" = true ]] && exit 0

# Install Zephir
echo "Installing Zephir"
git clone git://github.com/phalcon/zephir.git "$TEMP_DIR/zephir" > /dev/null
[[ $? -ne 0 ]] && exit $?
cd "$TEMP_DIR/zephir"
./install -c > /dev/null

# Install Zephir parser
echo "Installing zephir_parser extension "
git clone git://github.com/phalcon/php-zephir-parser.git "$TEMP_DIR/zephir_parser" > /dev/null
[[ $? -ne 0 ]] && exit $?
cd "$TEMP_DIR/zephir_parser"
./install
php-extension-enable zephir_parser 50

# Install Phalcon
echo "Installing phalcon extension"
git clone --depth=1 -b 3.0.x git://github.com/phalcon/cphalcon.git "$TEMP_DIR/cphalcon" > /dev/null
[[ $? -ne 0 ]] && exit $?
cd "$TEMP_DIR/cphalcon"
# Must use the ZendEngine3 option for PHP 7
zephir build --backend=ZendEngine3 > /dev/null
[[ $? -ne 0 ]] && exit $?

php-extension-enable phalcon 50

exit 0
