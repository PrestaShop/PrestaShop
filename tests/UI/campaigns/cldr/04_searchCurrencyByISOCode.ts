// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCurrencyTest} from '@commonTests/BO/international/currency';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import localizationPage from '@pages/BO/international/localization';
import currenciesPage from '@pages/BO/international/currencies';
import addCurrencyPage from '@pages/BO/international/currencies/add';

// Import data
import Currencies from '@data/demo/currencies';
import CurrencyData from '@data/faker/currency';

import {use, expect} from 'chai';
import chaiString from 'chai-string';
import type {BrowserContext, Page} from 'playwright';

use(chaiString);

const baseContext: string = 'cldr_searchCurrencyByISOCode';

describe('CLDR : Search a currency by ISO code', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

  const installedCurrencies: CurrencyData[] = [
    Currencies.usd,
    Currencies.pyg,
    Currencies.jpy,
    Currencies.gbp,
  ];

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

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab0', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCurrencies).to.be.above(0);
  });

  installedCurrencies.forEach((currency: CurrencyData, index: number) => {
    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCurrencyPage${currency.isoCode}`, baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await addCurrencyPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it(`should create the currency ${currency.isoCode}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createOfficialCurrency${currency.isoCode}`, baseContext);

      // Create and check successful message
      const textResult = await addCurrencyPage.addOfficialCurrency(page, currency);
      await expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + index + 1);
    });
  });

  it('should filter by iso code "EUR"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeEUR', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', 'EUR');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    await expect(textColumn).to.contains(Currencies.euro.isoCode);
  });

  it('should filter by iso code "US"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeUS', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', 'US');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    await expect(textColumn).to.contains(Currencies.usd.isoCode);
  });

  it('should filter by iso code "PY"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodePY', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', 'PY');
    await currenciesPage.sortTable(page, 'iso_code', 'asc');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCurrenciesAfterFilter).to.be.equal(2);

    // Check currencies
    const textColumnRow1 = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    await expect(textColumnRow1).to.contains(Currencies.jpy.isoCode);

    const textColumnRow2 = await currenciesPage.getTextColumnFromTableCurrency(page, 2, 'iso_code');
    await expect(textColumnRow2).to.contains(Currencies.pyg.isoCode);
  });

  it('should filter by iso code "P"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeP', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', 'P');
    await currenciesPage.sortTable(page, 'iso_code', 'asc');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCurrenciesAfterFilter).to.be.equal(3);

    // Check currencies
    const textColumnRow1 = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    await expect(textColumnRow1).to.contains(Currencies.gbp.isoCode);

    const textColumnRow2 = await currenciesPage.getTextColumnFromTableCurrency(page, 2, 'iso_code');
    await expect(textColumnRow2).to.contains(Currencies.jpy.isoCode);

    const textColumnRow3 = await currenciesPage.getTextColumnFromTableCurrency(page, 3, 'iso_code');
    await expect(textColumnRow3).to.contains(Currencies.pyg.isoCode);
  });

  it('should filter by iso code "ABC"', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterWithISOCodeABC', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', 'ABC');

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    await expect(numberOfCurrenciesAfterFilter).to.be.equal(0);

    // Check currencies
    const textColumn = await currenciesPage.getTextForEmptyTable(page);
    await expect(textColumn).to.equal('warning No records found');
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFinal', baseContext);

    const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCurrenciesAfterReset).to.be.eq(numberOfCurrencies + installedCurrencies.length);
  });

  // Post-condition - Delete currencies
  installedCurrencies.forEach((currency: CurrencyData, index: number) => {
    deleteCurrencyTest(currency, `${baseContext}_postTest_${index}`);
  });
});
