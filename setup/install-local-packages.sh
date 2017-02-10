#!/usr/bin/env bash
DIR=$(dirname "$BASH_SOURCE")
DEVDIR="$(readlink -f "$DIR/../dev")"

cd "$DEVDIR"

# skipclean=1 prevents a non-interactive install error
skipclean=1
npm install
[[ $? -ne 0 ]] && exit $?

composer install
[[ $? -ne 0 ]] && exit $?

exit 0
