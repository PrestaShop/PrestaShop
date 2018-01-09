# PrestaShop Functional Tests
## Summary
These tests are running using the awesome **[mocha](https://mochajs.org/)** test runner, using the **[chai](http://chaijs.com/)** assertions framework with the expect syntax.
They are using also **[webdriver.io](http://webdriver.io/)** that allows you to perform almost any action a browser would do using a fluent promise-based API.
Until we can do more documentation, please have a look at the existing tests and at the **[WebDriver.io](http://webdriver.io/api.html)** API.

## Requirements 
To run these tests you have to install
* node.js
* npm
* java
* Google Chrome

## How to run the tests
To use the following test suites, you need to install PrestaShop in **English** with setting country to **France**. (or you may change some assertions like the separator “,” or “.”, “€” or “$” or “£” or …) You need to create a user in Back Office with **SuperAdmin** rights and the following information’s:

* **Login**: demo@prestashop.com
* **Password**: prestashop_demo

### Package install

To install npm dependencies, selenium-server, chromedriver and geckodriver you have to run this command on the root directory of the functional tests
```
➜  cd tests/E2E
➜  npm install
```

### Launch selenium-standalone

Then you have to lunch selenium-standalone 
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
* URL: Front office URL of your prestashop website (without the “http://”)
* DIR: Your download directory (exp: /home/toto/Downloads/) 

