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
* [node.js](https://nodejs.org/en/download/), the minimum required version is 4.0.0
* [npm](https://www.npmjs.com/get-npm), the minimum required version is 2.14.2
* [java](https://java.com/fr/download/), the minimum required version is 8
* [Google Chrome](https://www.google.com/chrome/browser/desktop/index.html), the minimum required version is 58
* [mysql](https://www.mysql.com)
* poppler-utils for Ubuntu users
> Note:
> To install poppler-utils execute:
> apt-get install poppler-utils
* xpdf for OSX users
> Note:
> xpdf can be installed via homebrew: 
> brew install xpdf


### PrestaShop
* Prestashop with the following requirements:
- Installation in **English** with setting country to **France** (or you may change some assertions like the separator “,” or “.”, “€” or “$” or “£” or …) 
- A user in Back Office with **SuperAdmin** rights and the following information:
* **Login**: demo@prestashop.com
* **Password**: prestashop_demo

This command line does it for you (you need a mysql user presta:presta with the right to create a database on localhost)
```
php install/index_cli.php --language=en --country=fr --domain=localhost --db_server=localhostr --db_user=presta --db_name=presta --DB_PASSWD=presta --firstname=Foo --lastname=Bar --email=demo@prestashop.com --password=prestashop_demo --db_create=1
```
> Note:
> Or you can run the installation script via the npm script specific-test


### npm dependencies

To install npm dependencies, selenium-server, chromedriver and geckodriver you have to run this command on the root directory of the functional tests
```
cd tests/E2E
npm install
```
## How to run the tests
You will need two shell windows, one to run selenium, one to run the tests.

### Launch selenium-standalone

Then you have to launch selenium-standalone 
```
npm run start-selenium
```

Wait until you see:

```
...
Selenium started
```

### Check it is working

```
npm run sanity-check
```

### Launch test suite

#### Regular tests
If you want to run only the most important partial configuration tests and you have PrestaShop installed on **localhost** you can simply run the campaign **Regular**
```
npm test
```
If you want to 
  * Launch installation before running tests => you have to add your database parameters 
  * Specify an URL for your shop => you have to set the **URL** parameter
  * Specify a module to install => you have to set the **MODULE** parameter

```
npm test -- --URL=FrontOfficeURL --INSTALL=true --DB_SERVER=DataBaseUser --DB_PASSWD=DataBasePassword --DB_USER=DataBaseUser --LANGUAGE=language --COUNTRY=country --MODULE=DataTechNameModule
```
* **URL**: **(Optional)** Front office URL of your PrestaShop website without the “http://” (default to **localhost**)
* **MODULE**: **(Optional)** Module technical name to install (default to "gadwords")
* **INSTALL**: **(Optional)** Boolean option : set it to **true** if you want to run the installation script (default to **false**)
* **TEST_ADDONS**: **(Optional)** Boolean option : set it to **true** if you want disable check with Addons API (default to **false**)
* **LANGUAGE**: **(Optional)** Language to install with (default to "en")
* **COUNTRY**: **(Optional)** Country to install with (default o "france")
* **DB_SERVER**: **(Optional)** DataBase server (default to "mysql")
* **DB_USER**: **(Optional)** DataBase user (default to "root")
* **DB_PASSWD**: **(Optional)** DataBase password (default to "doge")
* **DB_EMPTY_PASSWD**:**(Optional)** Boolean option : set it to **true** if you have no password

#### Specific test
If you want to run test only on specific parts (for example products), you have to run this command:

```
path=high/02_product npm run specific-test -- --URL=FrontOfficeURL
```

* **path**: **(Required)** path of directory you want to test
* **URL**: **(Optional)** Front office URL of your PrestaShop website without the “http://” (default to **localhost**)

>Notes:
>1) if you are running high/01_order you must set the **DIR** option
>2) if you are running high/10_module or regular/02_install_module.js you must set the **MODULE** option
>3) If you have run only the install_upgrade/01_install.js with language different and country different from "en" and "france" you need to reinstall PrestaShop in **English** with setting country to **France** So you can launch the other tests

#### High tests
If you want to run the high level tests you can run the campaign **High**

```
npm run high-test -- --URL=FrontOfficeURL --DIR=DownloadDirectory --MODULE=DataTechNameModule
```
* **URL**: **(Optional)** Front office URL of your PrestaShop website without the “http://” (default to **localhost**)
* **DIR**: **(Required)** Your download directory (exp: /home/toto/Downloads/) so we can check the downloaded invoice.
* **MODULE**: **(Optional)** Module technical name to install (default to "gadwords")

>Notes:
>
> It's not recommended to run all the campaign high tests together, it's safer to run them one by one using the script specific-test.

#### Full tests
If you want to run the high level and full configuration tests you can run the campaign **Full**

```
npm run full-test -- --URL=FrontOfficeURL --DIR=DownloadDirectory --MODULE=DataTechNameModule
```
* **URL**: **(Optional)** Front office URL of your PrestaShop website without the “http://” (default to **localhost**)
* **DIR**: **(Required)** Your download directory (exp: /home/toto/Downloads/) so we can check the downloaded invoice.
* **MODULE**: **(Optional)** Module technical name to install (default to "gadwords")

>Notes:
>
> It's not recommended to run all the campaign full tests together, it's safer to run them one by one using the script specific-test.


#### Install and Autoupgrade
If you want to run the Install, Autoupgrade and Rollback tests you can run the campaign **install_upgrade**
```
npm run install-upgrade-test -- --URL=FrontOfficeURL --DIR=DownloadDirectory --URLLASTSTABLEVERSION=LaststableversionURL --DB_SERVER=DataBaseServer --DB_USER=DataBaseUser --DB_PASSWD=DataBasePassword --RCLINK=RCDownloadlink --RCTARGET=LastStableVersionLocation --FILENAME=RCFileName --LANGUAGE=language --COUNTRY=country
```

* **URL**: **(Optional)** Front office URL of your PrestaShop website without the “http://” (default to **localhost**)
* **DIR**: **(Required)** Your download directory (exp: /home/toto/Downloads/) so we can check the downloaded invoice.
* **URLLASTSTABLEVERSION**: **(Required)** URL of the last stable version of PrestaShop (without the “http://”) from which you need to upgrade to the latest release candidate
* **LANGUAGE**: **(Optional)** Language to install with (default to "en")
* **COUNTRY**: **(Optional)** Country to install with (default o "france")
* **DB_SERVER**: **(Optional)** DataBase server (default to "mysql")
* **DB_USER**: **(Optional)** DataBase user (default to "root")
* **DB_PASSWD**: **(Optional)** DataBase password (default to "doge")
* **DB_EMPTY_PASSWD**:**(Optional)** Boolean option : set it to **true** if you have no password
* **RCTARGET**: **(Required)** Last stable version location directory (example: /project/prestashop1724/)
* **RCLINK**: **(Optional)** RC Download link, if you have already downloaded the RC you have to copy the ZIP file in the --RCTARGET admin-dev/autoupgrade/download/ and set the FILENAME option
* **FILENAME**: **(Optional)** RC file name this parameter must be mentioned if the (RCLINK) option is not indicated


>Note:
> If you want to run tests in headless mode you can set the option --HEADLESS to true (This option will not work perfectly if your chrome version is under 62.0.3175.0, especially for category, attribute and feature tests)
>
> **HEADLESS**: **(Optional)** Set it to true to run tests in headless mode (default to false) 
