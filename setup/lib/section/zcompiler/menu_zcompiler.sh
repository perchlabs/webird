
menu_zcompiler() {
  local msg
  read -r -d '' msg << EOM
Choose the method for installing Zephir.
 _____              __    _
/__  /  ___  ____  / /_  (_)____
  / /  / _ \/ __ \/ __ \/ / ___/
 / /__/  __/ /_/ / / / / / /
/____/\___/ .___/_/ /_/_/_/
         /_/
EOM

  local project=ZCOMPILER
  local installer="${ZCOMPILER_INSTALLER:-$ZCOMPILER_DEFAULT}"
  local method=$(takeMethod "$installer")

  local option
  option=$("$DIALOG" \
    --backtitle "$MENU_BACKTITLE" \
    --title "Zephir Installer Method" \
    --notags \
    --default-item $method \
    --cancel-button "Return to Overview" \
    --menu "$msg" 17 80 4 \
      clear "Clear Installer" \
      phar "Phar" \
      tarball "Tarball" \
      git "Git" \
      3>&1 1>&2 2>&3)
  [[ $? -ne "$DIALOG_OK" ]] && return 0

  local methodNew=$option
  local refNew
  case "$option" in
    "phar")
      refNew=$(menuPhar $project "$installer")
      [[ $? -ne 0 ]] && return 0
      ;;
    "tarball")
      refNew=$(menuTarball $project "$installer")
      [[ $? -ne 0 ]] && return 0
      ;;
    "git")
      refNew=$(menuGit $project "$installer")
      [[ $? -ne 0 ]] && return 0
      ;;
  esac

  if [[ "$option" == "clear" ]]; then
    unset ZCOMPILER_INSTALLER
  else
    ZCOMPILER_INSTALLER="${methodNew}:${refNew}"
  fi
}
export -f menu_zcompiler
