
menu_phalcon() {
  local project=PHALCON
  local install="${PHALCON_INSTALLER:-$PHALCON_DEFAULT}"
  local method=$(takeMethod "$install")

  local option
  option=$("$DIALOG" \
    --backtitle "$MENU_BACKTITLE" \
    --title "Phalcon Installer method" \
    --notags \
    --default-item $method \
    --menu "Choose the method for installing Phalcon." 11 60 4 \
      clear "Clear Installer" \
      repository "Package Repository" \
      tarball "Tarball" \
      git "Git" \
      3>&1 1>&2 2>&3)
  [[ $? -ne "$DIALOG_OK" ]] && return 0

  local methodNew=$option
  local refNew
  case "$option" in
    "repository")
      refNew=$(menuRepository $project "$install")
      [[ $? -ne 0 ]] && return 0
      ;;
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
    unset PHALCON_INSTALLER
  else
    PHALCON_INSTALLER="${methodNew}:${refNew}"
  fi
}
export -f menu_phalcon
