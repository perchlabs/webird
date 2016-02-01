#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

cd $TEMPDIR

NODE_VERSION=5.5.0

# Download nodejs source
wget https://nodejs.org/v${NODE_VERSION}/node-v${NODE_VERSION}.tar.gz
[[ $? -ne 0 ]] && exit $?
# Extract source
tar -xf node-v${NODE_VERSION}.tar.gz
[[ $? -ne 0 ]] && exit $?

# Compile source
cd node-v${NODE_VERSION}
[[ $? -ne 0 ]] && exit $?
./configure
[[ $? -ne 0 ]] && exit $?
make
[[ $? -ne 0 ]] && exit $?
make install
[[ $? -ne 0 ]] && exit $?

exit 0
