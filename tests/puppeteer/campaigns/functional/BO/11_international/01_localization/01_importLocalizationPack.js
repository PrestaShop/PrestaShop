require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const LocalizationPage = require('@pages/BO/international/localization');
const CurrenciesPage = require('@pages/BO/international/currencies');
const LanguagesPage = require('@pages/BO/international/languages');
const FOBasePage = require('@pages/FO/FObasePage');
// Import Data
const {Currencies} = require('@data/demo/currencies');
const {Languages} = require('@data/demo/languages');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_importLocalizationPack';

let browser;
let page;
const contentToImport = {
  importStates: false,
  importTaxes: true,
  importCurrencies: true,
  importLanguages: true,
  importUnits: false,
  updatePriceDisplayForGroups: false,
};

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalizationPage(page),
    currenciesPage: new CurrenciesPage(page),
    languagesPage: new LanguagesPage(page),
    foBasePage: new FOBasePage(page),
  };
};

/*
Import localization pack for 'chile' in BO
Check Language 'Spanish' and currency 'Chilean Peso' in FO
Delete 'spanish' language
Delete 'Chilean Peso' currency
 */

describe('Import a localization pack including a language and a currency', async () => {
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to localization page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  describe('Import localization pack and check existence of language and currency in FO', async () => {
    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);
      const textResult = await this.pageObjects.localizationPage.importLocalizationPack('Chile', contentToImport);
      await expect(textResult).to.equal(this.pageObjects.localizationPage.importLocalizationPackSuccessfulMessage);
    });

    it('should go to FO and check the existence of currency and language added', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyAndLanguageInFO', baseContext);
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeCurrency(
        `${Currencies.chileanPeso.isoCode} ${Currencies.chileanPeso.symbol}`,
      );
      await this.pageObjects.foBasePage.changeLanguage(Languages.spanish.isoCode);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });

  describe('Delete language added by importing localization pack', async () => {
    it('should go to languages page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);
      await this.pageObjects.localizationPage.goToSubTabLanguages();
      const pageTitle = await this.pageObjects.languagesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.languagesPage.pageTitle);
    });

    it(`should filter language by name '${Languages.spanish.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterLanguages', baseContext);
      await this.pageObjects.languagesPage.filterTable('input', 'name', Languages.spanish.name);
      const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);
      const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(1, 'name');
      await expect(textColumn).to.contains(Languages.spanish.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);
      const textResult = await this.pageObjects.languagesPage.deleteLanguage(1);
      await expect(textResult).to.to.contains(this.pageObjects.languagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetLanguages', baseContext);
      const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
      await expect(numberOfLanguagesAfterReset).to.be.at.least(1);
    });
  });

  describe('Delete currency added by importing localization pack', async () => {
    it('should go to currencies page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);
      await this.pageObjects.localizationPage.goToSubTabCurrencies();
      const pageTitle = await this.pageObjects.currenciesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.currenciesPage.pageTitle);
    });

    it(`should filter by iso code of currency '${Currencies.chileanPeso.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCurrencies', baseContext);
      await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.chileanPeso.isoCode);
      const textColumn = await this.pageObjects.currenciesPage.getTextColumnFromTableCurrency(1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.chileanPeso.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);
      const result = await this.pageObjects.currenciesPage.deleteCurrency(1);
      await expect(result).to.be.equal(this.pageObjects.currenciesPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencies', baseContext);
      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.at.least(1);
    });
  });
});
