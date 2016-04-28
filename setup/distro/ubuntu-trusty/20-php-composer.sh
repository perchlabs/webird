#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available

# First check if composer is installed
command -v composer > /dev/null 2>&1
[[ $? -eq 0 ]] && exit 0

cd $TEMPDIR

curl -sS https://getcomposer.org/installer | php
if [[ $? -ne 0 ]]; then
  >&2 echo "Composer could not be downloaded"
  exit 1
fi

mv composer.phar /usr/local/bin/composer
echo "Composer moved to /usr/local/bin/composer"

exit $?
