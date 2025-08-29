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
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';

use(chaiString);

const baseContext: string = 'cldr_editSymbolFormatCurrency';

describe('CLDR : Edit symbol / format currency', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

  const customSymbol: string = '@';

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
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter0', baseContext);

    numberOfCurrencies = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  it(`should filter by iso code of currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToEurCurrency0', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.euro.isoCode);
  });

  it(`should edit the currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEuroCurrencyPage0', baseContext);

    await boCurrenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitleEdit(dataCurrencies.euro.name));
  });

  it('should have multiples currencies formats', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkMultipleFormats', baseContext);

    const numberCurrencyFormats = await boCurrenciesCreatePage.getNumberOfElementInGrid(page);
    expect(numberCurrencyFormats).to.be.gt(0);
  });

  it('should edit the first currency format and open a modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editCurrencyFormat', baseContext);

    const isModalVisible = await boCurrenciesCreatePage.editCurrencyFormat(page, 1);
    expect(isModalVisible).to.eq(true);
  });

  it(`should update the symbol by ${customSymbol} & the format`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCurrencyFormat', baseContext);

    await boCurrenciesCreatePage.setCurrencyFormatSymbol(page, customSymbol);
    await boCurrenciesCreatePage.setCurrencyFormatFormat(page, 'rightWithSpace');
    await boCurrenciesCreatePage.saveCurrencyFormat(page);

    const exampleFormat = await boCurrenciesCreatePage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormat).to.endWith(` ${customSymbol}`);
  });

  it('should reset the currency format', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencyFormat', baseContext);

    const growlMessage = await boCurrenciesCreatePage.resetCurrencyFormat(page, 1);
    expect(growlMessage).to.be.eq(boCurrenciesCreatePage.resetCurrencyFormatMessage);

    const exampleFormat = await boCurrenciesCreatePage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormat).to.startWith(dataCurrencies.euro.symbol);
  });

  [1, 2].forEach((numRow: number) => {
    it(`should edit the currency format #${numRow} and open a modal`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `editCurrencyFormat${numRow}`, baseContext);

      const isModalVisible = await boCurrenciesCreatePage.editCurrencyFormat(page, numRow);
      expect(isModalVisible).to.eq(true);
    });

    it(`should update the symbol by ${customSymbol} and the format`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateCurrencyFormat${numRow}`, baseContext);

      await boCurrenciesCreatePage.setCurrencyFormatSymbol(page, customSymbol);
      await boCurrenciesCreatePage.setCurrencyFormatFormat(page, 'rightWithSpace');
      await boCurrenciesCreatePage.saveCurrencyFormat(page);

      const exampleFormat = await boCurrenciesCreatePage.getTextColumnFromTable(page, numRow, 2);
      expect(exampleFormat).to.endWith(` ${customSymbol}`);
    });
  });

  it('should update the currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetCurrency0', baseContext);

    const result = await boCurrenciesCreatePage.saveCurrencyForm(page);
    expect(result).to.be.eq(boCurrenciesPage.successfulUpdateMessage);

    const symbolCurrency = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'symbol');
    expect(symbolCurrency).to.be.eq(customSymbol);
  });

  it(`should edit the currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEuroCurrencyPage1', baseContext);

    await boCurrenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitleEdit(dataCurrencies.euro.name));
  });

  it('should restore default settings\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'restoreDefaultSettings', baseContext);

    const modalRestore = await boCurrenciesCreatePage.restoreDefaultSettings(page);
    expect(modalRestore).to.eq(true);
  });

  it('should check the restoration is done', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkRestorationDone', baseContext);

    const exampleFormatRow1 = await boCurrenciesCreatePage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormatRow1).to.startWith(dataCurrencies.euro.symbol);

    const exampleFormatRow2 = await boCurrenciesCreatePage.getTextColumnFromTable(page, 2, 2);
    expect(exampleFormatRow2).to.endWith(` ${dataCurrencies.euro.symbol}`);
  });

  it('should update the currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetCurrency1', baseContext);

    const result = await boCurrenciesCreatePage.saveCurrencyForm(page);
    expect(result).to.be.eq(boCurrenciesPage.successfulUpdateMessage);

    const symbolCurrency = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'symbol');
    expect(symbolCurrency).to.be.eq(dataCurrencies.euro.symbol);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter1', baseContext);

    numberOfCurrencies = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });
});
