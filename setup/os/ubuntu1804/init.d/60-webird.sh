#!/bin/bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

[[ -z "$WEBIRD_INTEREST" ]] && exit 0

echo -e "${COLOR_SECTION}*** Webird setup ***${TEXT_RESET}"

projectRoot=$(readlink -f "$SETUP_ROOT_DIR/..")
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
prodConfigTemplate="$projectRoot/etc/templates/prod_config.json"
prodConfigDestination="$projectRoot/etc/prod.json"
if [[ ! -f "$prodConfigDestination" ]]; then
  cp "$prodConfigTemplate" "$prodConfigDestination"
fi

# Install nginx development configuration
./dev/run nginx | sudo tee "/etc/nginx/sites-available/$WEB_DOMAIN_DEVELOPMENT" 1> /dev/null
sudo ln -sf "/etc/nginx/sites-available/$WEB_DOMAIN_DEVELOPMENT" "/etc/nginx/sites-enabled/$WEB_DOMAIN_DEVELOPMENT"

exit 0
