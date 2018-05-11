#!/bin/bash
# Environment variables OS_DIR and TEMP_DIR are available

# Disable Apache if it is installed and enabled
systemctl is-enabled apache2 > /dev/null 2>&1
APACHE_ENABLED=$?
if [[ "$APACHE_ENABLED" -eq 0 ]]; then
  systemctl stop apache2
  systemctl disable apache2
fi

exit 0
