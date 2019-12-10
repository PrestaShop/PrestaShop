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
const AddCurrencyPage = require('@pages/BO/international/currencies/add');
const FOBasePage = require('@pages/FO/FObasePage');
// Import Data
const {Currencies} = require('@data/demo/currencies');

let browser;
let page;
let numberOfCurrencies = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalizationPage(page),
    currenciesPage: new CurrenciesPage(page),
    addCurrencyPage: new AddCurrencyPage(page),
    foBasePage: new FOBasePage(page),
  };
};

/*
Create unofficial currency
Check data created in table
Check Creation of currency in FO
Delete currency
 */
describe('Create unofficial currency and check it in FO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to currency page', async function () {
    await this.pageObjects.localizationPage.goToSubTabCurrencies();
    const pageTitle = await this.pageObjects.currenciesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.currenciesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    numberOfCurrencies = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
    await expect(numberOfCurrencies).to.be.above(0);
  });

  describe('Create unofficial currency', async () => {
    it('should go to create new currency page', async function () {
      await this.pageObjects.currenciesPage.goToAddNewCurrencyPage();
      const pageTitle = await this.pageObjects.addCurrencyPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCurrencyPage.pageTitle);
    });

    it('should create unofficial currency', async function () {
      const textResult = await this.pageObjects.addCurrencyPage.createUnOfficialCurrency(Currencies.toman);
      await expect(textResult).to.contains(this.pageObjects.currenciesPage.successfulCreationMessage);
      const numberOfCurrenciesAfterCreation = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
      await expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });

    it(
      `should filter by iso code of currency '${Currencies.toman.isoCode}' and check values created in table`,
      async function () {
        await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.toman.isoCode);
        const numberOfCurrenciesAfterFilter = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
        await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);
        const createdCurrency = await this.pageObjects.currenciesPage.getCurrencyFromTable(1);
        await Promise.all([
          expect(createdCurrency.name).to.contains(Currencies.toman.name),
          expect(createdCurrency.symbol).to.contains(Currencies.toman.symbol),
          expect(createdCurrency.isoCode).to.contains(Currencies.toman.isoCode),
          expect(createdCurrency.exchangeRate).to.be.above(0),
          expect(createdCurrency.enabled).to.be.equal(Currencies.toman.enabled),
        ]);
      },
    );

    it('should go to FO and check the new currency', async function () {
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeCurrency(`${Currencies.toman.isoCode} ${Currencies.toman.symbol}`);
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });

    it('should reset filters', async function () {
      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Disable and check currency in FO', async () => {
    it(`should filter by iso code of currency '${Currencies.toman.isoCode}'`, async function () {
      await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.toman.isoCode);
      const numberOfCurrenciesAfterFilter = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
      await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);
      const textColumn = await this.pageObjects.currenciesPage.getTextColumnFromTableCurrency(1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.toman.isoCode);
    });

    it('should disable currency', async function () {
      const isActionPerformed = await this.pageObjects.currenciesPage.updateEnabledValue(1, false);
      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.currenciesPage.getTextContent(
          this.pageObjects.currenciesPage.alertSuccessBlockParagraph,
        );
        await expect(resultMessage).to.contains(this.pageObjects.currenciesPage.successfulUpdateStatusMessage);
      }
      const isStatusChanged = await this.pageObjects.currenciesPage.getToggleColumnValue(1);
      await expect(isStatusChanged).to.be.equal(false);
    });

    it('should go to FO and check the new currency', async function () {
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      let textError = '';
      try {
        await this.pageObjects.foBasePage.changeCurrency(`${Currencies.toman.isoCode} ${Currencies.toman.symbol}`);
      } catch (e) {
        textError = e.toString();
      }
      await expect(textError).to.contains(
        `${Currencies.toman.isoCode} ${Currencies.toman.symbol} was not found as option of select`,
      );
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });

    it('should reset filters', async function () {
      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Delete currency created ', async () => {
    it(`should filter by iso code of currency '${Currencies.toman.isoCode}'`, async function () {
      await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.toman.isoCode);
      const numberOfCurrenciesAfterFilter = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
      await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);
      const textColumn = await this.pageObjects.currenciesPage.getTextColumnFromTableCurrency(1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.toman.isoCode);
    });

    it('should delete currency', async function () {
      const result = await this.pageObjects.currenciesPage.deleteCurrency(1);
      await expect(result).to.be.equal(this.pageObjects.currenciesPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies);
    });
  });
});
