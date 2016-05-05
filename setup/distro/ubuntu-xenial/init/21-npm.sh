#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available

[[ "$SKIP_NPM" = true ]] && exit 0

modules=$(readlist "$SCRIPTDIR/lists/npm")
npm install -g $modules
exit $?
