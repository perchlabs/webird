#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

# Update the package cache again for PPA repositories
# which may have been added or removed.
echo "Updating package cache again (for PPAs)"
sudo apt-get update --quiet=2

# The package cache has been update so if there is no interest
# in packages then we can skip expensive operations .
[[ -z "$PACKAGES_INTEREST" ]] && exit 0

list=$(readlist package)
sudo apt-get install --quiet=2 $list
if [[ $? -ne 0 ]]; then
  >&2 echo "Unable to install system packages."
  exit 1
fi

exit 0
