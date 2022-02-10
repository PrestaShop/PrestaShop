require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const addCurrencyPage = require('@pages/BO/international/currencies/add');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to create currency
 * @param currencyData {CurrencyData} Data to set to create currency
 * @param baseContext {string} String to identify the test
 */
function createCurrencyTest(currencyData, baseContext = 'commonTests-createCurrencyTest') {
  describe('PRE-TEST: Create currency', async () => {
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

    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);
      const pageTitle = await addCurrencyPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it('should create currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await addCurrencyPage.addOfficialCurrency(page, currencyData);
      await expect(textResult).to.contains(currenciesPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to delete currency
 * @param currencyData {CurrencyData} Data to set to delete currency
 * @param baseContext {string} String to identify the test
 */
function deleteCurrencyTest(currencyData, baseContext = 'commonTests-deleteCurrencyTest') {
  describe('POST-TEST: Delete currency', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage2', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage2', baseContext);

      await localizationPage.goToSubTabCurrencies(page);
      const pageTitle = await currenciesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it(`should filter by iso code of currency '${currencyData.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', currencyData.isoCode);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      await expect(textColumn).to.contains(currencyData.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await currenciesPage.deleteCurrency(page, 1);
      await expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
    });
  });
}

module.exports = {createCurrencyTest, deleteCurrencyTest};
