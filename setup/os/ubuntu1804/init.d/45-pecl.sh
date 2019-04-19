#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

[[ -z "$PECL_INTEREST" ]] && exit 0

echo -e "${COLOR_SECTION}*** PECL extensions ***${TEXT_RESET}"

list=$(readlist pecl)
for line in $list; do
  regex='^([0-9]+-){0,1}([a-z]+)(-[a-z0-9-]+){0,1}$'
  if [[ ! "$line" =~ $regex ]]; then
    >&2 echo "Bad line in PECL list"
    exit 1
  fi

  code=${BASH_REMATCH[1]//-}
  extension=${BASH_REMATCH[2]}
  state=${BASH_REMATCH[3]//-}

  echo -e "${COLOR_NOTICE}${extension}${TEXT_RESET}"

  phpPeclInstall "$extension" "$state"
  if [[ $? -ne 0 ]]; then
    >&2 echo "There was a problem installing PECL extension '$extension'"
    exit 1
  fi

  phpExtensionEnableAll "$extension" "$code"
  if [[ $? -ne 0 ]]; then
    >&2 echo "There was a problem enabling PECL extension '$extension'"
    exit 1
  fi
done
