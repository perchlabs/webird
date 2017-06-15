#!/usr/bin/env bash

systemctl restart mysql
systemctl restart php${PHP_VERSION}-fpm

# Disable and stop all unused webservers
list=(nginx caddy)
remove=($WEBIRD_WEBSERVER)
disableList=("${list[@]/$remove}")
for i in "${!disableList[@]}"; do
  disabledService="${disableList[$i]}"
  [[ -z "$disabledService" ]] && continue

  systemctl disable "$disabledService"
  systemctl stop "$disabledService"
done

# Enable and start active webserver
systemctl enable "$WEBIRD_WEBSERVER"
systemctl restart "$WEBIRD_WEBSERVER"

sleep 2s

exit 0
