#!/usr/bin/env bash
SETUP_ROOT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" > /dev/null 2>&1 && pwd )"
LIB_DIR=$SETUP_ROOT_DIR/lib

source "$LIB_DIR/common.sh"
source "$LIB_DIR/menu.sh"

menuInit
initCommon "$(menuOsname "$1")"
menuStart
