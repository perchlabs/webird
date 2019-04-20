#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

[[ -z "$NPM_INTEREST" ]] && exit 0

list=$(readlist npm)
[[ "$list" == "" ]] && exit 0

sudo npm install -g $list
if [[ $? -ne 0 ]]; then
  >&2 echo "There was a problem installing NPM modules"
  exit 1
fi

exit 0
