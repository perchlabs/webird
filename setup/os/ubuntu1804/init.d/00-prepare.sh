#!/bin/bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

echo -e "${COLOR_SECTION}*** Initialization ***${TEXT_RESET}"

echo "Obtaining sudo capabilities"
sudo ls / > /dev/null
if [[ $? -ne 0 ]]; then
  >&2 echo -e "FAILED to obtain sudo access"
  exit 1
fi

# Create the tool install directories if they don't exist.
mkdir -p ~/bin/ "$SOFTWARE_INSTALL_ROOT"

exit 0
