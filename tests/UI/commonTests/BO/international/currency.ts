// Import utils
import testContext from '@utils/testContext';

// Import BO pages
import {
  boCurrenciesPage,
  boCurrenciesCreatePage,
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  type FakerCurrency,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to create currency
 * @param currencyData {FakerCurrency} Data to set to create currency
 * @param baseContext {string} String to identify the test
 */
function createCurrencyTest(currencyData: FakerCurrency, baseContext: string = 'commonTests-createCurrencyTest'): void {
  describe('PRE-TEST: Create currency', async () => {
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

    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await boCurrenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
    });

    it('should create currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await boCurrenciesCreatePage.addOfficialCurrency(page, currencyData);
      expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);
    });
  });
}

/**
 * Function to delete currency
 * @param currencyData {FakerCurrency} Data to set to delete currency
 * @param baseContext {string} String to identify the test
 */
function deleteCurrencyTest(currencyData: FakerCurrency, baseContext: string = 'commonTests-deleteCurrencyTest'): void {
  describe('POST-TEST: Delete currency', async () => {
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

    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage2', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage2', baseContext);

      await boLocalizationPage.goToSubTabCurrencies(page);

      const pageTitle = await boCurrenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
    });

    it(`should filter by iso code of currency '${currencyData.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boCurrenciesPage.filterTable(page, 'input', 'iso_code', currencyData.isoCode);

      // Check currency to delete
      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(currencyData.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await boCurrenciesPage.deleteCurrency(page, 1);
      expect(result).to.be.equal(boCurrenciesPage.successfulDeleteMessage);
    });
  });
}

export {createCurrencyTest, deleteCurrencyTest};
