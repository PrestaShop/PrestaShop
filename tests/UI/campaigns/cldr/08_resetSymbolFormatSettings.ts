// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
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

const baseContext: string = 'cldr_resetSymbolFormatSettings';

describe('CLDR : Reset symbol / format settings', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

  const customSymbol: string = '@';

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
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter0', baseContext);

    numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  it(`should filter by iso code of currency '${Currencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToEurCurrency0', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.euro.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(Currencies.euro.isoCode);
  });

  it(`should edit the currency '${Currencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEuroCurrencyPage0', baseContext);

    await currenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await addCurrencyPage.getPageTitle(page);
    expect(pageTitle).to.contains(addCurrencyPage.pageTitleEdit(Currencies.euro.name));
  });

  it('should have multiples currencies formats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMultipleFormats', baseContext);

    const numberCurrencyFormats = await addCurrencyPage.getNumberOfElementInGrid(page);
    expect(numberCurrencyFormats).to.be.gt(0);
  });

  it('should edit the first currency format and open a modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editCurrencyFormat', baseContext);

    const isModalVisible = await addCurrencyPage.editCurrencyFormat(page, 1);
    expect(isModalVisible).to.eq(true);
  });

  it(`should update the symbol by ${customSymbol}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCurrencyFormatSymbol', baseContext);

    await addCurrencyPage.setCurrencyFormatSymbol(page, customSymbol);
    await addCurrencyPage.saveCurrencyFormat(page);

    const exampleFormat = await addCurrencyPage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormat).to.startWith(customSymbol);
  });

  it('should update the currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'saveCurrency', baseContext);

    const result = await addCurrencyPage.saveCurrencyForm(page);
    expect(result).to.be.eq(currenciesPage.successfulUpdateMessage);

    const symbolCurrency = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'symbol');
    expect(symbolCurrency).to.be.eq(customSymbol);
  });

  it(`should edit the currency '${Currencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEuroCurrencyPage1', baseContext);

    await currenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await addCurrencyPage.getPageTitle(page);
    expect(pageTitle).to.contains(addCurrencyPage.pageTitleEdit(Currencies.euro.name));
  });

  it('should reset the currency format', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencyFormat', baseContext);

    const growlMessage = await addCurrencyPage.resetCurrencyFormat(page, 1);
    expect(growlMessage).to.be.eq(addCurrencyPage.resetCurrencyFormatMessage);

    const exampleFormat = await addCurrencyPage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormat).to.startWith(Currencies.euro.symbol);
  });

  it('should update the currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetCurrency', baseContext);

    const result = await addCurrencyPage.saveCurrencyForm(page);
    expect(result).to.be.eq(currenciesPage.successfulUpdateMessage);

    const symbolCurrency = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'symbol');
    expect(symbolCurrency).to.be.eq(Currencies.euro.symbol);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter1', baseContext);

    numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });
});
