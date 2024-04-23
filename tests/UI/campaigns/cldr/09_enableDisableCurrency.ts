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

import {
  dataCurrencies,
  FakerCurrency,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';

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

  it(`should filter by iso code of currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToEurCurrency0', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.euro.isoCode);
  });

  it('should disable the default currency and check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCurrency', baseContext);

    const isActionPerformed = await currenciesPage.setStatus(page, 1, false);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await currenciesPage.getAlertDangerBlockParagraphContent(page);
    expect(resultMessage).to.contains(currenciesPage.cannotDisableDefaultCurrencyMessage);

    const currencyStatus = await currenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(true);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter1', baseContext);

    numberOfCurrencies = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrencies).to.be.above(0);
  });

  it('should go to create new currency page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

    await currenciesPage.goToAddNewCurrencyPage(page);

    const pageTitle = await addCurrencyPage.getPageTitle(page);
    expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
  });

  it('should create official currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

    // Create and check successful message
    const textResult = await addCurrencyPage.addOfficialCurrency(page, currencyDollar);
    expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

    // Check number of currencies after creation
    const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
  });

  it(`should filter by iso code of currency '${currencyDollar.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToDollarCurrency0', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', currencyDollar.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(currencyDollar.isoCode);
  });

  it(`should enable the currency '${currencyDollar.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDollarCurrency', baseContext);

    const isActionPerformed = await currenciesPage.setStatus(page, 1, true);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await currenciesPage.getAlertSuccessBlockParagraphContent(page);
    expect(resultMessage).to.contains(currenciesPage.successfulUpdateStatusMessage);

    const currencyStatus = await currenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(true);
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab0', baseContext);

    await localizationPage.goToSubTabLocalizations(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it(`should choose '${currencyDollar.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDollarAsDefaultCurrency', baseContext);

    const textResult = await localizationPage.setDefaultCurrency(page, `${currencyDollar.name} (${currencyDollar.isoCode})`);
    expect(textResult).to.contain(localizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab1', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToEurCurrency1', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.euro.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency created
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.euro.isoCode);
  });

  it(`should disable the currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableEuroCurrency', baseContext);

    const isActionPerformed = await currenciesPage.setStatus(page, 1, false);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await currenciesPage.getAlertSuccessBlockParagraphContent(page);
    expect(resultMessage).to.contains(currenciesPage.successfulUpdateStatusMessage);

    const currencyStatus = await currenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(false);
  });

  it(`should enable the currency '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableEuroCurrency', baseContext);

    const isActionPerformed = await currenciesPage.setStatus(page, 1, true);
    expect(isActionPerformed).to.eq(true);

    const resultMessage = await currenciesPage.getAlertSuccessBlockParagraphContent(page);
    expect(resultMessage).to.contains(currenciesPage.successfulUpdateStatusMessage);

    const currencyStatus = await currenciesPage.getStatus(page, 1);
    expect(currencyStatus).to.eq(true);
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab1', baseContext);

    await localizationPage.goToSubTabLocalizations(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it(`should choose '${dataCurrencies.euro.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setEuroAsDefaultCurrency', baseContext);

    const textResult = await localizationPage.setDefaultCurrency(
      page,
      `${dataCurrencies.euro.name} (${dataCurrencies.euro.isoCode})`,
    );
    expect(textResult).to.contain(localizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab2', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${currencyDollar.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToDollarCurrency1', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', currencyDollar.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(currencyDollar.isoCode);
  });

  it('should delete currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

    const result = await currenciesPage.deleteCurrency(page, 1);
    expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
  });

  it('should reset filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

    const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies);
  });
});
