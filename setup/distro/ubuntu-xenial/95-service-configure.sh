#!/usr/bin/env bash
. $SCRIPTDIR/versions.sh

if [[ ! -z "$WEBIRD_DB_ROOT_PW" ]]; then
  service mysql stop
  mysqld_safe --skip-grant-tables

  mysql -u root <<MYSQL
UPDATE mysql.user SET Password=PASSWORD("$WEBIRD_DB_ROOT_PW") Where User='root';
FLUSH PRIVILEGES;
\q;
MYSQL
fi

systemctl mysql restart
systemctl php${PHP_VERSION}-fpm restart
systemctl nginx restart

sleep 2s

exit 0
