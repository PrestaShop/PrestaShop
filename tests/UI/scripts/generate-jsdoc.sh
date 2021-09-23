#!/bin/bash

# To print only errors
set +x

DOC_DIRECTORY=".doc"
PAGES_DIRECTORY="pages"
FAKER_DIRECTORY="campaigns/data/faker"

# Create .doc directory
mkdir -p $DOC_DIRECTORY

# Function to generate documentation
function generate_doc() {
  JS_DIRECTORY=$1
  JS_DIRECTORIES=$(find $JS_DIRECTORY -type d)
  JS_FILES=($(find $JS_DIRECTORY -type f -name \*.js))

  # 1 Create directories
  cd $DOC_DIRECTORY
  mkdir -p $JS_DIRECTORIES

  # 2 Create documentation for each file
  cd ..
  for JS_FILE in "${JS_FILES[@]}"; do
    jsdoc2md --no-gfm --files $JS_FILE > $DOC_DIRECTORY/$JS_FILE.md
  done

  echo "File generated for '$JS_DIRECTORY', Check errors if printed"
}

# 1. Generate documentation for pages directory
generate_doc $PAGES_DIRECTORY

# 2. Generate documentation for campaigns/data/faker directory
generate_doc $FAKER_DIRECTORY
