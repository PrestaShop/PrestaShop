#!/bin/bash

find . -name "*.php" ! -path "./vendor/*" ! -path "./tools/*" -print0 | while IFS= read -r -d '' file; do
  if php -l $file | grep -q "Parse error"
  then
    exit 1;
  fi;
done
