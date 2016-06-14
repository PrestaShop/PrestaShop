#!/bin/bash

! (find . -name "*.php" ! -path "./vendor/*" ! -path "./tools/*" -print0 | xargs -0 -n1 -P4 php -l | grep -q "Parse error")
php=$?

php app/console lint:twig src
twig=$?
php app/console lint:yaml themes/classic/config/theme.yml
yaml=$?

if [[ "$php" == "0" && "$twig" == 0 && "$yaml == 0" ]]; then
  exit 0;
else
  exit 255;
fi
