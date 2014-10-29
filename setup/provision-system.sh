#!/usr/bin/env bash
DIR=`dirname "$BASH_SOURCE"`
distro=$1

if [ "$EUID" -ne 0 ]; then
  echo "This must be run as root"
  exit 1
fi

if [ -z $distro ]; then
  >&2 echo "A distro must be specified for provisioning"
  exit 1
fi

export SCRIPTDIR="$DIR/distro/$distro"
export TEMPDIR=$(mktemp -d)
if [[ ! -d $SCRIPTDIR ]]; then
  >&2 echo "A provisioning directory does not exist for '$distro'"
  exit 1
fi

# Export functions to be used throughout the build scripts
functions=$(find "$DIR/functions" -maxdepth 1 -type f)
for fscript in $functions; do
  # Find the base name of the file and split it by '.'
  fscript_basename=$(basename "$fscript")
  fscript_parts=(${fscript_basename/./ })
  fname=${fscript_parts[0]}
  # Source and then export the function
  . "$fscript"
  export -f $fname
done

# Find all of the files that begin with two numbers and sort them
scripts=$(find "$SCRIPTDIR" -maxdepth 1 -type f -name "[0-9][0-9]*" | sort)
for script in $scripts; do
  chmod ug+x "$script"
  sleep 0.10s
  "$script"
  ret=$?
  if [[ $ret -ne 0 ]]; then
    >&2 "Aborting There was an error with $script"
    exit $ret
  fi
done

echo "All provisioning source is located at:"
echo "$TEMPDIR\n"

exit 0
