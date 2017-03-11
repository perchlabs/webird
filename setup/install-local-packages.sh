#!/usr/bin/env bash
DIR=$(dirname "$BASH_SOURCE")
DEVDIR="$(readlink -f "$DIR/../dev")"

cd "$DEVDIR"

yarn install
[[ $? -ne 0 ]] && exit $?

composer install
[[ $? -ne 0 ]] && exit $?

exit 0
