#!/bin/bash

# YAML
php bin/console lint:yaml src
yaml_src=$?

php bin/console lint:yaml app
yaml_app=$?

php bin/console lint:yaml themes
yaml_themes=$?

php bin/console lint:yaml .t9n.yml
yaml_trad=$?

# Twig
php bin/console lint:twig src/PrestaShopBundle/Resources/views/
twig_src=$?

if [[ "$yaml_src" == "0" && "$yaml_app" == "0" && "$yaml_themes" == "0" && "$yaml_trad" == "0" && "twig_src" == "0" ]]; then
  echo -e "\e[92mSYNTAX TESTS OK"
  exit 0;
else
  echo -e "\e[91mSYNTAX TESTS FAILED"
  exit 255;
fi
