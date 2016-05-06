#!/usr/bin/env bash
# Get the absolute path of the script directory
pushd `dirname $0` > /dev/null
SETUP_ROOT_DIR=`pwd -P`
popd > /dev/null

distro=$1

if [[ "$EUID" -ne 0 ]]; then
  echo "This must be run as root"
  exit 1
fi

if [[ -z "$distro" ]]; then
  >&2 echo "A distro name must be specified for provisioning."
  exit 1
fi
export DISTRO_DIR="$SETUP_ROOT_DIR/distro/$distro"
if [[ ! -d "$DISTRO_DIR" ]]; then
  >&2 echo "A provisioning directory does not exist for '$distro'."
  exit 1
fi

TEMP_DIR=$(mktemp -d)

# Allows reading of distro specific lists while ignoring lines beginning to hash
readlist() { echo $(grep -v '^#' "$DISTRO_DIR/lists/$1"); }
export -f readlist

. "$DISTRO_DIR/exports.sh"
functions=$(find "$DISTRO_DIR/functions" -maxdepth 1 -type f)
for fscript in $functions; do
  . $fscript
done

# Mark all variables for export
set -a

# Find all of the files that begin with two numbers and sort them
scripts=$(find "$DISTRO_DIR/init" -maxdepth 1 -type f -name "[0-9][0-9]*" | sort)
for script in $scripts; do
  "$script"
  ret=$?
  if [[ $ret -ne 0 ]]; then
    >&2 "Aborting There was an error with $script"
    exit $ret
  fi
done

echo "Finshed. All provisioning source is located at:"
echo "$TEMP_DIR"

exit 0
