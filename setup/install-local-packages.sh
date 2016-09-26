#!/usr/bin/env bash
DIR=$(dirname "$BASH_SOURCE")
DEVDIR="$(readlink -f "$DIR/../dev")"

cd "$DEVDIR"

# skipclean=1 prevents a non-interactive install error
skipclean=1
npm install
[[ $? -ne 0 ]] && exit $?

# Run bower install from local node_modules so that it doesn't need to be globally installed
npm run bower-install
[[ $? -ne 0 ]] && exit $?

composer install
[[ $? -ne 0 ]] && exit $?

exit 0
