#!/bin/bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

[[ -z "$WEBIRD_INTEREST" ]] && exit 0

echo -e "${COLOR_SECTION}*** Webird setup ***${TEXT_RESET}"

echo "SETUP_ROOT_DIR: $SETUP_ROOT_DIR"
echo "OS_DIR: $OS_DIR"

projectRoot=$(readlink -f "$SETUP_ROOT_DIR/..")
echo "projectRoot: $projectRoot"
exit

cd "$projectRoot"

echo "Installing local composer packages"
composer install > /dev/null

echo "Installing local NPM packages"
npm install

# Install dev config template if it doesn't exist already.
devConfigTemplate="$projectRoot/etc/templates/dev_config.json"
devConfigDestination="$projectRoot/etc/dev.json"
if [[ ! -f "$devConfigDestination" ]]; then
  cp "$devConfigTemplate" "$devConfigDestination"
fi

# Install build config template if it doesn't exist already.
buildConfigTemplate="$projectRoot/etc/templates/dist_config.json"
distConfigDestination="$projectRoot/etc/dist.json"
if [[ ! -f "$distConfigDestination" ]]; then
  cp "$distConfigTemplate" "$distConfigDestination"
fi

# Install nginx development
./dev/run nginx | sudo tee "/etc/nginx/sites-available/$WEB_DOMAIN_DEVELOPMENT" 1> /dev/null
sudo ln -s "/etc/nginx/sites-available/$WEB_DOMAIN_DEVELOPMENT" "/etc/nginx/sites-enabled/$WEB_DOMAIN_DEVELOPMENT"

# Install nginx production
./dev/run nginx | sudo tee "/etc/nginx/sites-available/$WEB_DOMAIN_PRODUCTION" 1> /dev/null
sudo ln -s "/etc/nginx/sites-available/$WEB_DOMAIN_PRODUCTION" "/etc/nginx/sites-enabled/$WEB_DOMAIN_PRODUCTION"

exit 0
