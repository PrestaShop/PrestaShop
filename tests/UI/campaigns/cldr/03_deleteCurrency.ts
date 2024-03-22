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
  type FakerCurrency,
} from '@prestashop-core/ui-testing';

import {use, expect} from 'chai';
import chaiString from 'chai-string';
import type {BrowserContext, Page} from 'playwright';

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

  currencies.forEach((currency: FakerCurrency, index : number) => {
    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCurrencyPage${currency.isoCode}`, baseContext);

      await currenciesPage.goToAddNewCurrencyPage(page);

      const pageTitle = await addCurrencyPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCurrencyPage.pageTitle);
    });

    it(`should create the currency ${currency.isoCode}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createOfficialCurrency${currency.isoCode}`, baseContext);

      // Create and check successful message
      const textResult = await addCurrencyPage.addOfficialCurrency(page, currency);
      expect(textResult).to.contains(currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await currenciesPage.getNumberOfElementInGrid(page);
      expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + index + 1);
    });
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab0', baseContext);

    await localizationPage.goToSubTabLocalizations(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it(`should choose '${dataCurrencies.usd.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setUSDAsDefaultCurrency', baseContext);

    const textResult = await localizationPage.setDefaultCurrency(
      page,
      `${dataCurrencies.usd.name} (${dataCurrencies.usd.isoCode})`,
    );
    expect(textResult).to.contains(localizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab1', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToUSDCurrency0', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.usd.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it('should delete currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrencyUSDWithError', baseContext);

    const result = await currenciesPage.deleteCurrency(page, 1);
    expect(result).to.be.equal(currenciesPage.cannotDeleteDefaultCurrencyMessage);
  });

  it('should go to Localization Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationTab1', baseContext);

    await localizationPage.goToSubTabLocalizations(page);

    const pageTitle = await localizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(localizationPage.pageTitle);
  });

  it(`should choose '${dataCurrencies.euro.isoCode}' as default currency`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setEURAsDefaultCurrency', baseContext);

    const textResult = await localizationPage.setDefaultCurrency(
      page,
      `${dataCurrencies.euro.name} (${dataCurrencies.euro.isoCode})`,
    );
    expect(textResult).to.contains(localizationPage.successfulSettingsUpdateMessage);
  });

  it('should go to Currencies Tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesTab2', baseContext);

    await localizationPage.goToSubTabCurrencies(page);

    const pageTitle = await currenciesPage.getPageTitle(page);
    expect(pageTitle).to.contains(currenciesPage.pageTitle);
  });

  it(`should filter by iso code of currency '${dataCurrencies.usd.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterToUSDCurrency1', baseContext);

    // Filter
    await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.usd.isoCode);

    // Check number of currencies
    const numberOfCurrenciesAfterFilter = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterFilter).to.be.equal(1);

    // Check currency
    const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
    expect(textColumn).to.contains(dataCurrencies.usd.isoCode);
  });

  it('should delete currency', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrencyUSD', baseContext);

    const result = await currenciesPage.deleteCurrency(page, 1);
    expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
  });

  it(`should select rows except '${dataCurrencies.euro.isoCode}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetAndSelectRowsExceptEUR', baseContext);

    const numberOfCurrenciesAfterDelete = await currenciesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCurrenciesAfterDelete).to.be.eq(numberOfCurrencies + currencies.length - 1);

    const isBulkActionsEnabledBeforeSelect = await currenciesPage.isBulkActionsEnabled(page);
    expect(isBulkActionsEnabledBeforeSelect).to.eq(false);

    for (let numRow = 1; numRow <= numberOfCurrenciesAfterDelete; numRow++) {
      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, numRow, 'iso_code');

      if (textColumn !== dataCurrencies.euro.isoCode) {
        await currenciesPage.selectRow(page, numRow);
      }
    }

    const isBulkActionsEnabledAfterSelect = await currenciesPage.isBulkActionsEnabled(page);
    expect(isBulkActionsEnabledAfterSelect).to.eq(true);
  });

  it('should bulk delete currencies', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCurrencies', baseContext);

    const result = await currenciesPage.bulkDeleteCurrencies(page);
    expect(result).to.be.eq(currenciesPage.successfulMultiDeleteMessage);

    const numberOfCurrenciesAfterBulkDeete = await currenciesPage.getNumberOfElementInGrid(page);
    expect(numberOfCurrenciesAfterBulkDeete).to.be.equal(numberOfCurrencies);
  });
});
