require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const languagesPage = require('@pages/BO/international/languages');
const foHomePage = require('@pages/FO/home');

// Import Data
const {Currencies} = require('@data/demo/currencies');
const {Languages} = require('@data/demo/languages');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_importLocalizationPack';

let browserContext;
let page;
const contentToImport = {
  importStates: false,
  importTaxes: true,
  importCurrencies: true,
  importLanguages: true,
  importUnits: false,
  updatePriceDisplayForGroups: false,
};

/*
Import localization pack for 'chile' in BO
Check Language 'Spanish' and currency 'Chilean Peso' in FO
Delete 'spanish' language
Delete 'Chilean Peso' currency
 */

describe('Import a localization pack including a language and a currency', async () => {
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

  it('should go to localization page', async function () {
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

  describe('Import localization pack and check existence of language and currency in FO', async () => {
    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);

      const textResult = await localizationPage.importLocalizationPack(page, 'Chile', contentToImport);
      await expect(textResult).to.equal(localizationPage.importLocalizationPackSuccessfulMessage);
    });

    it('should go to FO and check the existence of currency and language added', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyAndLanguageInFO', baseContext);

      // Go to FO and init pages
      page = await localizationPage.viewMyShop(page);

      await foHomePage.changeCurrency(
        page,
        `${Currencies.chileanPeso.isoCode} ${Currencies.chileanPeso.symbol}`,
      );

      await foHomePage.changeLanguage(page, Languages.spanish.isoCode);

      // Go back to BO
      page = await foHomePage.closePage(browserContext, page, 0);
    });
  });

  describe('Delete language added by importing localization pack', async () => {
    it('should go to languages page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await localizationPage.goToSubTabLanguages(page);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });

    it(`should filter language by name '${Languages.spanish.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterLanguages', baseContext);

      await languagesPage.filterTable(page, 'input', 'name', Languages.spanish.name);

      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textColumn).to.contains(Languages.spanish.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

      const textResult = await languagesPage.deleteLanguage(page, 1);
      await expect(textResult).to.to.contains(languagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetLanguages', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.be.at.least(1);
    });
  });

  describe('Delete currency added by importing localization pack', async () => {
    it('should go to currencies page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

      await localizationPage.goToSubTabCurrencies(page);
      const pageTitle = await currenciesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(currenciesPage.pageTitle);
    });

    it(`should filter by iso code of currency '${Currencies.chileanPeso.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCurrencies', baseContext);

      await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.chileanPeso.isoCode);

      const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.chileanPeso.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await currenciesPage.deleteCurrency(page, 1);
      await expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencies', baseContext);

      const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCurrenciesAfterReset).to.be.at.least(1);
    });
  });
});
