#!/bin/bash

# To print only errors
set +x

# Get tests/UI directory path
PROJECT_PATH=$(cd "$( dirname "$0" )/../" && pwd)
cd $PROJECT_PATH

DOC_DIRECTORY=".doc"
PAGES_DIRECTORY="pages"
FAKER_DIRECTORY="data/faker"
UTILS_DIRECTORY="utils"

# Create .doc directory
mkdir -p $PROJECT_PATH/$DOC_DIRECTORY

# Function to generate documentation
function generate_doc() {
  local JS_DIRECTORY=$1
  local JS_DIRECTORIES=$(find $JS_DIRECTORY -type d)
  local JS_FILES=($(find $JS_DIRECTORY -type f -name \*.js))

  echo "Start generating files for '$JS_DIRECTORY'"
  # 1 Create directories
  pushd $PROJECT_PATH/$DOC_DIRECTORY
  mkdir -p $JS_DIRECTORIES
  popd

  # 2 Create documentation for each file
  pushd $PROJECT_PATH
  for JS_FILE in "${JS_FILES[@]}"; do
    ./node_modules/.bin/jsdoc2md --no-gfm --files $PROJECT_PATH/$JS_FILE > $PROJECT_PATH/$DOC_DIRECTORY/$JS_FILE.md
  done
  popd

  echo "File generated for '$JS_DIRECTORY', Check errors if printed"
}

# 1. Generate documentation for pages directory
generate_doc $PAGES_DIRECTORY

# 2. Generate documentation for data/faker directory
generate_doc $FAKER_DIRECTORY

# 3. Generate documentation for utils directory
generate_doc $UTILS_DIRECTORY
