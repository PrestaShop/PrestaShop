// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import localizationPage from '@pages/BO/international/localization';
import currenciesPage from '@pages/BO/international/currencies';
import addCurrencyPage from '@pages/BO/international/currencies/add';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';

import {
  boDashboardPage,
  dataCurrencies,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_localization_currencies_createUnofficialCurrency';

/*
Create unofficial currency
Check data created in table
Check Creation of currency in FO
Delete currency
 */
describe('BO - International - Currencies : Create unofficial currency and check it in FO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number = 0;

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

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
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

  describe('Create unofficial currency', async () => {
    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await addCurrencyPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it('should create unofficial currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createUnofficialCurrency', baseContext);

      // Check successful message after creation
      const textResult = await addCurrencyPage.createUnOfficialCurrency(page, dataCurrencies.toman);
      expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });

    it(
      `should filter by iso code of currency '${dataCurrencies.toman.isoCode}' and check values created in table`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyValues', baseContext);

        // Filter
        await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.toman.isoCode);

        // Check number of element
        const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
        expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

        const createdCurrency = await currenciesPage.getCurrencyFromTable(page, 1);
        await Promise.all([
          expect(createdCurrency.name).to.contains(dataCurrencies.toman.name),
          expect(createdCurrency.symbol).to.contains(dataCurrencies.toman.symbol),
          expect(createdCurrency.isoCode).to.contains(dataCurrencies.toman.isoCode),
          expect(createdCurrency.exchangeRate).to.be.above(0),
          expect(createdCurrency.enabled).to.be.equal(dataCurrencies.toman.enabled),
        ]);
      },
    );

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo1', baseContext);

      // View my shop and init pages
      page = await currenciesPage.viewMyShop(page);

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should change FO currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeFoCurrency1', baseContext);

      // Check currency
      await foHomePage.changeCurrency(page, dataCurrencies.toman.isoCode, dataCurrencies.toman.symbol);

      const shopCurrency = await foHomePage.getDefaultCurrency(page);
      expect(shopCurrency).to.contain(dataCurrencies.toman.isoCode);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await currenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

      const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Disable and check currency in FO', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.toman.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableCurrency', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.toman.isoCode);

      // Check number of currencies
      const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency created
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.toman.isoCode);
    });

    it('should disable currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCurrency', baseContext);

      const isActionPerformed = await currenciesPage.setStatus(page, 1, false);

      if (isActionPerformed) {
        const resultMessage = await currenciesPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(currenciesPage.successfulUpdateStatusMessage);
      }

      // Check currency disabled
      const currencyStatus = await currenciesPage.getStatus(page, 1);
      expect(currencyStatus).to.be.equal(false);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo2', baseContext);

      // View my shop and init pages
      page = await currenciesPage.viewMyShop(page);

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check that the currencies list is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrency2', baseContext);

      const found = await foHomePage.isCurrencyDropdownExist(page);
      expect(found, 'Currencies list is visible').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await currenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDisable', baseContext);

      const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Delete currency created ', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.toman.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.toman.isoCode);

      // Check number of currencies
      const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.toman.isoCode);
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
