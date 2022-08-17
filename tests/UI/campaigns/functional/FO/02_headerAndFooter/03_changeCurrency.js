require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');

// Import common tests
const {createCurrencyTest, deleteCurrencyTest} = require('@commonTests/BO/international/createDeleteCurrency');
const loginCommon = require('@commonTests/BO/loginBO');

// Import Data
const {Currencies} = require('@data/demo/currencies');
const {Products} = require('@data/demo/products');

// Import pages
// BO
const currenciesPage = require('@pages/BO/international/currencies');
const localizationPage = require('@pages/BO/international/localization');
const dashboardPage = require('@pages/BO/dashboard');

// FO
const homePage = require('@pages/FO/home');
const searchResultsPage = require('@pages/FO/searchResults');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_FO_headerAndFooter_changeCurrency';

let browserContext;
let page;
let filePath;
let exchangeRateValue = 0;

/*
Pre-condition: Check the block currencies is not displayed in header of FO
Pre-condition: Create a new currency
Scenario:
 - Go to FO and check that the currencies list is not visible in the header
 - Create new currency
 - Go to FO and switch to the new currency
 - Check that the price of the product is changed
 - Switch back to the original currency and check the price of the first product
Post-condition:
- Delete the created currency
*/

describe('FO - Header and Footer : Change currency', async () => {
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await files.deleteFile(filePath);
    await helper.closeBrowserContext(browserContext);
  });

  // Pre-condition: Check the block currencies is not displayed in header of FO
  describe('PRE-TEST: Check that the block currencies is not displayed in header of FO', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check that the currencies block is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrenciesLink', baseContext);

      const isVisible = await homePage.isCurrencyVisible(page);
      await expect(isVisible).to.be.false;
    });
  });

  // Pre-condition: Create currency
  createCurrencyTest(Currencies.mad, `${baseContext}_preTest_2`);

  describe('Filter by iso code of currency and get the exchange rate value ', async () => {
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

    it(`should get the currency exchange rate value '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getExchangeRate', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.mad.isoCode);

      // Check exchange rate
      exchangeRateValue = await currenciesPage.getExchangeRateValue(page, 1);
      await expect(exchangeRateValue).to.be.above(0);
    });
  });

  describe('Switch to another currency and check the product price', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should change FO currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeFoCurrency', baseContext);

      // Check currency
      await homePage.changeCurrency(page, Currencies.mad.isoCode, Currencies.mad.symbol);
      const shopCurrency = await homePage.getDefaultCurrency(page);
      await expect(shopCurrency).to.contains(Currencies.mad.isoCode);
    });

    it('should search product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, Products.demo_11.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      await expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice', baseContext);

      const newExchangeRateValue = (exchangeRateValue * Products.demo_11.finalPrice).toFixed(Currencies.mad.decimals);
      const productPrice = await searchResultsPage.getProductPrice(page);
      await expect(productPrice).to.contains(`${Currencies.mad.symbol}${newExchangeRateValue}`);
    });

    it('should switch back to the default currency', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'switchBackDefaultFoCurrency',
        baseContext,
      );

      // Check currency
      await homePage.changeCurrency(page, Currencies.euro.isoCode, Currencies.euro.symbol);
      const shopCurrency = await homePage.getDefaultCurrency(page);
      await expect(shopCurrency).to.contains(Currencies.euro.isoCode);
    });

    it('should check the product price', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductPrice2', baseContext);

      const exchangeRate = Math.round(Currencies.euro.exchangeRate * Products.demo_11.finalPrice);
      const productPrice = await searchResultsPage.getProductPrice(page);
      await expect(productPrice).to.contains(`${Currencies.euro.symbol}${exchangeRate}`);
    });
  });

  // Post-condition - Delete currency
  deleteCurrencyTest(Currencies.mad, `${baseContext}_postTest_1`);
});
