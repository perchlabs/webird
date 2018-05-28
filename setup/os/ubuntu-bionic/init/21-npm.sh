#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ ! -z "${SKIP_NPM+x}" ]] && exit 0

modules=$(readlist npm)
npm install -g $modules
exit $?
