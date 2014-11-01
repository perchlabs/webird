#!/bin/bash
# Environment variables SCRIPTDIR and TEMPDIR are available

# Disable Apache if it is installed
if [[ -f /etc/init.d/apache2 ]]; then
  service apache2 stop
  update-rc.d apache2 disable
fi

exit 0
