// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import localizationPage from '@pages/BO/international/localization';
import currenciesPage from '@pages/BO/international/currencies';
import addCurrencyPage from '@pages/BO/international/currencies/add';

import {
  dataCurrencies,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_localization_currencies_updateExchangeRate';

describe('BO - International - Currencies : Update exchange rate', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number = 0;

  const newExchangeRate: number = 6;

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
      expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await localizationPage.goToSubTabCurrencies(page);

      const pageTitle = await currenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrencies).to.be.above(0);
    });

    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await addCurrencyPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it('should create currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await addCurrencyPage.addOfficialCurrency(page, dataCurrencies.mad);
      expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Update exchange rates', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.mad.isoCode);
    });

    it('should go to the created currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyPage', baseContext);

      await currenciesPage.goToEditCurrencyPage(page, 1);

      const pageTitle = await addCurrencyPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCurrencyPage.editCurrencyPage);
    });

    it('should update the exchange rate of the created currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateExchangeRate', baseContext);

      // Update and check successful message
      const textResult = await addCurrencyPage.updateExchangeRate(page, newExchangeRate);
      expect(textResult).to.contains(currenciesPage.successfulUpdateMessage);
    });

    it('should click on Update exchange rate button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickUpdateExchangeRates', baseContext);

      // Click on update exchange rates button and check successful message
      const textResult = await currenciesPage.updateExchangeRate(page);
      expect(textResult).to.contains(currenciesPage.successfulUpdateMessage);
    });

    it(`should filter by iso code of currency '${dataCurrencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckValue', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.mad.isoCode);
    });

    it(`should check that the exchange rate of currency '${dataCurrencies.mad.isoCode}' is updated`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExchangeRates', baseContext);

      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'conversion_rate');
      expect(textColumn).to.not.equal(newExchangeRate);
    });
  });

  describe('Delete currency created', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.mad.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await currenciesPage.deleteCurrency(page, 1);
      expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies);
    });
  });
});
