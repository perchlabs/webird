#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ -n "$SKIP_NPM" ]] && exit 0

packages=$(readlist npm)
if [[ -n "$packages" ]]; then
  npm install -g $packages
  exit $?
fi

exit 0
