#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

# CLI setup
php-conf cli memory_limit 512M

# FPM setup
php-conf fpm post_max_size 512M
php-conf fpm upload_max_filesize 512M

# FPM setup to listen on TCP/IP instead of socket file
POOLD_CONF=/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
FPM_SOCK_FILE=/run/php/php${PHP_VERSION}-fpm.sock
grep "^listen = $FPM_SOCK_FILE" "$POOLD_CONF" > /dev/null
if [[ $? -eq 0 ]]; then
  sed -i "\|^listen = ${FPM_SOCK_FILE}|s|^|;|" $POOLD_CONF
  sed -i "\|;listen = ${FPM_SOCK_FILE}|a listen = 127.0.0.1:9000" $POOLD_CONF
fi


exit 0
