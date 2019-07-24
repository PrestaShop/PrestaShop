This module is used to check if translations from modules are found using trans() function,
no matter what is the source of the translation (Xliff or Legacy file system).

The related tests can be executed this using Behat:

``
./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml --name "Legacy Translation System"
``