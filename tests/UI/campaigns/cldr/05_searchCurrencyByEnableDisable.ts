// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteCurrencyTest} from '@commonTests/BO/international/currency';

// Import pages
import {
  boDashboardPage,
  boLocalizationPage,
  boCurrenciesPage,
  boCurrenciesCreatePage,
  boLoginPage,
  type BrowserContext,
  dataCurrencies,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';

use(chaiString);

const baseContext: string = 'cldr_searchCurrencyByEnableDisable';

describe('CLDR : Search a currency by enable/disable', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

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

  it('should go to create new currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPageUSD', baseContext);

    await boCurrenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
  });

  it('should create the currency USD', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrencyUSD', baseContext);

    // Create and check successful message
    const textResult = await boCurrenciesCreatePage.addOfficialCurrency(page, dataCurrencies.usd);
    expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);

    // Check number of currencies after creation
    const numberOfCurrenciesAfterCreation = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
  });

  it(`should filter by iso code of currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToUSDCurrency', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.usd.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it(`should disable currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', `disableCurrency${dataCurrencies.usd.isoCode}`, baseContext);

    await boCurrenciesPage.setStatus(page, 1, false);

    const status = await boCurrenciesPage.getStatus(page, 1);
    expect(status).to.eq(false);
  });

  it('should search Enabled currencies', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterEnabledCurrencies', baseContext);

    await boCurrenciesPage.resetFilter(page);
    await boCurrenciesPage.filterTable(page, 'select', 'active', '1');

    const numberEnabledCurrencies = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberEnabledCurrencies).to.be.eq(1);

    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.euro.isoCode);
  });

  it('should search Disabled currencies', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterDisabledCurrencies', baseContext);

    await boCurrenciesPage.resetFilter(page);
    await boCurrenciesPage.filterTable(page, 'select', 'active', '0');

    const numberEnabledCurrencies = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberEnabledCurrencies).to.be.eq(1);

    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it(`should enable currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', `enableCurrency${dataCurrencies.usd.isoCode}`, baseContext);

    await boCurrenciesPage.setStatus(page, 1, true);

    const textColumn = await boCurrenciesPage.getTextForEmptyTable(page);
    expect(textColumn).to.equal('warning No records found');
  });

  it('should go to create new currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPageGBP', baseContext);

    await boCurrenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
  });

  it('should create the currency GBP', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrencyGBP', baseContext);

    // Create and check successful message
    const textResult = await boCurrenciesCreatePage.addOfficialCurrency(page, dataCurrencies.gbp);
    expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);

    // Check number of currencies after creation
    const numberOfCurrenciesAfterCreation = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterCreation).to.be.eq(0);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFinal', baseContext);

    const numberOfCurrenciesAfterReset = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrenciesAfterReset).to.be.eq(numberOfCurrencies + 2);
  });

  // Post-condition - Delete currency USD
  deleteCurrencyTest(dataCurrencies.usd, `${baseContext}_postTest_1`);

  // Post-condition - Delete currency GBP
  deleteCurrencyTest(dataCurrencies.gbp, `${baseContext}_postTest_2`);
});
