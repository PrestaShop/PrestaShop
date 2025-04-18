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
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'cldr_enableDisableCurrency';

describe('CLDR : Enable/Disable a currency', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCurrencies: number;

  const currencyDollar: FakerCurrency = new FakerCurrency({
    name: dataCurrencies.usd.name,
    isoCode: dataCurrencies.usd.isoCode,
    enabled: false,
  });

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

  it('should disable the default currency and check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCurrency', baseContext);

    const isActionPerformed = await boCurrenciesPage.setStatus(page, 1, false);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await boCurrenciesPage.getAlertDangerBlockParagraphContent(page);
    expect(resultMessage).to.contains(boCurrenciesPage.cannotDisableDefaultCurrencyMessage);

    const currencyStatus = await boCurrenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(true);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter1', baseContext);

    numberOfCurrencies = await boCurrenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  it('should go to create new currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

    await boCurrenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await boCurrenciesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesCreatePage.pageTitle);
  });

  it('should create official currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

    // Create and check successful message
    const textResult = await boCurrenciesCreatePage.addOfficialCurrency(page, currencyDollar);
    expect(textResult).to.contains(boCurrenciesPage.successfulCreationMessage);

    // Check number of currencies after creation
    const numberOfCurrenciesAfterCreation = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
  });

  it(`should filter by iso code of currency '${currencyDollar.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToDollarCurrency0', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', currencyDollar.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(currencyDollar.isoCode);
  });

  it(`should enable the currency '${currencyDollar.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDollarCurrency', baseContext);

    const isActionPerformed = await boCurrenciesPage.setStatus(page, 1, true);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await boCurrenciesPage.getAlertSuccessBlockParagraphContent(page);
    expect(resultMessage).to.contains(boCurrenciesPage.successfulUpdateStatusMessage);

    const currencyStatus = await boCurrenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(true);
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab0', baseContext);

    await boLocalizationPage.goToSubTabLocalizations(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it(`should choose '${currencyDollar.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDollarAsDefaultCurrency', baseContext);

    const textResult = await boLocalizationPage.setDefaultCurrency(page, `${currencyDollar.name} (${currencyDollar.isoCode})`);
    expect(textResult).to.contain(boLocalizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab1', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToEurCurrency1', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.euro.isoCode);
  });

  it(`should disable the currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableEuroCurrency', baseContext);

    const isActionPerformed = await boCurrenciesPage.setStatus(page, 1, false);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await boCurrenciesPage.getAlertSuccessBlockParagraphContent(page);
    expect(resultMessage).to.contains(boCurrenciesPage.successfulUpdateStatusMessage);

    const currencyStatus = await boCurrenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(false);
  });

  it(`should enable the currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableEuroCurrency', baseContext);

    const isActionPerformed = await boCurrenciesPage.setStatus(page, 1, true);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await boCurrenciesPage.getAlertSuccessBlockParagraphContent(page);
    expect(resultMessage).to.contains(boCurrenciesPage.successfulUpdateStatusMessage);

    const currencyStatus = await boCurrenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(true);
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab1', baseContext);

    await boLocalizationPage.goToSubTabLocalizations(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it(`should choose '${dataCurrencies.euro.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setEuroAsDefaultCurrency', baseContext);

    const textResult = await boLocalizationPage.setDefaultCurrency(
      page,
      `${dataCurrencies.euro.name} (${dataCurrencies.euro.isoCode})`,
    );
    expect(textResult).to.contain(boLocalizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab2', baseContext);

    await boLocalizationPage.goToSubTabCurrencies(page);

    const pageTitle = await boCurrenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCurrenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${currencyDollar.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToDollarCurrency1', baseContext);

    // Filter
    await boCurrenciesPage.filterTable(page, 'input', 'iso_code', currencyDollar.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await boCurrenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await boCurrenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(currencyDollar.isoCode);
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
