#!/bin/bash

! (find . -name "*.php" ! -path "./vendor/*" ! -path "./tools/*" -print0 | xargs -0 -n1 -P4 php -l | grep -q "Parse error")
php=$?

# Yml tests
php bin/console lint:yaml src
yaml_src=$?

php bin/console lint:yaml app
yaml_app=$?

php bin/console lint:yaml themes
yaml_themes=$?

php bin/console lint:yaml .t9n.yml
yaml_trad=$?

# Check our current PHP Coding Style rules
php ./vendor/bin/php-cs-fixer fix --dry-run --stop-on-violation --show-progress=dot
coding_styles=$?

if [[ "$php" == "0" && "$yaml_src" == "0" && "$yaml_app" == "0" && "$yaml_themes" == "0" && "$yaml_trad" == "0" && "$coding_styles" == "0" ]]; then
  echo -e "\e[92mSYNTAX TESTS OK"
  exit 0;
else
  echo -e "\e[91mSYNTAX TESTS FAILED"
  exit 255;
fi
