require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/BO/loginBO');
const searchResultsPage = require('@pages/FO/searchResults');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const addCurrencyPage = require('@pages/BO/international/currencies/add');

// Import Data
const {Currencies} = require('@data/demo/currencies');
const {Products} = require('@data/demo/products');


// Import FO pages
const homePage = require('@pages/FO/home');
const currencyPage = require('@pages/FO/currency');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_changeCurrency';

let browserContext;
let page;
let numberOfCurrencies = 0;

/*
Case 1: You have one currency only
Go to FO
Check that the currency link is not visible:
Case 2: You have tow or more currencies
Go to BO
Go to International > Localization > currencies page
Add new currency
Go to FO
Change the current currency
Check currency is changed
Check the product price value is changed
Check the exchange rate value
Switch back to the original currency
Check currency is changed
Go to Bo
Delete currency
*/

describe('FO - Case 1 One currency exists : Check link to change currency is not appear in header of FO', async () => {
  describe('FO -  Check link to change currency is not appear in header of FO', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check \'currencies\' link not appear', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrenciesLink', baseContext);

      const currency = await currencyPage.isCurrencyVisible(page);
      await expect(currency).to.be.false;
    });
  });
});

describe('FO - Case 2 : Tow or more currencies exists : Check link to change currency  is appear in header of FO', async () => {
  describe('BO - Add new currency', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.localizationLink,
      );

      await localizationPage.closeSfToolBar(page);

      const pageTitle = await localizationPage.getPageTitle(page);
      await expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await localizationPage.goToSubTabCurrencies(page);
      const pageTitle = await currenciesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });


    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCurrencies).to.be.above(0);
    });

    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);
      const pageTitle = await addCurrencyPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it('should add a new currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectCurrency', baseContext);

      // Check successful message after creation
      const textResult = await addCurrencyPage.createUnOfficialCurrency(page, Currencies.toman);
      await expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });

    it(
      `should filter by iso code of currency '${Currencies.toman.isoCode}' and check values created in table`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyValues', baseContext);

        // Filter
        await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.toman.isoCode);

        // Check number of element
        const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

        const createdCurrency = await currenciesPage.getCurrencyFromTable(page, 1);
        await Promise.all([
          expect(createdCurrency.name).to.contains(Currencies.toman.name),
          expect(createdCurrency.symbol).to.contains(Currencies.toman.symbol),
          expect(createdCurrency.isoCode).to.contains(Currencies.toman.isoCode),
          expect(createdCurrency.exchangeRate).to.be.above(0),
          expect(createdCurrency.enabled).to.be.equal(Currencies.toman.enabled),
        ]);
      },
    );


    describe('FO : Switch to another currency and check the products price', async () => {
      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFo2', baseContext);

        // View my shop and init pages
        page = await currenciesPage.viewMyShop(page);

        const isHomePage = await homePage.isHomePage(page);
        await expect(isHomePage).to.be.true;
      });

      it('should change FO currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeFoCurrency', baseContext);

        // Check currency
        await homePage.changeCurrency(page, Currencies.toman.isoCode, Currencies.toman.symbol);
        const shopCurrency = await homePage.getDefaultCurrency(page);
        await expect(shopCurrency).to.contains(Currencies.toman.isoCode);
      });
      it('should search product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

        await homePage.searchProduct(page, Products.demo_11.name);

        const pageTitle = await searchResultsPage.getPageTitle(page);
        await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
      });
      it('should check products currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductCurrency', baseContext);

        // Check product currency

        const productCurrency = await currencyPage.getProductCurrency(page);
        await expect(productCurrency).to.contains(Currencies.toman.symbol);
      });
      it('should check the exchange rate value ', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkExchangeRateValue', baseContext);

        // Check the exchange rate value

        const productWithExchangeRate = await currencyPage.getProductPrice(page);
        await expect(Math.round(productWithExchangeRate)).to.equal(Math.round(Currencies.toman.exchangeRate * Products.demo_11.finalPrice));
      });


      describe('FO : Switch back to the original currency', async () => {
        it('should switch back the the original currency', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'switchBackOriginalFoCurrency', baseContext);

          // Check currency
          await homePage.changeCurrency(page, Currencies.euro.isoCode, Currencies.euro.symbol);
          const shopCurrency = await homePage.getDefaultCurrency(page);
          await expect(shopCurrency).to.contains(Currencies.euro.isoCode);
        });

        it('should search product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

          // Search for the product Mug The best is yet to come
          await homePage.searchProduct(page, Products.demo_11.name);

          const pageTitle = await searchResultsPage.getPageTitle(page);
          await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
        });


        it('should check products currency is EURO ', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkProductCurrency', baseContext);

          // Check product currency

          const productCurrency = await currencyPage.getProductCurrency(page);
          await expect(productCurrency).to.contains(Currencies.euro.symbol);
        });

        it('should check the exchange rate value ', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkExchangeRateValue', baseContext);

          // Check the exchange rate value

          const productWithExchangeRate = await currencyPage.getProductPrice(page);
          await expect(Math.round(productWithExchangeRate)).to.equal(Math.round(Currencies.euro.exchangeRate * Products.demo_11.finalPrice));
        });

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

          page = await homePage.closePage(browserContext, page, 0);
          const pageTitle = await currenciesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(currenciesPage.pageTitle);
        });

        it('should reset filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

          const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
        });
      });

      describe('Delete currency created ', async () => {
        it(`should filter by iso code of currency '${Currencies.toman.isoCode}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

          // Filter
          await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.toman.isoCode);

          // Check number of currencies
          const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
          await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

          const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
          await expect(textColumn).to.contains(Currencies.toman.isoCode);
        });

        it('should delete currency', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

          const result = await currenciesPage.deleteCurrency(page, 1);
          await expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
        });

        it('should reset filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

          const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies);
        });
      });
    });
  });
});
