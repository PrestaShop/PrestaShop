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
  FakerCurrency,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect, use} from 'chai';
import chaiString from 'chai-string';

use(chaiString);

const baseContext: string = 'functional_BO_international_localization_currencies_editOfficialAndUnofficialCurrency';

describe('BO - International - Currencies : Edit official and unofficial currency', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number = 0;
  const customSymbol: string = '£';
  const editCurrencyData: FakerCurrency = new FakerCurrency({
    name: 'Euros',
    exchangeRate: 3.000000,
    decimals: 3,
    enabled: true,
  });

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

  it(`should filter by iso code of currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterCurrency', baseContext);

    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.equal(1);
  });

  it('should go to edit currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrency', baseContext);

    await boCurrenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitleEdit(dataCurrencies.euro.name));
  });

  it('should edit the currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editCurrency', baseContext);

    const textResult = await boCurrenciesCreatePage.editCurrency(page, editCurrencyData);
    expect(textResult).to.contains(boCurrenciesPage.successfulUpdateMessage);
  });

  it('should go back to edit currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrency2', baseContext);

    await boCurrenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitleEdit(editCurrencyData.name));
  });

  it('should edit the first currency format and open a modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editCurrencyFormat', baseContext);

    const isModalVisible = await boCurrenciesCreatePage.editCurrencyFormat(page, 1);
    expect(isModalVisible).to.equal(true);
  });

  it(`should update the symbol by ${customSymbol} & the format`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCurrencyFormat', baseContext);

    await boCurrenciesCreatePage.setCurrencyFormatSymbol(page, customSymbol);
    await boCurrenciesCreatePage.setCurrencyFormatFormat(page, 'leftWithSpace');
    await boCurrenciesCreatePage.saveCurrencyFormat(page);

    const exampleFormat = await boCurrenciesCreatePage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormat).to.startWith(customSymbol);
  });

  it('should save and check the new symbol', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkNewSymbol', baseContext);

    const result = await boCurrenciesCreatePage.saveCurrencyForm(page);
    expect(result).to.equal(boCurrenciesPage.successfulUpdateMessage);

    const symbolCurrency = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'symbol');
    expect(symbolCurrency).to.equal(customSymbol);
  });

  it('should go to FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

    // View my shop and int pages
    page = await boCurrenciesPage.viewMyShop(page);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage).to.eq(true);
  });

  it('should check the price of the first product in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSymbol', baseContext);

    const productPrice = await foClassicHomePage.getProductPrice(page, 1);
    expect(productPrice).to.contains(customSymbol);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
  });

  it('should go back to edit currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditCurrency3', baseContext);

    await boCurrenciesPage.goToEditCurrencyPage(page, 1);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitleEdit(editCurrencyData.name));
  });

  it('should restore default settings\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'restoreDefaultSettings', baseContext);

    const modalRestore = await boCurrenciesCreatePage.restoreDefaultSettings(page);
    expect(modalRestore).to.equal(true);
  });

  it('should check the restoration is done', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkRestorationDone', baseContext);

    const exampleFormatRow1 = await boCurrenciesCreatePage.getTextColumnFromTable(page, 1, 2);
    expect(exampleFormatRow1).to.startWith(dataCurrencies.euro.symbol);
  });

  it('should save the edit currency form', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'saveForm', baseContext);

    const result = await boCurrenciesCreatePage.saveCurrencyForm(page);
    expect(result).to.equal(boCurrenciesPage.successfulUpdateMessage);
  });
});
