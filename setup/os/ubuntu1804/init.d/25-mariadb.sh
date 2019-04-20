#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

[[ -z "$MARIADB_INTEREST" ]] && exit 0

echo -e "${COLOR_SECTION}*** MariaDB/MySQL ***${TEXT_RESET}"

if [[ -z "$MARIADB_HOST" ]]; then
  >&2 echo "Skipping MariaDB setup because the host variable is not set."
  exit 1
fi
if [[ -z "$MARIADB_USER" ]]; then
  >&2 echo "Skipping MariaDB setup because the user variable is not set."
  exit 1
fi
if [[ -z "$MARIADB_PASSWORD" ]]; then
  >&2 echo "Skipping MariaDB setup because the password variable is not set."
  exit 1
fi

# MariaDB 10.4+ has a new method to initialize the system
# using a sudo login through the local socket.

# Delete built in test tables.
sudo mysql --protocol=socket <<EOF
DELETE FROM mysql.db WHERE db LIKE 'tes%' AND user='';
EOF

# Delete annonymous users
sudo mysql --protocol=socket <<EOF
DROP USER IF EXISTS ''@'localhost';
DROP USER IF EXISTS ''@'$(hostname)';
EOF

# Create the normal user and set the password.
sudo mysql --protocol=socket <<EOF
CREATE USER IF NOT EXISTS '$MARIADB_USER'@'$MARIADB_HOST';
ALTER USER '$MARIADB_USER'@'$MARIADB_HOST' IDENTIFIED BY '$MARIADB_PASSWORD';
EOF

# Assign privileges to normal user.
# Modify this to grant less permissions as needed.
sudo mysql --protocol=socket <<EOF
GRANT ALL PRIVILEGES ON *.* TO '$MARIADB_USER'@'$MARIADB_HOST' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EOF

exit 0
