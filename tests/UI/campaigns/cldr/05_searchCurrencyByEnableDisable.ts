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

import {use, expect} from 'chai';
import chaiString from 'chai-string';
import type {BrowserContext, Page} from 'playwright';

use(chaiString);

const baseContext: string = 'cldr_searchCurrencyByEnableDisable';

describe('CLDR : Search a currency by enable/disable', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

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

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab0', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  it('should go to create new currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPageUSD', baseContext);

    await currenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await addCurrencyPage.getPageTitle(page);
    expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
  });

  it('should create the currency USD', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrencyUSD', baseContext);

    // Create and check successful message
    const textResult = await addCurrencyPage.addOfficialCurrency(page, Currencies.usd);
    expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

    // Check number of currencies after creation
    const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
  });

  it(`should filter by iso code of currency '${Currencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToUSDCurrency', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.usd.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(Currencies.usd.isoCode);
  });

  it(`should disable currency '${Currencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', `disableCurrency${Currencies.usd.isoCode}`, baseContext);

    await currenciesPage.setStatus(page, 1, false);

    const status = await currenciesPage.getStatus(page, 1);
    expect(status).to.eq(false);
  });

  it('should search Enabled currencies', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterEnabledCurrencies', baseContext);

    await currenciesPage.resetFilter(page);
    await currenciesPage.filterTable(page, 'select', 'active', '1');

    const numberEnabledCurrencies = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberEnabledCurrencies).to.be.eq(1);

    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(Currencies.euro.isoCode);
  });

  it('should search Disabled currencies', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterDisabledCurrencies', baseContext);

    await currenciesPage.resetFilter(page);
    await currenciesPage.filterTable(page, 'select', 'active', '0');

    const numberEnabledCurrencies = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberEnabledCurrencies).to.be.eq(1);

    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(Currencies.usd.isoCode);
  });

  it(`should enable currency '${Currencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', `enableCurrency${Currencies.usd.isoCode}`, baseContext);

    await currenciesPage.setStatus(page, 1, true);

    const textColumn = await currenciesPage.getTextForEmptyTable(page);
    expect(textColumn).to.equal('warning No records found');
  });

  it('should go to create new currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPageGBP', baseContext);

    await currenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await addCurrencyPage.getPageTitle(page);
    expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
  });

  it('should create the currency GBP', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrencyGBP', baseContext);

    // Create and check successful message
    const textResult = await addCurrencyPage.addOfficialCurrency(page, Currencies.gbp);
    expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

    // Check number of currencies after creation
    const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterCreation).to.be.eq(0);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFinal', baseContext);

    const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrenciesAfterReset).to.be.eq(numberOfCurrencies + 2);
  });

  // Post-condition - Delete currency USD
  deleteCurrencyTest(Currencies.usd, `${baseContext}_postTest_1`);

  // Post-condition - Delete currency GBP
  deleteCurrencyTest(Currencies.gbp, `${baseContext}_postTest_2`);
});
