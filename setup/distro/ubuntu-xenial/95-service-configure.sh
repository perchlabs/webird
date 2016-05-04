#!/usr/bin/env bash
. $SCRIPTDIR/versions.sh

if [[ ! -z "$DATABASE_ROOT_PASSWORD" ]]; then
  systemctl stop mysql
  mysqld_safe --skip-grant-tables

  mysql -u root <<MYSQL
UPDATE mysql.user SET Password=PASSWORD("$DATABASE_ROOT_PASSWORD") Where User='root';
FLUSH PRIVILEGES;
\q;
MYSQL
fi

systemctl restart mysql
systemctl restart php${PHP_VERSION}-fpm
systemctl restart nginx

sleep 2s

exit 0
