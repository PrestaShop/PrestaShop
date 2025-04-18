// Import utils
import testContext from '@utils/testContext';

// Import pages
import {
  boDashboardPage,
  boLocalizationPage,
  boCurrenciesPage,
  boCurrenciesCreatePage,
  boLoginPage,
  type BrowserContext,
  dataCurrencies,
  type FakerCurrency,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';

use(chaiString);

const baseContext: string = 'cldr_deleteCurrency';

describe('CLDR : Delete a currency', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

  const currencies: FakerCurrency[] = [
    dataCurrencies.gbp,
    dataCurrencies.jpy,
    dataCurrencies.usd,
  ];

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

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab0', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfCurrencies = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  currencies.forEach((currency: FakerCurrency, index : number) => {
    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCurrencyPage${currency.isoCode}`, baseContext);

      await boCurrenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
    });

    it(`should create the currency ${currency.isoCode}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createOfficialCurrency${currency.isoCode}`, baseContext);

      // Create and check successful message
      const textResult = await boCurrenciesCreatePage.addOfficialCurrency(page, currency);
      expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await boCurrenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + index + 1);
    });
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab0', baseContext);

    await boLocalizationPage.goToSubTabLocalizations(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it(`should choose '${dataCurrencies.usd.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setUSDAsDefaultCurrency', baseContext);

    const textResult = await boLocalizationPage.setDefaultCurrency(
      page,
      `${dataCurrencies.usd.name} (${dataCurrencies.usd.isoCode})`,
    );
    expect(textResult).to.contains(boLocalizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab1', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToUSDCurrency0', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.usd.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it('should delete currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrencyUSDWithError', baseContext);

    const result = await boCurrenciesPage.deleteCurrency(page, 1);
    expect(result).to.be.equal(boCurrenciesPage.cannotDeleteDefaultCurrencyMessage);
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab1', baseContext);

    await boLocalizationPage.goToSubTabLocalizations(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it(`should choose '${dataCurrencies.euro.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setEURAsDefaultCurrency', baseContext);

    const textResult = await boLocalizationPage.setDefaultCurrency(
      page,
      `${dataCurrencies.euro.name} (${dataCurrencies.euro.isoCode})`,
    );
    expect(textResult).to.contains(boLocalizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab2', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToUSDCurrency1', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.usd.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it('should delete currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrencyUSD', baseContext);

    const result = await boCurrenciesPage.deleteCurrency(page, 1);
    expect(result).to.be.equal(boCurrenciesPage.successfulDeleteMessage);
  });

  it(`should select rows except '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAndSelectRowsExceptEUR', baseContext);

    const numberOfCurrenciesAfterDelete = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrenciesAfterDelete).to.be.eq(numberOfCurrencies + currencies.length - 1);

    const isBulkActionsEnabledBeforeSelect = await boCurrenciesPage.isBulkActionsEnabled(page);
    expect(isBulkActionsEnabledBeforeSelect).to.eq(false);

    for (let numRow = 1; numRow <= numberOfCurrenciesAfterDelete; numRow++) {
      const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, numRow, 'iso_code');

      if (textColumn !== dataCurrencies.euro.isoCode) {
        await boCurrenciesPage.selectRow(page, numRow);
      }
    }

    const isBulkActionsEnabledAfterSelect = await boCurrenciesPage.isBulkActionsEnabled(page);
    expect(isBulkActionsEnabledAfterSelect).to.eq(true);
  });

  it('should bulk delete currencies', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCurrencies', baseContext);

    const result = await boCurrenciesPage.bulkDeleteCurrencies(page);
    expect(result).to.be.eq(boCurrenciesPage.successfulMultiDeleteMessage);

    const numberOfCurrenciesAfterBulkDeete = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterBulkDeete).to.be.equal(numberOfCurrencies);
  });
});
