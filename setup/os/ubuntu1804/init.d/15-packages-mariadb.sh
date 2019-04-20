#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

# The package cache has been update so if there is no interest
# in packages then we can skip expensive operations .
[[ -z "$PACKAGES_INTEREST" ]] && exit 0

sudo apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0xF1656F24C74CD1D8
sudo add-apt-repository 'deb [arch=amd64,arm64,ppc64el] http://mirror.jaleco.com/mariadb/repo/10.4/ubuntu bionic main'
