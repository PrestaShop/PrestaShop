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


```
➜  npm run high-test -- --URL=FrontOfficeURL --DIR=DownloadDirectory
```
* **URL**: Front office URL of your prestashop website (without the “http://”)
* **DIR**: Your download directory (exp: /home/toto/Downloads/) so we can check the downloaded invoice.

If you want to run test only on specific parts (for example products), you have to run this command:
```
➜  path=high/02_product npm run specific-test -- --URL=FrontOfficeURL
```

* **path**: path of directory you want to test.
