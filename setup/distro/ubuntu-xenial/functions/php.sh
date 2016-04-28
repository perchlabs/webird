#!/usr/bin/env bash

PHP_VERSION=7.0
PHP_ETC=/etc/php/$PHP_VERSION

php-confd-ini-path() {
  local sapi=$1
  local ext=$2
  local code=
  if [[ -z "$3" ]];
    then code=20
    else code=$3
  fi

  echo "${PHP_ETC}/$sapi/conf.d/${code}-${ext}.ini"
  return $?
}

php-settings-update() {
  local setting_name=$1
  local setting_value=$2
  local cli_ini=$(php-confd-ini-path cli $setting_name 0)
  local fpm_ini=$(php-confd-ini-path fpm $setting_name 0)

  echo "${setting_name}=${setting_value}" | tee "$cli_ini" "$fpm_ini"
  return $?
}

php-extension-enable() {
  local name=$1
  local code=$2

  local cli_ini=$(php-confd-ini-path cli $name $code)
  local fpm_ini=$(php-confd-ini-path fpm $name $code)
  local mod_ini="${PHP_ETC}/mods-available/$name.ini"

  echo "extension=$name.so" > "$mod_ini"
  [[ $? -ne 0 ]] && exit 1

  if [[ ! -f $cli_ini ]]; then
    ln -s --relative "$mod_ini" "$cli_ini"
    [[ $? -ne 0 ]] && exit 1
  fi
  if [[ ! -f $fpm_ini ]]; then
    ln -s --relative "$mod_ini" "$fpm_ini"
    [[ $? -ne 0 ]] && exit 1
  fi

  return 0
}

# Pecl wants beta packages to be accessed with a -beta postfix but the file
# that is downloaded does not have this postfix. This function is more robust
# than a simple 'pecl install' since it is able to detect installation errors
# at every stage whereas 'pecl install' will not.
php-pecl-install() {
  # Split $1 by '-'
  local args=(${1/-/ })
  local name=${args[0]}
  local state=${args[1]}

  # Allow for alpha and beta packages
  local pecl_name=$name
  [[ ! -z "$state" ]] && pecl_name=$pecl_name-$state

  cd $TEMPDIR

  pecl bundle $pecl_name
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': Could not be downloaded"
    return 1
  fi

  cd $name

  phpize
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': phpize failed"
    return 1
  fi

  ./configure
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': ./configure failed"
    return 1
  fi

  make
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': make failed"
    return 1
  fi

  make install
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': make install failed"
    return 1
  fi

  php-extension-enable $name
  return $?
}

php-fpm-restart() {
  service "php${PHP_VERSION}-fpm" restart
  return $?
}
