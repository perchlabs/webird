#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

method=$(takeMethod "$NODEJS_INSTALLER")
[[ -z "$method" ]] && exit 0

# Remove existing repositories.
echo "Removing any existing Node repositories"
sudo rm -f /etc/apt/sources.list.d/nodesource.list > /dev/null

[[ "$method" != repository ]] && exit 0

echo "Installing Node repository"
ref=$(takeRef "$NODEJS_INSTALLER")

echo "Install Nodejs repository for '${ref}'"
curl -sL "https://deb.nodesource.com/setup_${ref}.x" | sudo -E bash -
if [[ $? -ne 0 ]]; then
  >&2 echo "Unable to install Nodejs repository."
  exit 1
fi

exit 0
