#!/bin/bash
# Environment variables SCRIPTDIR and TEMPDIR are available

# Disable Apache if it is installed
[[ -f /etc/init.d/apache2 ]] &&  update-rc.d apache2 disable

exit 0
