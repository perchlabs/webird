#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive
export PHP_VERSION=7.0

export PHP_ETC=/etc/php/$PHP_VERSION

# Setup web server choice
if [[ -n "$WEBIRD_WEBSERVER" ]]; then
  if [[ "$WEBIRD_WEBSERVER" != caddy ]] && [[ "$WEBIRD_WEBSERVER" != nginx ]]; then
    >&2 echo "Invalid webserver set from environment variable."
    exit 1
  fi
elif [[ "$WEBIRD_PROVISION" == noninteractive ]]; then
  export WEBIRD_WEBSERVER=caddy
else
  PS3="Select a web server: "
  select option in caddy nginx
  do
    case $option in
      caddy)
        export WEBIRD_WEBSERVER=caddy
        break;;
      nginx)
        export WEBIRD_WEBSERVER=nginx
        break;;
     esac
  done
fi
