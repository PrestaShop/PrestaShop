# PrestaShop Functional Tests

## Summary

PrestaShop functional end2end tests are based on the following stack:

* [mocha](https://mochajs.org/)
* [chai](http://chaijs.com/)
* [webdriver.io](http://webdriver.io/)
* [selenium](http://www.seleniumhq.org/)
* [PageObject pattern](https://martinfowler.com/bliki/PageObject.html)

## Requirements

### Software needed

To run these tests you have to install

* [node.js](https://nodejs.org/en/download/), the minimum required version is 8
* [npm](https://www.npmjs.com/get-npm), the minimum required version is 5
* [java](https://java.com/fr/download/), the minimum required version is 8
* [Google Chrome](https://www.google.com/chrome/browser/desktop/index.html), the minimum required version is 58
* [mysql](https://www.mysql.com)

* poppler-utils for Ubuntu/Debian users
> Note:
> To install poppler-utils execute:
> apt-get install poppler-utils

* xpdf for OSX users
> Note:
> xpdf can be installed via homebrew:
> brew install xpdf


### Dependencies

To install npm dependencies (selenium-server, chromedriver and geckodriver) you have to run this command:

```bash
npm install
```

### PrestaShop

* Prestashop with the following requirements:
- Installation must be in **English** with setting country to **France** (or you may change some assertions like the separator “,” or “.”, “€” or “$” or “£” or …)
- A user in Back Office with **SuperAdmin** rights

This command line does it for you but you need a mysql server available with the right to create a database:

```bash
php install/index_cli.php --language=en \
                          --country=fr \
                          --domain=localhost \
                          --db_server=localhostr \
                          --db_user=prestashop_user \
                          --db_name=prestashop \
                          --DB_PASSWD=prestaop_password \
                          --firstname=Foo \
                          --lastname=Bar \
                          --email=demo@prestashop.com \
                          --password=prestashop_demo \
                          --db_create=1 \
```

> Note:
> Or you can run the installation script via the npm script specific-test


## How to run the tests

On Windows, you will need one instance for Selenium and one for the tests.

### Launch selenium-standalone

Then you have to launch selenium-standalone

```bash
npm run start-selenium
```

Wait until you see something like:

```bash
14:11:56.786 INFO - Found handler: org.openqa.selenium.remote.server.commandhandler.Status@712804ef
14:11:56.789 INFO - /status: Executing GET on /status (handler: Status)
Selenium started
```

Check if it is working

```bash
npm run sanity-check
```

### Tests suite

#### Available command line parameters


| Parameter            | Description  |
| -------------------- | ------------ |
| URL                  | URL of your PrestaShop website (default to **http://localhost**) |
| DIR                  | Your download directory (exp: /home/toto/Downloads/) so we can check the downloaded invoice. |
| URLLASTSTABLEVERSION | URL of the last stable version of PrestaShop (without the “http://”) from which you need to upgrade to the latest release candidate |
| LANGUAGE             | Language to install with (default to "en") |
| COUNTRY              | Country to install with (default o "france") |
| DB_SERVER            | DataBase server (default to "mysql") |
| DB_USER              | DataBase user (default to "root") |
| DB_PASSWD            | DataBase password (default to "doge") |
| DB_EMPTY_PASSWD      | Boolean option: set it to **true** if you have no password |
| RCTARGET             | Last stable version location directory (example: /project/prestashop1724/) |
| RCLINK               | RC Download link, if you have already downloaded the RC you have to copy the ZIP file in the --RCTARGET admin-dev/autoupgrade/download/ and set the FILENAME option |
| FILENAME             | RC file name this parameter must be mentioned if the (RCLINK) option is not indicated |
| MODULE               | Module technical name to install (default to "gadwords") |
| INSTALL              | Boolean option: set it to **true** if you want to run the installation script (default to **false**) |
| TEST_ADDONS          | Boolean option: set it to **true** if you want disable check with Addons API (default to **false**) |
| HEADLESS             | Boolean option:Set it to true to run tests in headless mode (default to false)
| ADMIN_EMAIL          | Set admin email (default: "demo@prestashop.com")
| ADMIN_PASSWORD       | Set admin password (default: "prestashop_demo")

#### Regular tests

If you want to
  * Launch installation before running tests => you have to add your database parameters
  * Specify an URL for your shop => you have to set the **URL** parameter
  * Specify a module to install => you have to set the **MODULE** parameter

```
npm test -- --URL=http://prestashop.localhost \
            --DB_SERVER=localhost \
            --DB_USER=prestashop \
            --DB_PASSWD=prestashop \
            --INSTALL \
            --LANGUAGE=en \
            --COUNTRY=france \
            --MODULE=gadwords
```

#### Specific test

If you want to run test only on specific parts (for example products), you have to run this command:

```bash
TEST_PATH=high/02_product npm run specific-test -- --URL=http://prestashop.localhost
```

Use `TEST_PATH` environement variable to specify which test you want to run.

>Notes:
>1) if you are running high/01_order you must set the **DIR** option
>2) if you are running high/10_module or regular/02_install_module.js you must set the **MODULE** option
>3) If you have run only the install_upgrade/01_install.js with language different and country different from "en" and "france" you need to reinstall PrestaShop in **English** with setting country to **France** So you can launch the other tests

#### High tests

If you want to run the high level tests you can run the campaign **High**

```
npm run high-test -- --URL=prestashop.localhost --DIR=DownloadDirectory --MODULE=DataTechNameModule
```

>Notes:
>
> It's not recommended to run all the campaign high tests together, it's safer to run them one by one using the script specific-test.

#### Full tests

If you want to run the high level and full configuration tests you can run the campaign **Full**

```
npm run full-test -- --URL=prestashop.localhost --DIR=DownloadDirectory --MODULE=DataTechNameModule
```

>Notes:
>
> It's not recommended to run all the campaign full tests together, it's safer to run them one by one using the script specific-test.


#### Install and Autoupgrade

If you want to run the Install, Autoupgrade and Rollback tests you can run the campaign **install_upgrade**

``` bash
npm run install-upgrade-test -- --URL=prestashop.localhost \
                                --DIR=DownloadDirectory \
                                --URLLASTSTABLEVERSION=LaststableversionURL \
                                --RCLINK=RCDownloadlink \
                                --RCTARGET=LastStableVersionLocation \
                                --FILENAME=RCFileName
```



## Running the tests with Docker

A dockerfile is provided in this folder, which allows you to run all the
tests even if you haven't followed the installation part. This however requires
Docker to be installed on your environment.

Build:

```
docker build -t prestashop/e2e .
```

The LAMP stack and the test dependancies will be installed in an image tagged
as `prestashop/e2e`.

Run:
```
docker run -ti --privileged prestashop/e2e
```

In case you want to retrieved the report and screenshot generated by the tests,
mounting volumes will help you:

```
docker run -ti --privileged -v $PWD\screenshots:/var/www/html/tests/E2E/test/screenshots -v $PWD\reports:/var/www/html/tests/E2E/mochawesome-report prestashop/e2e
```
