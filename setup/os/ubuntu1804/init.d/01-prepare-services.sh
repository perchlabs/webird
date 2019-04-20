#!/bin/bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

systemctl is-enabled apache2 > /dev/null 2>&1
APACHE_ENABLED=$?
if [[ "$APACHE_ENABLED" -eq 0 ]]; then
  sudo systemctl stop apache2
  sudo systemctl disable apache2
fi

exit 0
