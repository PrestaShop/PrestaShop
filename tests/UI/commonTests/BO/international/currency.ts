// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import currenciesPage from '@pages/BO/international/currencies';
import addCurrencyPage from '@pages/BO/international/currencies/add';
import localizationPage from '@pages/BO/international/localization';

// Import data
import type CurrencyData from '@data/faker/currency';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to create currency
 * @param currencyData {CurrencyData} Data to set to create currency
 * @param baseContext {string} String to identify the test
 */
function createCurrencyTest(currencyData: CurrencyData, baseContext: string = 'commonTests-createCurrencyTest'): void {
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
      expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await localizationPage.goToSubTabCurrencies(page);

      const pageTitle = await currenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(currenciesPage.pageTitle);
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
      const textResult = await addCurrencyPage.addOfficialCurrency(page, currencyData);
      expect(textResult).to.contains(currenciesPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to delete currency
 * @param currencyData {CurrencyData} Data to set to delete currency
 * @param baseContext {string} String to identify the test
 */
function deleteCurrencyTest(currencyData: CurrencyData, baseContext: string = 'commonTests-deleteCurrencyTest'): void {
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
      expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Currencies\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage2', baseContext);

      await localizationPage.goToSubTabCurrencies(page);

      const pageTitle = await currenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it(`should filter by iso code of currency '${currencyData.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await currenciesPage.filterTable(page, 'input', 'iso_code', currencyData.isoCode);

      // Check currency to delete
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(currencyData.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await currenciesPage.deleteCurrency(page, 1);
      expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
    });
  });
}

export {createCurrencyTest, deleteCurrencyTest};
