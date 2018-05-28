#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ ! -z "${SKIP_PACKAGE+x}" ]] && exit 0

# Update to get package list
apt-get update
# Install add-apt-repository command
apt-get install -y software-properties-common

# Nginx Stable Ubuntu PPA
# add-apt-repository ppa:nginx/stable

# Add Nodejs repository
curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -

# Add Phalcon repository
# curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash
# curl -s https://packagecloud.io/install/repositories/phalcon/nightly/script.deb.sh | sudo bash

# Update again after PPA changes
apt-get update
apt-get upgrade -y

packages=$(readlist package)
apt-get install -y $packages

exit 0
