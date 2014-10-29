#!/bin/bash

# Read a list of items from a file and ignore lines starting with '#' 
readlist() {
  filepath=$1
  packages=$(grep -v '^#' $filepath)
  echo $packages
}
