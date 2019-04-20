#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

method=$(takeMethod "$NODEJS_INSTALLER")
[[ -z "$method" ]] && exit 0

echo -e "${COLOR_SECTION}*** Node.js ***${TEXT_RESET}"

cd "$TEMP_DIR"

packageName=nodejs

# If the Node.js installation method is something other than repository
# then remove the package from the system so that it doesn't interfere
# with other installation methods.
if [[ "$method" != repository ]]; then
  dpkg -s "$packageName" > /dev/null 2>&1
  [[ $? -eq 0 ]] && sudo apt-get remove "$packageName"
fi

# Determine the method used for installing Node.js.
case "$method" in
  "")
    echo "No installer specified."
    exit 0
    ;;
  # "tarball")
  #   ref=$(takeRef "$NODEJS_INSTALLER")
  #   downloadDir="$TEMP_DIR/nodejs"

  #   mkdir "$downloadDir"
  #   cd "$downloadDir"

  #   # The ref is a url or version.
  #   isUrl "$ref"
  #   isRefUrl=$?
  #   if [[ $isRefUrl -eq 0 ]]; then
  #     url="$ref"
  #   else
  #     version="$ref"
  #     url="https://github.com/nodejs/node/archive/v${version}.tar.gz"
  #   fi

  #   echo "Downloading Node tarball"
  #   tarballFile="node.tarball"
  #   curl --silent -L -o "$tarballFile" "$url"
  #   [[ $? -ne 0 ]] && exit 1

  #   tar -xf "$tarballFile"
  #   [[ $? -ne 0 ]] && exit 1

  #   mysteryDirName=$(ls -d ./*/)
  #   [[ $? -ne 0 ]] && exit 1

  #   cd "$mysteryDirName"
  #   [[ $? -ne 0 ]] && exit 1
  #   ;;
  "repository")
    sudo apt-get install --quiet=2 "$packageName"
    [[ $? -ne 0 ]] && exit 1
    ;;
  *)
    >&2 echo "Invalid Node installation method."
    exit 1
    ;;
esac

printf "Node.js installed.\n"
