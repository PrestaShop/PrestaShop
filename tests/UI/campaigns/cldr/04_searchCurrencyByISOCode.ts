// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCurrencyTest} from '@commonTests/BO/international/currency';

// Import pages
import {
  boDashboardPage,
  boLocalizationPage,
  boLoginPage,
  boCurrenciesPage,
  boCurrenciesCreatePage,
  type BrowserContext,
  dataCurrencies,
  type FakerCurrency,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';

use(chaiString);

const baseContext: string = 'cldr_searchCurrencyByISOCode';

describe('CLDR : Search a currency by ISO code', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

  const installedCurrencies: FakerCurrency[] = [
    dataCurrencies.usd,
    dataCurrencies.pyg,
    dataCurrencies.jpy,
    dataCurrencies.gbp,
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

  installedCurrencies.forEach((currency: FakerCurrency, index: number) => {
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

  it('should filter by iso code "EUR"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeEUR', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', 'EUR');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.euro.isoCode);
  });

  it('should filter by iso code "US"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeUS', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', 'US');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it('should filter by iso code "PY"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodePY', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', 'PY');
    await boCurrenciesPage.sortTable(page, 'iso_code', 'asc');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(2);

    // Check currencies
    const textColumnRow1 = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumnRow1).to.contains(dataCurrencies.jpy.isoCode);

    const textColumnRow2 = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 2, 'iso_code');
    expect(textColumnRow2).to.contains(dataCurrencies.pyg.isoCode);
  });

  it('should filter by iso code "P"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeP', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', 'P');
    await boCurrenciesPage.sortTable(page, 'iso_code', 'asc');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(3);

    // Check currencies
    const textColumnRow1 = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumnRow1).to.contains(dataCurrencies.gbp.isoCode);

    const textColumnRow2 = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 2, 'iso_code');
    expect(textColumnRow2).to.contains(dataCurrencies.jpy.isoCode);

    const textColumnRow3 = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 3, 'iso_code');
    expect(textColumnRow3).to.contains(dataCurrencies.pyg.isoCode);
  });

  it('should filter by iso code "ABC"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeABC', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', 'ABC');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(0);

    // Check currencies
    const textColumn = await boCurrenciesPage.getTextForEmptyTable(page);
    expect(textColumn).to.equal('warning No records found');
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFinal', baseContext);

    const numberOfCurrenciesAfterReset = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrenciesAfterReset).to.be.eq(numberOfCurrencies + installedCurrencies.length);
  });

  // Post-condition - Delete currencies
  installedCurrencies.forEach((currency: FakerCurrency, index: number) => {
    deleteCurrencyTest(currency, `${baseContext}_postTest_${index}`);
  });
});
