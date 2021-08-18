require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const addCurrencyPage = require('@pages/BO/international/currencies/add');

// Import Data
const {Currencies} = require('@data/demo/currencies');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_currencies_updateExchangeRate';

let browserContext;
let page;
let numberOfCurrencies = 0;
const newExchangeRate = 6;

describe('BO - International - Currencies : Update exchange rate', async () => {
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

  describe('Create new currency', async () => {
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

    it('should create currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await addCurrencyPage.addOfficialCurrency(page, Currencies.mad);
      await expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Update exchange rates', async () => {
    it(`should filter by iso code of currency '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.mad.isoCode);
    });

    it('should go to the created currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyPage', baseContext);

      await currenciesPage.goToEditCurrencyPage(page, 1);
      const pageTitle = await addCurrencyPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it('should update the exchange rate of the created currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateExchangeRate', baseContext);

      // Update and check successful message
      const textResult = await addCurrencyPage.updateExchangeRate(page, newExchangeRate);
      await expect(textResult).to.contains(currenciesPage.successfulUpdateMessage);
    });

    it('should click on Update exchange rate button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickUpdateExchangeRates', baseContext);

      // Click on update exchange rates button and check successful message
      const textResult = await currenciesPage.updateExchangeRate(page);
      await expect(textResult).to.contains(currenciesPage.successfulUpdateMessage);
    });

    it(`should filter by iso code of currency '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckValue', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.mad.isoCode);

      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.mad.isoCode);
    });

    it(`should check that the exchange rate of currency '${Currencies.mad.isoCode}' is updated`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExchangeRates', baseContext);

      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'conversion_rate');
      await expect(textColumn).to.not.equal(newExchangeRate);
    });
  });

  describe('Delete currency created', async () => {
    it(`should filter by iso code of currency '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.mad.isoCode);
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
