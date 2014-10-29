#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

cd $TEMPDIR

git clone --depth=1 git://github.com/phalcon/cphalcon.git
[[ $? -ne 0 ]] && exit $?

cd cphalcon/build
./install
[[ $? -ne 0 ]] && exit $?

php-extension-enable 'phalcon' 50

exit 0
