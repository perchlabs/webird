#!/usr/bin/env bash

if [[ -z "$DATABASE_PASSWORD" ]]; then
  echo "Skipping Mysql setup."
  exit 0
fi

echo "Setting password for MariaDB root user"

#  # Restart Mysql to disable grant table checking
systemctl set-environment MYSQLD_OPTS="--skip-grant-tables"
systemctl restart mysql

mysql -u root <<MYSQL
CREATE USER IF NOT EXISTS 'webird'@'localhost';
UPDATE mysql.user
  SET Password=PASSWORD("$DATABASE_PASSWORD")
  WHERE user='webird';
GRANT ALL PRIVILEGES ON *.* TO 'webird'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
MYSQL

# Restart Mysql without --skip-grant-tables option
systemctl unset-environment MYSQLD_OPTS
systemctl restart mysql

exit 0
