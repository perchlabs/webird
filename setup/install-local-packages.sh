#!/usr/bin/env bash
DIR=$(dirname "$BASH_SOURCE")
ROOT_DIR="$(readlink -f "$DIR/..")"

cd "$ROOT_DIR"

npm install
[[ $? -ne 0 ]] && exit $?

composer install
[[ $? -ne 0 ]] && exit $?

exit 0
