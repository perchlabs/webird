#!/usr/bin/env bash

if [[ ! -z "$DATABASE_ROOT_PASSWORD" ]]; then
  echo "Setting password for MariaDB root user"

#  # Restart Mysql to disable grant table checking
  systemctl set-environment MYSQLD_OPTS="--skip-grant-tables"
  systemctl restart mysql

  mysql -u root <<MYSQL
UPDATE mysql.user SET Password=PASSWORD("$DATABASE_ROOT_PASSWORD")
  Where User='root';
FLUSH PRIVILEGES;
MYSQL

  # Restart Mysql without --skip-grant-tables option
  systemctl unset-environment MYSQLD_OPTS
  systemctl restart mysql
fi

exit 0
