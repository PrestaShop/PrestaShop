# PrestaShop Tests with Puppeteer (Docker version)
This file will explain to you how to run tests in puppeteer with docker-compose.

### LinkChecker

```bash
# Create Shop and running test
docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml up --build
docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml exec -e COMMAND="linkchecker" tests bash /tmp/run-tests.sh
```

### Sanity tests

```bash
# Create Shop and running test
docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml up --build
docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml exec -e COMMAND="sanity-tests" tests bash /tmp/run-tests.sh
```

### Functional tests

```bash
# Create Shop and running test
docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml up --build
docker-compose -f docker-compose.nightly.yml -f docker-compose.tests.yml exec -e COMMAND="functional-tests" tests bash /tmp/run-tests.sh
```

### Upgrade test
For this specific test you need to specify which Prestashop Version you need to install.

For that you can Add PS_VERSION_TO_INSTALL Env Variable and use docker-compose.ps-git.yml to install prestashop.

To test an upgrade from 1.7.5.2 to last version

```bash
# Create shop version 1.7.5.2
PS_VERSION_TO_INSTALL=1.7.5.2 docker-compose -f docker-compose.ps-git.yml -f docker-compose.tests.yml up --build
# Testing upgrade from 1.7.5.2 to 1.7.6.0
docker-compose -f docker-compose.tests.yml -f docker-compose.ps-git.yml exec -e COMMAND="specific-test" -e PS_VERSION="1.7.6.0" -e TEST_PATH="upgrade/upgradeShop" tests bash /tmp/run-tests.sh
```

Enjoy :wink: :v:
