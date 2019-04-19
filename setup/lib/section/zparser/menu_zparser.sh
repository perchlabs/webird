
menu_zparser() {
  local project=ZPARSER
  local install="${ZPARSER_INSTALLER:-$ZPARSER_DEFAULT}"
  local method=$(takeMethod "$install")

  local option
  option=$("$DIALOG" \
    --backtitle "$MENU_BACKTITLE" \
    --title "zephir_parser Installer Method" \
    --notags \
    --default-item $method \
    --cancel-button "Return to Customize" \
    --menu "Choose the method for installing zephir_parser." 11 60 3 \
      clear "Clear Installer" \
      "tarball" "Tarball" \
      "git" "Git" \
      3>&1 1>&2 2>&3)
  [[ $? -ne "$DIALOG_OK" ]] && return 0

  local methodNew=$option
  local refNew
  case "$option" in
    "tarball")
      refNew=$(menuTarball $project "$install")
      [[ $? -ne 0 ]] && return 0
      ;;
    "git")
      refNew=$(menuGit $project "$install")
      [[ $? -ne 0 ]] && return 0
      ;;
  esac

  if [[ "$option" == "clear" ]]; then
    unset ZPARSER_INSTALLER
  else
    ZPARSER_INSTALLER="${methodNew}:${refNew}"
  fi

}
export -f menu_zparser
