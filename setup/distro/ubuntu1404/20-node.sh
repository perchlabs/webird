#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

cd $TEMPDIR

# Download nodejs source
wget https://nodejs.org/dist/latest/node-v4.0.0.tar.gz
[[ $? -ne 0 ]] && exit $?
# Extract source
tar -xf node-v4.0.0.tar.gz
[[ $? -ne 0 ]] && exit $?

# Compile source
cd node-v4.0.0
[[ $? -ne 0 ]] && exit $?
./configure
[[ $? -ne 0 ]] && exit $?
make
[[ $? -ne 0 ]] && exit $?
make install
[[ $? -ne 0 ]] && exit $?

exit 0
