#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

method=$(takeMethod "$PHALCON_INSTALLER")
[[ -z "$method" ]] && exit 0

# Remove existing installed Phalcon repositories.
echo "Removing any existing Phalcon repositories"
sudo rm -f /etc/apt/sources.list.d/phalcon*.list > /dev/null

# If the Phalcon method is not 'repository' then exit
[[ "$method" != repository ]] && exit 0

ref=$(takeRef "$PHALCON_INSTALLER")

# Install the Phalcon repository.
echo "Installing Phalcon repository for '$ref'"
curl -s "https://packagecloud.io/install/repositories/phalcon/${ref}/script.deb.sh" | sudo bash
if [[ $? -ne 0 ]]; then
  >&2 echo "Unable to install Phalcon repository."
  exit 1
fi

exit 0
