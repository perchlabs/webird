#!/bin/bash
# Environment variables SCRIPTDIR and TEMPDIR are available

# Disable Apache if it is installed and enabled
systemctl is-enabled apache2 > /dev/null
APACHE_ENABLED=$?
if [[ "$APACHE_ENABLED" -eq 0 ]]; then
  systemctl stop apache2
  systemctl disable apache2
fi

exit 0
