# PrestaShop Functional Tests
## Summary
These tests are running using the awesome **[mocha](https://mochajs.org/)** test runner, using the **[chai](http://chaijs.com/)** assertions framework with the expect syntax.
They are using also **[webdriver.io](http://webdriver.io/)** that allows you to perform almost any action a browser would do using a fluent promise-based API.
Until we can do more documentation, please have a look at the existing tests and at the **[WebDriver.io](http://webdriver.io/api.html)** API.

## Requirements 
To run these tests you have to install
* [node.js](https://nodejs.org/en/download/)
* [npm](https://www.npmjs.com/get-npm)
* [java](https://java.com/fr/download/)
* [Google Chrome](https://www.google.com/chrome/browser/desktop/index.html?brand=CHBD&gclid=EAIaIQobChMIva2UgZTN2AIVjjgbCh2kcA9MEAAYASAAEgKC8fD_BwE)
* poppler-utils for Ubuntu users
> Note:
> To install poppler-utils execute:
> apt-get install poppler-utils
* xpdf for OSX users
> Note:
> xpdf can be installed via homebrew: 
> brew install xpdf


## How to run the tests
To use the following test suites, you need to install PrestaShop in **English** with setting country to **France** (or you may change some assertions like the separator “,” or “.”, “€” or “$” or “£” or …) You need to create a user in Back Office with **SuperAdmin** rights and the following information:

* **Login**: demo@prestashop.com
* **Password**: prestashop_demo

### Package install

To install npm dependencies, selenium-server, chromedriver and geckodriver you have to run this command on the root directory of the functional tests
```
➜  cd tests/E2E
➜  npm install
```

### Launch selenium-standalone

Then you have to launch selenium-standalone 
```
➜  npm run start-selenium
```

Expected

```
...
Selenium started
```

### Launch test suite
#### Specific test
If you want to run test only on specific parts (for example products), you have to run this command:

```
➜ path=high/02_product npm run specific-test -- --URL=FrontOfficeURL
```

* **path**: path of directory you want to test
* **URL**: **(Required)** Front office URL of your PrestaShop website (without the “http://”)

>Note:
>If you have run only the 13_installation/1_installation_language_equal_to_country.js you need to reinstall PrestaShop in **English** with setting country to **France** So you can launch the other tests

#### High tests
If you want to run only the high level and full configuration tests you can run the campaign **High**

```
➜ npm run high-test -- --URL=FrontOfficeURL --DIR=DownloadDirectory --URLLASTSTABLEVERSION=LaststableversionURL --DB_SERVER=DataBaseServer --DB_USER=DataBaseUser --DB_PASSWD=DataBasePassword --RCLINK=RCDownloadlink --RCTARGET=LastStableVersionLocation --FILENAME=RCFileName
```
* **URL**: **(Required)** Front office URL of your PrestaShop website (without the “http://”)
* **DIR**: **(Required)** Your download directory (exp: /home/toto/Downloads/) so we can check the downloaded invoice.
* **MODULE**: **(Optional)** Module technical name to install (default to "gadwords")
* **INSTALL**: **(Optional)** Boolean option : set it to **true** if you want to run the installation script (default to **false**)
* **URLLASTSTABLEVERSION**: **(Required)** URL of the last stable version of PrestaShop (without the “http://”) from which you need to upgrade to the latest release candidate
* **DB_SERVER**: **(Optional)** DataBase server (default to "mysql")
* **DB_USER**: **(Optional)** DataBase user (default to "root")
* **DB_PASSWD**: **(Optional)** DataBase password (default to "doge")
* **DB_EMPTY_PASSWD**:**(Optional)** Boolean option : set it to **true** if you have no password
* **RCTARGET**: **(Required)** Last stable version location directory (example: /project/prestashop1724/)
* **RCLINK**: **(Optional)** RC Download link, if you have already downloaded the RC you have to extract the ZIP file in the --RCTARGET admin-dev/autoupgrade/download/ and set the FILENAME option
* **FILENAME**: **(Optional)** RC file name this parameter must be mentioned if the (RCLINK) option is not indicated

#### Regular tests
If you want to run only the most important partial configuration tests you can run the campaign **Regular**
```
➜ npm run regular-test -- --URL=FrontOfficeURL --MODULE=DataTechNameModule --INSTALL=true --DB_SERVER=DataBaseUser --DB_PASSWORD=DataBasePassword --DB_USER=DataBaseUser
```

>Notes:
>1) if you are running high/01_order you must set the **DIR** option
>2) if you are running high/10_module or regular/02_install_module.js you must set the **MODULE** option
>3) If you are running all the test or only high/13_installation you must set **all the options** (RCLink or FILENAME)
