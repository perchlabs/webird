#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ ! -z "${SKIP_NODE+x}" ]] && exit 0

NODE_INSTALLED_PATH=$(which node)
if [[ $? -eq 0 ]]; then
  NODE_INSTALLED_VERSION=$(node --version)
  if [[ "$NODE_INSTALLED_VERSION" = "v${NODE_VERSION}" ]]; then
    echo "Node installation is already up to date."
    exit 0
  fi
fi

cd $TEMP_DIR

# Download nodejs source
wget https://nodejs.org/dist/v${NODE_VERSION}/node-v${NODE_VERSION}.tar.gz
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
