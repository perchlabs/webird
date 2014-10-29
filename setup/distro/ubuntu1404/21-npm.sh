#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available

modules=$(readlist "$SCRIPTDIR/lists/npm")
npm install -g $modules
exit $?
