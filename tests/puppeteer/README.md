# PrestaShop Tests with Puppeteer

## How to install your environment

```bash
git clone https://github.com/PrestaShop/PrestaShop/
cd tests/puppeteer/
npm i
```

## LinkChecker
This script will detect not found and erroneous pages, by crawling your back office and front office

### Available command line parameters

| Parameter           | Description      |
|---------------------|----------------- |
| URL_BO              | URL of your PrestaShop website Back Office (default to **http://localhost:8080/admin-dev/**) |
| URL_FO              | URL of your PrestaShop website Front Office (default to **http://localhost:8080/**) |
| LOGIN               | LOGIN of your PrestaShop website (default to **demo@prestashop.com**) |
| PASSWD              | PASSWD of your PrestaShop website (default to **prestashop_demo**) |

### Launch script
If you want to run the links checker test you can run the script **test/linkchecker.js**

#### With default values

```bash
npm run linkchecker
```

#### With custom values

```bash
URL_BO="Your_Shop_URL_BO" URL_FO="Your_Shop_URL_FO" LOGIN="Your_Login" PASSWD="Your_Password" npm run linkchecker
```


## Smoke tests 
This campaign includes a non-exhaustive set of tests and it will ensure that the most important functions work.

### Launch all scripts
If you want to run all smoke tests, you can run the script **campaigns/smoke/***

#### With default values

```bash
npm run smoke-tests
```

Enjoy :wink: :v:
