
initCommon() {
  local osName=$1
  local shouldPerformMaximumSteps=$2

  if [[ -z "$osName" ]]; then
    >&2 echo "An OS name must be specified for installation."
    >&2 echo "Possible OS names include:"
    >&2 ls -1 "$SETUP_ROOT_DIR/os"
    exit 1
  fi

  export OS_DIR="$SETUP_ROOT_DIR/os/$osName"
  if [[ ! -d "$OS_DIR" ]]; then
    >&2 echo "An installation directory does not exist for '$osName'."
    exit 1
  fi
  export TEMP_DIR=$(mktemp -d)

  # Reset all font settings
  export TEXT_RESET='\e[0m'

  # Automatically export the variables in these files.
  set -a
  source "$SETUP_ROOT_DIR/settings"
  source "$LIB_DIR/data.sh"

  # Define interests while preserving existing values.
  local interestLine
  local interestVar
  local regex="([A-Z]+_INTEREST)"
  local interestLines=$(egrep -nd recurse '[\$|\{][A-Z]+_INTEREST' settings "$OS_DIR")
  for interestLine in $interestLines; do
    if [[ "$interestLine" =~ $regex ]]; then
      local interestVar=${BASH_REMATCH[1]}
      declare -g $interestVar=${!interestVar}
      export "$interestVar"
    fi
  done

  # Load a data file for each section.
  local sectionNames=$(getSectionNames)
  local sectionName
  for sectionName in $sectionNames; do
    local sectionDataPath="$LIB_DIR/section/${sectionName}/data.sh"
    if [[ -f "$sectionDataPath" ]]; then
      source "$sectionDataPath"
    fi
  done
  set +a

  if [[ ! -z "$shouldPerformMaximumSteps" ]]; then
    loadInstallers
    enableAllInterests
  fi

  # Load operating system settings.
  set -a
  source "$OS_DIR/os.sh"
  set +a
}
export -f initCommon


loadInstallers() {
  local defaultVarList=$(compgen -v | grep -E '^[A-Z]+_DEFAULT$')
  local regex="(.+)_DEFAULT$"
  local defaultVar
  local installerVar
  local section

  set -a
  for defaultVar in $defaultVarList; do
    [[ "$defaultVar" =~ $regex ]] && installerVar="${BASH_REMATCH[1]}_INSTALLER"

    local installerVal="${!installerVar}"
    local installerDefault="${!defaultVar}"

    declare -g $installerVar="${installerVal:-$installerDefault}"
  done
  set +a
}
export -f loadInstallers


clearInstallers() {
  local installVarList=$(compgen -v | grep -E '^[A-Z]+_INSTALLER$')
  local installVar

  set -a
  for installVar in $installVarList; do
    unset "$installVar"
  done
  set +a
}
export -f clearInstallers


enableAllInterests() {
  set -a

  local interestVarList=$(compgen -v | grep -E '^[A-Z]+_INTEREST$')
  local regex='^(.+)_INTEREST$'
  for interestVar in $interestVarList; do
    declare -g $interestVar=1
  done

  set +a
}
export -f enableAllInterests


disableAllInterests() {
  set -a

  local interestVarList=$(compgen -v | grep -E '^[A-Z]+_INTEREST$')
  local regex='^(.+)_INTEREST$'
  for interestVar in $interestVarList; do
    declare -g $interestVar=''
  done

  set +a
}
export -f disableAllInterests


startInstallation() {
  # Source OS functions
  local functions=$(find "$OS_DIR/functions" -maxdepth 1 -type f)
  local fscript
  for fscript in $functions; do
    source "$fscript"
  done

  # Find all of the files that begin with two digits and sort them.
  local scripts=$(find "$OS_DIR/init.d" -maxdepth 1 -type f -name "[0-9][0-9]-*" | sort)
  local script
  for script in $scripts; do
    "$script"
    if [[ "$?" -ne 0 ]]; then
      >&2 echo -e "${COLOR_ERROR}ERROR${TEXT_RESET} in init.d script ${script}"
      >&2 echo "All Installation resources are located at: $TEMP_DIR"
      return 1
    fi
  done

  echo "All Installation resources are located at: $TEMP_DIR"
  printf "${COLOR_NOTICE}SUCCESS!\n${TEXT_RESET}"
}
export -f startInstallation


getSectionNames() {
  local sectionNames
  sectionNames=$(ls -d "$LIB_DIR"/section/*/ | xargs -n 1 basename)
  [[ $? -ne 0 ]] && return 1

  echo $sectionNames
}
export -f getSectionNames


readlist() {
  echo $(grep -v -e '^#' -e '^$' "$OS_DIR/lists/$1")
}
export -f readlist


# Take only the first method field.
takeMethod() {
  [[ -z "$1" ]] && echo "" || echo "$1" | cut -d: -f1
}
export -f takeMethod


takeRef() {
  echo "$1" | cut -d: -f2-
}
export -f takeRef


# Take everything after the first field.
takeRefFirst() {
  echo "$1" | cut -d: -f2
}
export -f takeRefFirst


takeRefRest() {
  echo "$1" | cut -d: -f3-
}
export -f takeRefRest


function isUrl() {
  local ref=$1
  local regex='^[a-z]+://'
  [[ "$ref" =~ $regex ]];
  return $?
}
export -f isUrl
