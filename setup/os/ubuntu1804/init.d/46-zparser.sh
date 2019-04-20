#!/usr/bin/env bash
# Environment variables SETUP_ROOT_DIR, OS_DIR and TEMP_DIR are available

method=$(takeMethod "$ZPARSER_INSTALLER")
[[ -z "$method" ]] && exit 0

echo -e "${COLOR_SECTION}*** zephir_parser ***${TEXT_RESET}"

cd "$TEMP_DIR"

case "$method" in
  "git")
    echo "Git cloning zephir_parser repository"
    gitBranch=$(takeRefFirst "$ZPARSER_INSTALLER")
    gitUrl=$(takeRefRest "$ZPARSER_INSTALLER")

    git clone --depth=1 -b "$gitBranch" "$gitUrl" php-zephir-parser > /dev/null
    [[ $? -ne 0 ]] && exit 1
    cd php-zephir-parser
    [[ $? -ne 0 ]] && exit 1
    ;;
  "tarball")
    ref=$(takeRef "$ZPARSER_INSTALLER")
    downloadDir="$TEMP_DIR/zparser"

    mkdir "$downloadDir"
    cd "$downloadDir"

      # The ref is a url or version.
    isUrl "$ref"
    if [[ $? -eq 0 ]]; then
      url="$ref"
    else
      url="https://github.com/phalcon/php-zephir-parser/archive/v${ref}.tar.gz"
    fi

    echo "Downloading zephir_parser tarball"

    tarballFile="php-zephir-parser.tarball"
    curl --silent -L -o "$tarballFile" "$url"
    [[ $? -ne 0 ]] && exit 1

    tar -xf "$tarballFile"
    [[ $? -ne 0 ]] && exit 1

    mysteryDirName=$(ls -d ./*/)
    [[ $? -ne 0 ]] && exit 1

    cd "$mysteryDirName"
    [[ $? -ne 0 ]] && exit 1
    ;;
  *)
    echo "Invalid zephir_parser installation method."
    exit 1
    ;;
esac

# The current directory should be the zephir_parser source.
phpize
[[ $? -ne 0 ]] && exit 1

./configure --quiet
[[ $? -ne 0 ]] && exit 1

make > /dev/null
[[ $? -ne 0 ]] && exit 1

sudo make install
[[ $? -ne 0 ]] && exit 1

phpExtensionEnableAll zephir_parser 50
