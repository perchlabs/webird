#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available

# Update to get package list
apt-get update
# Install add-apt-repository command
apt-get install -y software-properties-common

# MariaDB Ubuntu PPA
apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xcbcb082a1bb943db
add-apt-repository 'deb http://mariadb.mirror.nucleus.be//repo/10.1/ubuntu trusty main'

# Nginx Stable Ubuntu PPA
add-apt-repository ppa:nginx/stable

# Update again after PPA changes
apt-get update
apt-get upgrade -y

packages=$(readlist "$SCRIPTDIR/lists/package")
apt-get install -y $packages

exit 0
