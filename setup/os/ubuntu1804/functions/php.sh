
phpConfdIniPath() {
  local sapi="$1"
  local ext="$2"
  [[ -z "$3" ]] && local code=20 || local code="$3"

  echo "${PHP_ETC}/$sapi/conf.d/${code}-${ext}.ini"
}
export -f phpConfdIniPath


phpConf() {
  local sapi="$1"
  local key="$2"
  local value="$3"
  local iniPath=$(phpConfdIniPath "$sapi" "$key" 00)

  echo "$key = $value" | sudo tee "$iniPath" > /dev/null
}
export -f phpConf


phpConfAll() {
  local key="$1"
  local value="$2"

  local sapi
  for sapi in "$PHP_SAPI_LIST"; do
    phpConf "$sapi" "$key" "$value"
    [[ $? -ne 0 ]] && return 1
  done

  return 0
}
export -f phpConfAll


phpIsUsingSapi() {
  local sapi="$1"

  echo "$PHP_SAPI_LIST" | grep -w "$sapi" > /dev/null
}
export -f phpIsUsingSapi


phpExtensionEnableSapi() {
  local sapi="$1"
  local name="$2"
  local code="$3"

  local modIniPath="${PHP_ETC}/mods-available/$name.ini"
  local sapiIniPath=$(phpConfdIniPath $sapi $name $code)

  echo "extension=$name.so" | sudo tee "$modIniPath" > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "Enabling PHP extension '$name' failed."
    return 1
  fi

  if [[ ! -f "$sapiIniPath" ]]; then
    sudo ln -s --relative "$modIniPath" "$sapiIniPath"
    if [[ $? -ne 0 ]]; then
      >&2 echo "Enabling PHP extension '$name' failed."
      return 1
    fi
  fi

  return 0
}
export -f phpExtensionEnableSapi


phpExtensionEnableAll() {
  local name="$1"
  local code="$2"

  local sapi
  for sapi in "$PHP_SAPI_LIST"; do
    phpExtensionEnableSapi "$sapi" "$name" "$code"
    [[ $? -ne 0 ]] && return 1
  done

  return 0
}
export -f phpExtensionEnableAll


# Pecl wants beta packages to be accessed with a -beta postfix but the file
# that is downloaded does not have this postfix. This function is more robust
# than a simple 'pecl install' since it is able to detect installation errors
# at every stage whereas 'pecl install' will not.
phpPeclInstall() {
  local name=$1
  local state=$2

  # Allow for alpha and beta packages
  local peclName=$name
  [[ ! -z "$state" ]] && peclName=$peclName-$state

  cd "$TEMP_DIR"

  pecl bundle $peclName > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "PECL extension '$name': Could not be downloaded."
    return 1
  fi

  cd "$name"
  if [[ $? -ne 0 ]]; then
    >&2 echo "Could not change to PECL extension directory '$name'."
    return 1
  fi

  phpize > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "PECL extension '$name': phpize failed."
    return 1
  fi

  ./configure > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "PECL extension '$name': ./configure failed."
    return 1
  fi

  make > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "PECL extension '$name': make failed."
    return 1
  fi

  sudo make install > /dev/null
  if [[ $? -ne 0 ]]; then
    >&2 echo "PECL extension '$name': make install failed."
    return 1
  fi

  return 0
}
export -f phpPeclInstall
