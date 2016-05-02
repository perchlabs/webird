#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available
. $SCRIPTDIR/functions/php.sh

# CLI setup
# php-conf cli memory_limit 512M

# FPM setup
php-conf fpm listen 127.0.0.1:9000

exit 0
