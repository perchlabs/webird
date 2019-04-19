#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

echo -e "${COLOR_SECTION}*** System Packages ***${TEXT_RESET}"

# First update for VMs that begin without a package cache.
# Also update for packages which are supplied by external repository.
echo "Updating package cache"
sudo apt-get update --quiet=2 > /dev/null
if [[ $? -ne 0 ]]; then
  >&2 echo "Unable to update the package cache."
  exit 1
fi

# The package cache has been update so if there is no interest
# in packages then we can skip expensive operations .
[[ -z "$PACKAGES_INTEREST" ]] && exit 0

# Upgrade the installed packages.
echo "Upgrading packages"
sudo apt-get upgrade --quiet=2 -y
if [[ $? -ne 0 ]]; then
  >&2 echo "Unable to upgrade the packages."
  exit 1
fi

# Some hosted VMs start without this essential package installed.
echo "Installing software-properties-common"
sudo apt-get install --quiet=2 --assume-yes software-properties-common
[[ $? -ne 0 ]] && exit 1

exit 0
