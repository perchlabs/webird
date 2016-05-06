#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ "$SKIP_NPM" = true ]] && exit 0

modules=$(readlist npm)
npm install -g $modules
exit $?
