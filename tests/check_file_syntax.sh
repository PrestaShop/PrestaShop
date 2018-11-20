#!/bin/bash

! (find . -name "*.php" ! -path "./vendor/*" ! -path "./tools/*" -print0 | xargs -0 -n1 -P4 php -l | grep -q "Parse error")
php=$?

#twig tests
php app/console lint:twig src
twig_src=$?

php app/console lint:twig app
twig_app=$?

#yml tests
php app/console lint:yaml src
yaml_src=$?

php app/console lint:yaml app
yaml_app=$?

php app/console lint:yaml themes
yaml_themes=$?

php app/console lint:yaml .t9n.yml
yaml_trad=$?

if [[ "$php" == "0" && "$twig_src" == "0" && "$twig_app" == "0" && "$yaml_src" == "0" && "$yaml_app" == "0" && "$yaml_themes" == "0" && "$yaml_trad == 0" ]]; then
  exit 0;
else
  exit 255;
fi
