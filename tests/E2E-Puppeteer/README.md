# PrestaShop Functional Tests with puppeteer

## prestashop_linkchecker (our first test)
This script will prevent the 400 and 500 http error code, by crawling your back office and front office

### How to install your environment

```bash
git clone https://github.com/PrestaShop/PrestaShop/
cd tests/E2E-puppeteer/
npm i
```

### Available command line parameters

| Parameter           | Description      |
|---------------------|----------------- |
| URL_BO              | URL of your PrestaShop website Back Office (default to **http://localhost:8080/admin-dev/**) |
| URL_FO              | URL of your PrestaShop website Front Office (default to **http://localhost:8080/**) |
| LOGIN               | LOGIN of your PrestaShop website (default to **demo@prestashop.com**) |
| PASSWD              | PASSWD of your PrestaShop website (default to **prestashop_demo**) |

### Launch script
If you want to run the Install test you can run the script **test/linkchecker.js**

#### With default values

```
node test/linkchecker.js
```

#### With custom values

```bash
URL_BO="Your_Shop_URL_BO" URL_FO="Your_Shop_URL_FO" LOGIN="Your_Login" PASSWD="Your_Password" node test/linkchecker.js
```

#### Run with docker

```bash
#Build image
docker build -t puppeteer_linkchecker -f .docker/Dockerfile .
#Run test
docker run -e URL_BO="Your_Shop_URL_BO" -e URL_FO="Your_Shop_URL_FO" -e LOGIN="Your_Login" -e PASSWD="Your_Password" --network="host" puppeteer_linkchecker
```

#### Run with docker-compose

```bash
#Create Shop and running test
docker-compose up --build
```

Enjoy :wink: :v:
