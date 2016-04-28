#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

cd $TEMPDIR

git clone --depth=1 -b 2.1.x git://github.com/phalcon/cphalcon.git
[[ $? -ne 0 ]] && exit $?

git clone git://github.com/phalcon/zephir.git
[[ $? -ne 0 ]] && exit $?
cd zephir
./install -c

cd $TEMPDIR/cphalcon
zephir build --backend=ZendEngine3
[[ $? -ne 0 ]] && exit $?

php-extension-enable 'phalcon' 50

exit 0
