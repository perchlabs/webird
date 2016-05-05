#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

[[ "$SKIP_PHALCON" = true ]] && exit 0

# Install Zephir
echo "Installing Zephir"
git clone git://github.com/phalcon/zephir.git "$TEMPDIR/zephir" > /dev/null
[[ $? -ne 0 ]] && exit $?
cd "$TEMPDIR/zephir"
./install -c > /dev/null

# Install Phalcon
echo "Installing Phalcon"
git clone --depth=1 -b 2.1.x git://github.com/phalcon/cphalcon.git "$TEMPDIR/cphalcon" > /dev/null
[[ $? -ne 0 ]] && exit $?
cd "$TEMPDIR/cphalcon"
# Must use the ZendEngine3 option for PHP 7
zephir build --backend=ZendEngine3 > /dev/null
[[ $? -ne 0 ]] && exit $?

php-extension-enable 'phalcon' 50

exit 0
