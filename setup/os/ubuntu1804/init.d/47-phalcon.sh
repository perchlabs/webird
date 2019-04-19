#!/usr/bin/env bash
# Environment variables OS_DIR and TEMP_DIR are available

method=$(takeMethod "$PHALCON_INSTALLER")
[[ -z "$method" ]] && exit 0

echo -e "${COLOR_SECTION}*** Phalcon ***${TEXT_RESET}"

cd "$TEMP_DIR"

packageName="php${PHP_VERSION}-phalcon"

# If the Phalcon installation method is something other than repository
# then remove the package from the system so that it doesn't interfere
# with other installation methods.
if [[ "$method" != repository ]]; then
  dpkg -s "$packageName" > /dev/null 2>&1
  [[ $? -eq 0 ]] && sudo apt-get remove "$packageName"
fi

# Determine the method used for installing Phalcon.
case "$method" in
  "git")
    echo "Git cloning Phalcon repository"
    gitBranch=$(takeRefFirst "$PHALCON_INSTALLER")
    gitUrl=$(takeRefRest "$PHALCON_INSTALLER")

    git clone --quiet --depth=1 -b "$gitBranch" "$gitUrl" cphalcon > /dev/null
    [[ $? -ne 0 ]] && exit 1

    cd cphalcon
    [[ $? -ne 0 ]] && exit 1

    # Compile and install Phalcon source.
    zephir --quiet install > /dev/null
    if [[ $? -ne 0 ]]; then
      >&2 echo "Unable to compile and install Phalcon."
      exit 1
    fi
    ;;
  "tarball")
    ref=$(takeRef "$PHALCON_INSTALLER")
    downloadDir="$TEMP_DIR/phalcon"

    mkdir "$downloadDir"
    cd "$downloadDir"

    # The ref is a url or version.
    isUrl "$ref"
    isRefUrl=$?
    if [[ $isRefUrl -eq 0 ]]; then
      url="$ref"
    else
      version="$ref"
      url="https://github.com/phalcon/cphalcon/archive/v${version}.tar.gz"
    fi

    echo "Downloading Phalcon tarball"
    tarballFile="phacon.tarball"
    curl --silent -L -o "$tarballFile" "$url"
    [[ $? -ne 0 ]] && exit 1

    tar -xf "$tarballFile"
    [[ $? -ne 0 ]] && exit 1

    mysteryDirName=$(ls -d ./*/)
    [[ $? -ne 0 ]] && exit 1

    cd "$mysteryDirName"
    [[ $? -ne 0 ]] && exit 1

    # Compile and install Phalcon source.
    zephir --quiet install > /dev/null
    if [[ $? -ne 0 ]]; then
      >&2 echo "Unable to compile and install Phalcon."
      exit 1
    fi
    ;;
  "repository")
    sudo apt-get install --quiet=2 "$packageName"
    [[ $? -ne 0 ]] && exit 1
    ;;
  *)
    >&2 echo "Invalid Phalcon installation method."
    exit 1
    ;;
esac

phpExtensionEnableAll phalcon 50
if [[ $? -ne 0 ]]; then
  >&2 echo "Unable to enable Phalcon extension."
  exit 1
fi

printf "Phalcon extension installed.\n"
