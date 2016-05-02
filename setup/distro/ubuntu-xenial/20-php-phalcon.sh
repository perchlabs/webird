#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

cd $TEMPDIR

# Install Zephir
git clone git://github.com/phalcon/zephir.git
[[ $? -ne 0 ]] && exit $?
cd zephir
./install -c

# Install Phalcon
git clone --depth=1 -b 2.1.x git://github.com/phalcon/cphalcon.git
[[ $? -ne 0 ]] && exit $?
cd $TEMPDIR/cphalcon
# Must use the ZendEngine3 option for PHP 7
zephir build --backend=ZendEngine3
[[ $? -ne 0 ]] && exit $?

php-extension-enable 'phalcon' 50

exit 0
