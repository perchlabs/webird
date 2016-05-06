#!/usr/bin/env bash

systemctl restart mysql
systemctl restart php${PHP_VERSION}-fpm
systemctl restart nginx

sleep 2s

exit 0
