#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

# CLI setup
# php-conf cli memory_limit 512M

# FPM setup
# FPM setup to listen on TCP/IP instead of socket file
POOLD_CONF=/etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
FPM_SOCK_FILE=/run/php/php${PHP_VERSION}-fpm.sock
grep "^listen = $FPM_SOCK_FILE" "$POOLD_CONF" > /dev/null
if [[ $? -eq 0 ]]; then
  sed -i "\|^listen = ${FPM_SOCK_FILE}|s|^|;|" $POOLD_CONF
  sed -i "\|;listen = ${FPM_SOCK_FILE}|a listen = 127.0.0.1:9000" $POOLD_CONF
fi

exit 0
