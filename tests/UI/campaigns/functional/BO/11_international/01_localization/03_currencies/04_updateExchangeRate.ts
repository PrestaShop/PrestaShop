// Import utils
import testContext from '@utils/testContext';

// Import pages
import {
  boCurrenciesPage,
  boCurrenciesCreatePage,
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  dataCurrencies,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_BO_international_localization_currencies_updateExchangeRate';

describe('BO - International - Currencies : Update exchange rate', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number = 0;

  const newExchangeRate: number = 6;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  describe('Create new currency', async () => {
    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.localizationLink,
      );
      await boLocalizationPage.closeSfToolBar(page);

      const pageTitle = await boLocalizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await boLocalizationPage.goToSubTabCurrencies(page);

      const pageTitle = await boCurrenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfCurrencies = await boCurrenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrencies).to.be.above(0);
    });

    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await boCurrenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
    });

    it('should create currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await boCurrenciesCreatePage.addOfficialCurrency(page, dataCurrencies.mad);
      expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Update exchange rates', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.mad.isoCode);
    });

    it('should go to the created currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrencyPage', baseContext);

      await boCurrenciesPage.goToEditCurrencyPage(page, 1);

      const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesCreatePage.editCurrencyPage);
    });

    it('should update the exchange rate of the created currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateExchangeRate', baseContext);

      // Update and check successful message
      const textResult = await boCurrenciesCreatePage.updateExchangeRate(page, newExchangeRate);
      expect(textResult).to.contains(boCurrenciesPage.successfulUpdateMessage);
    });

    it('should click on Update exchange rate button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickUpdateExchangeRates', baseContext);

      // Click on update exchange rates button and check successful message
      const textResult = await boCurrenciesPage.updateExchangeRate(page);
      expect(textResult).to.contains(boCurrenciesPage.successfulUpdateMessage);
    });

    it(`should filter by iso code of currency '${dataCurrencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckValue', baseContext);

      // Filter
      await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.mad.isoCode);
    });

    it(`should check that the exchange rate of currency '${dataCurrencies.mad.isoCode}' is updated`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExchangeRates', baseContext);

      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'conversion_rate');
      expect(textColumn).to.not.equal(newExchangeRate);
    });
  });

  describe('Delete currency created', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.mad.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await boCurrenciesPage.deleteCurrency(page, 1);
      expect(result).to.be.equal(boCurrenciesPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCurrenciesAfterReset = await boCurrenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies);
    });
  });
});
