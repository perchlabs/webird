#!/usr/bin/env bash
# Environment variables SCRIPTDIR and TEMPDIR are available

# Update to get package list
apt-get update
# Install add-apt-repository command
apt-get install -y software-properties-common

# MariaDB Ubuntu PPA
# Upgrades to 10.1
# sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
# sudo add-apt-repository 'deb [arch=amd64,i386] http://mirror.netinch.com/pub/mariadb/repo/10.1/ubuntu xenial main'

# Nginx Stable Ubuntu PPA
# add-apt-repository ppa:nginx/stable

# Update again after PPA changes
apt-get update
apt-get upgrade -y

packages=$(readlist "$SCRIPTDIR/lists/package")
apt-get install -y $packages

exit 0
