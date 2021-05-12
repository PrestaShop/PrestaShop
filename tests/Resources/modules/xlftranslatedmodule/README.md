This module is used to check if translations from modules are found using trans() function,
no matter what is the source of the translation (Xliff or Legacy file system).

The related tests can be executed this using Behat:

``
./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml --name "Legacy Translation System"
``

To access this module's controllers:

- BO controller, modern: /admin-dev/index.php/modules/xlftranslatedmodule
- BO controller, legacy: /admin-dev/index.php?controller=AdminXlftranslatedmoduleFoo
- FO controller: index.php?fc=module&module=xlftranslatedmodule&controller=bar
