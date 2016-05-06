#!/usr/bin/env bash

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
export -f php-confd-ini-path

php-conf() {
  local sapi=$1
  local key=$2
  local value=$3
  local ini_path=$(php-confd-ini-path "$sapi" "$key" 00)

  echo "${key} = ${value}" >"$ini_path"
  return $?
}
export -f php-conf

php-conf-all() {
  local key=$1
  local value=$2
  php-conf cli "$key" "$value"
  php-conf fpm "$key" "$value"
  return 0
}
export -f php-conf-all

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
export -f php-extension-enable

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

  echo "Building PECL extension $name"

  cd $TEMP_DIR

  pecl bundle $pecl_name > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': Could not be downloaded"
    return 1
  fi

  cd $name

  phpize > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': phpize failed"
    return 1
  fi

  ./configure > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': ./configure failed"
    return 1
  fi

  make > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': make failed"
    return 1
  fi

  make install > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "Pecl extension '$name': make install failed"
    return 1
  fi

  php-extension-enable $name
  return $?
}
export -f php-pecl-install

php-fpm-restart() {
  systemctl restart "php${PHP_VERSION}-fpm"
  return $?
}
export -f php-fpm-restart

