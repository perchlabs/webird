
menu_nodejs() {
  local project=NODEJS
  local installer="${NODEJS_INSTALLER:-$NODEJS_DEFAULT}"
  local method=$(takeMethod "$installer")

  local option
  option=$("$DIALOG" \
    --backtitle "$MENU_BACKTITLE" \
    --title "Node.js Installer Method" \
    --notags \
    --default-item $method \
    --menu "Choose the method for installing Node.js." 9 60 2 \
      clear "Clear Installer" \
      repository "Package Repository" \
      3>&1 1>&2 2>&3)
  [[ $? -ne "$DIALOG_OK" ]] && return 0

  local methodNew=$option
  local refNew
  case "$option" in
    "repository")
      refNew=$(menuRepository $project "$install")
      [[ $? -ne 0 ]] && return 0
      ;;
    # "tarball")
    #   refNew=$(menuTarball $project "$install")
    #   [[ $? -ne 0 ]] && return 0
    #   ;;
  esac

  if [[ "$option" == "clear" ]]; then
    unset NODEJS_INSTALLER
  else
    NODEJS_INSTALLER="${methodNew}:${refNew}"
  fi
}
export -f menu_nodejs
