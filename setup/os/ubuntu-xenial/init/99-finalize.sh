#!/usr/bin/env bash

systemctl restart mysql
systemctl restart php${PHP_VERSION}-fpm

case "$WEBIRD_WEBSERVER" in
  caddy)
    # Disable and stop nginx
    systemctl disable nginx
    systemctl stop nginx

    # Enable and start caddy
    systemctl enable caddy
    systemctl restart caddy
    ;;
  nginx)
    # Disable and stop caddy
    systemctl disable caddy
    systemctl stop caddy

    # Enable and start nginx
    systemctl enable nginx
    systemctl restart nginx
    ;;
esac


sleep 2s

exit 0
