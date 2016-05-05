#!/usr/bin/env bash

if [[ ! -z "$DATABASE_ROOT_PASSWORD" ]]; then
  systemctl set-environment MYSQL_OPTS="--skip-grant-tables"
  systemctl stop mysql

  mysql -u root <<MYSQL
UPDATE mysql.user SET Password=PASSWORD("$DATABASE_ROOT_PASSWORD") Where User='root';
FLUSH PRIVILEGES;
quit;
MYSQL

  systemctl stop mysql
  systemctl unset-environment MYSQL_OPTS
fi

exit 0
