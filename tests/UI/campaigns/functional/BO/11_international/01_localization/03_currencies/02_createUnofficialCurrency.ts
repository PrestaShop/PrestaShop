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
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

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

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCurrencies = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  describe('Create unofficial currency', async () => {
    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await boCurrenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
    });

    it('should create unofficial currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createUnofficialCurrency', baseContext);

      // Check successful message after creation
      const textResult = await boCurrenciesCreatePage.createUnOfficialCurrency(page, dataCurrencies.toman);
      expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });

    it(
      `should filter by iso code of currency '${dataCurrencies.toman.isoCode}' and check values created in table`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyValues', baseContext);

        // Filter
        await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.toman.isoCode);

        // Check number of element
        const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
        expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

        const createdCurrency = await boCurrenciesPage.getCurrencyFromTable(page, 1);
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
      page = await boCurrenciesPage.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should change FO currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeFoCurrency1', baseContext);

      // Check currency
      await foClassicHomePage.changeCurrency(page, dataCurrencies.toman.isoCode, dataCurrencies.toman.symbol);

      const shopCurrency = await foClassicHomePage.getDefaultCurrency(page);
      expect(shopCurrency).to.contain(dataCurrencies.toman.isoCode);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCurrenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

      const numberOfCurrenciesAfterReset = await boCurrenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Disable and check currency in FO', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.toman.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableCurrency', baseContext);

      // Filter
      await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.toman.isoCode);

      // Check number of currencies
      const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency created
      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.toman.isoCode);
    });

    it('should disable currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCurrency', baseContext);

      const isActionPerformed = await boCurrenciesPage.setStatus(page, 1, false);

      if (isActionPerformed) {
        const resultMessage = await boCurrenciesPage.getAlertSuccessBlockParagraphContent(page);
        expect(resultMessage).to.contains(boCurrenciesPage.successfulUpdateStatusMessage);
      }

      // Check currency disabled
      const currencyStatus = await boCurrenciesPage.getStatus(page, 1);
      expect(currencyStatus).to.be.equal(false);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo2', baseContext);

      // View my shop and init pages
      page = await boCurrenciesPage.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it('should check that the currencies list is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrency2', baseContext);

      const found = await foClassicHomePage.isCurrencyDropdownExist(page);
      expect(found, 'Currencies list is visible').to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boCurrenciesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDisable', baseContext);

      const numberOfCurrenciesAfterReset = await boCurrenciesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Delete currency created ', async () => {
    it(`should filter by iso code of currency '${dataCurrencies.toman.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.toman.isoCode);

      // Check number of currencies
      const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      expect(textColumn).to.contains(dataCurrencies.toman.isoCode);
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
