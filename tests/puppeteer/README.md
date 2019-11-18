# PrestaShop Tests with Puppeteer

## How to install your environment

```bash
# Clone Prestashop
git clone https://github.com/PrestaShop/PrestaShop/
# Install dependencies in puppeteer folder
cd tests/puppeteer/
npm install
```

## Available command line parameters
| Parameter           | Description      |
|---------------------|----------------- |
| URL_FO              | URL of your PrestaShop website Front Office (default to **`http://localhost:8080/`**) |
| URL_BO              | URL of your PrestaShop website Back Office (default to **`URL_FO + admin-dev/`**) |
| URL_INSTALL         | URL of the Install folder (default to **`URL_FO + install-dev/`**) |
| FIRSTNAME           | Firstname of your admin employee (default to **`demo`**) |
| LASTNAME            | Lastname of your admin employee (default to **`demo`**) |
| LOGIN               | LOGIN of your PrestaShop website (default to **`demo@prestashop.com`**) |
| PASSWD              | PASSWD of your PrestaShop website (default to **`prestashop_demo`**) |
| SHOPNAME            | Shop Name of tour PrestaShop (default to **`Prestashop`**) |
| DB_USER             | Login user of your MySql (default to **`root`**) |
| DB_PASSWD           | Password for your MySql (default to **`empty`**) |
| HEADLESS            | Boolean to run tests in headless or not (default to **`true`**) |

Before running tests, you should install your shop manually or run the install script **`campaigns/sanity/01_installShop/*`** with the [`specific-test` command](README.md#specific-test).

## Sanity tests 
This campaign includes a non-exhaustive set of tests and will ensure that the most important functions work.

### Launch all scripts
If you want to run all sanity tests, you can run scripts in **`campaigns/sanity/*`**

#### With default values

```bash
npm run sanity-tests
```

#### With custom values
You can add parameters that you need in the beginning of your command 
```bash
HEADLESS=false URL_BO="Your_Shop_URL_BO" URL_FO="Your_Shop_URL_FO" npm run sanity-tests
```

### Stop tests when first step in failed
If you want to run all sanity tests "safely", you can use the Travis-specific command : this will add the Mocha `--bail` parameter which stops the campaign when the first test fails.

```bash
npm run sanity-travis
```

## Specific test 
If you want to run only one test from the campaign or a couple of tests in the same folder, you can use **`specific-test`** command.

To specify which test to run, you can add the **`TEST_PATH`** parameter in the beginning of the command

```bash
# To run the **Filter Products** test from sanity campaign
TEST_PATH="sanity/02_productsBO/01_filterProducts" URL_FO="Your_Shop_URL_FO" npm run specific-test
# To run all **Products BO** tests 
TEST_PATH="sanity/02_productsBO/*" URL_FO="Your_Shop_URL_FO" npm run specific-test
```


## LinkChecker
This script will detect not found and erroneous pages, by crawling your back office and front office. It's still a Work In Progress.


### Launch script
If you want to run the links checker test you can run the script **`tools/linkchecker.js`**.
It uses a `urls.js` file describing all the URLs it can crawl.

You **must** disable the Security Token before running this script ! Add this line in your `.htaccess` file:

```bash
SetEnv _TOKEN_ disabled
``` 

#### With default values

```bash
npm run linkchecker
```

## Upgrade test
This test will upgrade Prestashop version with the Autoupgrade module

### Launch script
Before testing it, you should install Prestashop version to upgrade from
If you want to run this test, you can use command **specific-test**

#### With default values

```bash
# You need to set PS_VERSION to check after upgrade, default to 1.7.6.0 
PS_VERSION=1.7.6.0 TEST_PATH="upgrade/upgradeShop" npm run specific-test
```

Enjoy :wink: :v:
