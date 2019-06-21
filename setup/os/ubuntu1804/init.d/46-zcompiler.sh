#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

method=$(takeMethod "$ZCOMPILER_INSTALLER")
[[ -z "$ZCOMPILER_INTEREST" || -z "$method" ]] && exit 0

echo -e "${COLOR_SECTION}*** Zephir ***${TEXT_RESET}"

symlinkPath=~/bin/zephir

cd "$TEMP_DIR"

# Install Zephir
case "$method" in
  "git")
    installDir="${SOFTWARE_INSTALL_ROOT}/zephir"
    gitBranch=$(takeRefFirst "$ZCOMPILER_INSTALLER")
    gitUrl=$(takeRefRest "$ZCOMPILER_INSTALLER")

    echo "Git cloning Zephir repository"

    git clone --depth=1 -b "$gitBranch" "$gitUrl" zephir > /dev/null
    [[ $? -ne 0 ]] && exit 1
    mv zephir "$installDir"
    [[ $? -ne 0 ]] && exit 1
    ln -fs "${installDir}/zephir" "$symlinkPath"
    [[ $? -ne 0 ]] && exit 1

    echo "Installed in $installDir"
    echo "Symlink installed at $symlinkPath"
    ;;
  "tarball")
    ref=$(takeRef "$ZCOMPILER_INSTALLER")
    downloadDir="$TEMP_DIR/zcompiler"

    mkdir "$downloadDir"
    cd "$downloadDir"

      # The ref is a url or version.
    isUrl "$ref"
    isRefUrl=$?
    if [[ $isRefUrl -eq 0 ]]; then
      name="zephir"
      url="$ref"
    else
      version="$ref"
      name="zephir-${version}"
      url="https://github.com/phalcon/zephir/archive/${version}.tar.gz"
    fi

    installDir="${SOFTWARE_INSTALL_ROOT}/${name}"

    # If the directory already exists.
    if [[ -d "$installDir" ]]; then

      # Don't install again if using a version string.
      if [[ $isRefUrl -ne 0 ]]; then
        echo "Zephir version $version was already installed from tarball."
        exit 0
      fi

      rm -Rf "$installDir"
    fi

    echo "Downloading Zephir tarball"
    tarballFile="zephir-parser.tarball"
    curl --silent -L -o "$tarballFile" "$url"
    [[ $? -ne 0 ]] && exit 1

    tar -xf "$tarballFile"
    [[ $? -ne 0 ]] && exit 1

    mysteryDirName=$(ls -d ./*/)
    [[ $? -ne 0 ]] && exit 1

    cd "$mysteryDirName"
    [[ $? -ne 0 ]] && exit 1

    echo "Installing Zephir Composer packages."
    # composer install --quiet > /dev/null 2>&1
    composer install --quiet
    [[ $? -ne 0 ]] && exit 1

    mv "${downloadDir}/${mysteryDirName}" "$installDir"
    [[ $? -ne 0 ]] && exit 1

    ln -fs "${installDir}/zephir" "$symlinkPath"
    [[ $? -ne 0 ]] && exit 1

    echo "Installed Zephir in $installDir"
    echo "Symlink installed at $symlinkPath"
    ;;
  "phar")
    ref=$(takeRef "$ZCOMPILER_INSTALLER")

      # The ref is a url or version.
    isUrl "$ref"
    isRefUrl=$?
    if [[ $isRefUrl -eq 0 ]]; then
      name="zephir"
      url="$ref"
    else
      version="$ref"
      name="zephir-${version}"
      url="https://github.com/phalcon/zephir/releases/download/${version}/zephir.phar"
    fi

    installFile="${SOFTWARE_INSTALL_ROOT}/${name}.phar"

    if [[ -f "$installFile" ]]; then

      # Don't install again if using a version string.
      if [[ $isRefUrl -ne 0 ]]; then
        echo "Zephir version $version was already installed from PHAR."
        exit 0
      fi

      rm "$installFile"
    fi

    echo "Downloading Zephir PHAR"
    pharFile="zephir.phar"
    curl --silent -L -o "$pharFile" "$url"
    [[ $? -ne 0 ]] && exit 1

    chmod a+x "$pharFile"
    [[ $? -ne 0 ]] && exit 1
    mv -f "$TEMP_DIR/$pharFile" "$installFile"
    [[ $? -ne 0 ]] && exit 1

    ln -fs "$installFile" "$symlinkPath"
    [[ $? -ne 0 ]] && exit 1

    echo "Installed Zephir PHAR at $installFile"
    echo "Symlink installed at $symlinkPath"
    ;;
  *)
    echo "Invalid Zephir installation method."
    exit 1
    ;;
esac
