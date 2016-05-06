#!/usr/bin/env bash
# Environment variables DISTRO_DIR and TEMP_DIR are available

[[ "$SKIP_PECL" = true ]] && exit 0

list=$(readlist pecl)
for extension in $list
do
  php-pecl-install $extension
  [[ $? -ne 0 ]] && exit 1
done

exit 0
