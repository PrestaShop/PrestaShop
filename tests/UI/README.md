# PrestaShop UI Tests

## Requirement

Before begin working on tests, make sure you have installed

* [nodejs](https://nodejs.org/) v10.x or newer
* [npm](https://www.npmjs.com/) v6.x or newer

## How to install your environment

```bash
# Clone Prestashop
git clone https://github.com/PrestaShop/PrestaShop/
# Install dependencies in UI folder
cd tests/UI/
npm ci
```

## Available command line parameters

### PrestaShop parameters

| Parameter          | Description                                                                                 |
|--------------------|---------------------------------------------------------------------------------------------|
| URL_FO             | URL of your PrestaShop website Front Office (default to **`http://localhost/prestashop/`**) |
| URL_BO             | URL of your PrestaShop website Back Office (default to **`URL_FO + admin-dev/`**)           |
| URL_INSTALL        | URL of the Install folder (default to **`URL_FO + install-dev/`**)                          |
| ENABLE_SSL         | Enable SSL (default to **`false`**)                                                         |
| FIRSTNAME          | Firstname of your admin employee (default to **`demo`**)                                    |
| LASTNAME           | Lastname of your admin employee (default to **`demo`**)                                     |
| LOGIN              | LOGIN of your PrestaShop website (default to **`demo@prestashop.com`**)                     |
| PASSWD             | PASSWD of your PrestaShop website (default to **`prestashop_demo`**)                        |
| SHOP_NAME          | Shop Name of tour PrestaShop (default to **`Prestashop`**)                                  |
| DB_SERVER          | The Database server address (default to **`127.0.0.1`**)                                    |
| DB_USER            | Login user of your MySql (default to **`root`**)                                            |
| DB_NAME            | Name of the MySql database (default to **`prestashop_db`**)                                 |
| DB_PASSWD          | Password for your MySql (default to **`empty`**)                                            |
| DB_PREFIX          | Prefix for the database tables (default to **`tst_`**)                                      |
| PS_PARAMETERS_FILE | Parameters files (default to **`app/config/parameters.php`**)                               |

### Maildev parameters

| Parameter   | Description                                                       |
|-------------|-------------------------------------------------------------------|
| SMTP_SERVER | The smtp server address for maildev (default to **`172.20.0.4`**) |
| SMTP_PORT   | The smtp port for maildev (default to **`1025`**)                 |

### Keycloak parameters

| Parameter             | Description                                                                              |
|-----------------------|------------------------------------------------------------------------------------------|
| KEYCLOAK_URL_EXTERNAL | The external URL for Keycloak (default to **`http://127.0.0.1:8003`**) (outside Docker)  |
| KEYCLOAK_URL_INTERNAL | The internal URL for Keycloak (default to **`http://keycloak:8080`**) (inside Docker)    |
| KEYCLOAK_ADMIN_USER   | The admin user for connecting to Keycloak (default to **`admin`**)                       |
| KEYCLOAK_ADMIN_PASS   | The admin password for connecting to Keycloak (default to **`admin`**)                   |
| KEYCLOAK_CLIENT_ID    | The Client ID for using in PrestaShop & Keycloak (default to **`prestashop_client_id`**) |

### Playwright parameters

| Parameter | Description                                                                                                             |
|-----------|-------------------------------------------------------------------------------------------------------------------------|
| BROWSER   | Specific browser to launch for tests (default to **`chromium`**)                                                        |
| HEADLESS  | Boolean to run tests in [headless mode](https://en.wikipedia.org/wiki/Headless_software) or not (default to **`true`**) |
| SLOW_MO   | Integer to slow down Playwright operations by the specified amount of milliseconds (default to 5 milliseconds)          |

Before running tests, you should install your shop manually or run the install script *
*`campaigns/sanity/01_installShop/*`** with the [`test:specific` command](README.md#specific-test).

## Sanity tests

This campaign includes a non-exhaustive set of tests and will ensure that the most important functions work.

### Launch all scripts

If you want to run all sanity tests, you can run scripts in **`campaigns/sanity/*`**

#### With default values

```bash
npm run test:sanity
```

#### With custom values

You can add parameters that you need in the beginning of your command

```bash
HEADLESS=false URL_BO="Your_Shop_URL_BO" URL_FO="Your_Shop_URL_FO" npm run test:sanity
```

### Stop tests when first step in failed

If you want to run all sanity tests "safely", you can use the Travis-specific command : this will add the Mocha `--bail`
parameter which stops the campaign when the first test fails.

```bash
npm run test:sanity:fast-fail
```

## Functional tests

This campaign verifies that each function of the software application operate in conformance with the functional
requirements.
Each and every functionality of the system is tested by providing appropriate input, verifying the output, and comparing
the actual results with the expected results.

```bash
URL_FO="Your_Shop_URL_FO" npm run test:functional
```

## Specific test

If you want to run only one test from a campaign or a couple of tests in the same folder, you can use **`test:specific`
** command.

To specify which test to run, you can add the **`TEST_PATH`** parameter in the beginning of the command

```bash
# To run the **Filter Products** test from sanity campaign
TEST_PATH="sanity/03_productsBO/01_filterProducts" URL_FO="Your_Shop_URL_FO" npm run test:specific
# To run all **Products BO** tests
TEST_PATH="sanity/03_productsBO/*" URL_FO="Your_Shop_URL_FO" npm run test:specific
```

## LinkChecker

This script will detect not found and erroneous pages, by crawling your back office and front office. It's still a Work
In Progress.

### Launch script

If you want to run the links checker test you can run the script **`tools/linkchecker.ts`**.
It uses a `urls.ts` file describing all the URLs it can crawl.

You **must** disable the Security Token before running this script ! Add this line in your `.htaccess` file:

```bash
SetEnv _TOKEN_ disabled
```

#### With default values

```bash
npm run check:links
```

## Documentation

To help contributors find more documentation about UI tests, [JS-DOC](https://jsdoc.app/) was added on these
directories:

- `pages`
- `campaigns/data/faker`
- `campaigns/utils`

### Before generating documentation

[jsdoc-to-markdown](https://github.com/jsdoc2md/jsdoc-to-markdown) is the library used, it will create `.md` files using
js files from the above directories.

To install `jsdoc-to-markdown` :

```shell
cd tests/UI
npm ci
```

### Generate documentation

By running the command below, it will generate jsdoc on `.doc` directory.

```shell
bash scripts/generate-jsdoc.sh
```

Enjoy :wink: :v:
