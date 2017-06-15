#!/usr/bin/env bash

systemctl restart mysql
systemctl restart php${PHP_VERSION}-fpm

# Disable and stop all unused webservers
list=(nginx caddy)
remove=($WEBIRD_WEBSERVER)
disableList=("${list[@]/$remove}")
for i in "${!disableList[@]}"; do
  disabledServer = "${disableList[$i]}"
  [[ -z "$disabledServer" ]] && continue

  systemctl disable "$disabledServer"
  systemctl stop "$disabledServer"
done

# Enable and start active webserver
systemctl enable "$WEBIRD_WEBSERVER"
systemctl restart "$WEBIRD_WEBSERVER"

sleep 2s

exit 0
