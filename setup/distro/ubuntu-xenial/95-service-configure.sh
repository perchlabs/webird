#!/usr/bin/env bash
DIR=`dirname "$BASH_SOURCE"`

if [[ ! -z "$WEBIRD_DB_ROOT_PW" ]]; then
  service mysql stop
  mysqld_safe --skip-grant-tables

  mysql -u root <<MYSQL
UPDATE mysql.user SET Password=PASSWORD("$WEBIRD_DB_ROOT_PW") Where User='root';
FLUSH PRIVILEGES;
\q;
MYSQL
fi

service mysql restart
service php7.0-fpm restart
service nginx restart

sleep 2s

exit 0
