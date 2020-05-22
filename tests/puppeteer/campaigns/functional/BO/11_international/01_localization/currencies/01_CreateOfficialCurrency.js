require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const LocalizationPage = require('@pages/BO/international/localization');
const CurrenciesPage = require('@pages/BO/international/currencies');
const AddCurrencyPage = require('@pages/BO/international/currencies/add');
const FOBasePage = require('@pages/FO/FObasePage');

// Import Data
const {Currencies} = require('@data/demo/currencies');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_currencies_CreateOfficialCurrency';

let browser;
let page;
let numberOfCurrencies = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalizationPage(page),
    currenciesPage: new CurrenciesPage(page),
    addCurrencyPage: new AddCurrencyPage(page),
    foBasePage: new FOBasePage(page),
  };
};

/*
Create official currency
Check data created in table
Check Creation of currency in FO
Delete currency
 */
describe('Create official currency and check it in FO', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.internationalParentLink,
      this.pageObjects.dashboardPage.localizationLink,
    );

    await this.pageObjects.localizationPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to currencies page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

    await this.pageObjects.localizationPage.goToSubTabCurrencies();
    const pageTitle = await this.pageObjects.currenciesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.currenciesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCurrencies = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
    await expect(numberOfCurrencies).to.be.above(0);
  });

  describe('Create official currency', async () => {
    it('should go to create new currency page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewCurrencyPage', baseContext);

      await this.pageObjects.currenciesPage.goToAddNewCurrencyPage();
      const pageTitle = await this.pageObjects.addCurrencyPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCurrencyPage.pageTitle);
    });

    it('should create official currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createOfficialCurrency', baseContext);

      // Create and check successful message
      const textResult = await this.pageObjects.addCurrencyPage.addOfficialCurrency(Currencies.mad);
      await expect(textResult).to.contains(this.pageObjects.currenciesPage.successfulCreationMessage);

      // Check number of currencies after creation
      const numberOfCurrenciesAfterCreation = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
      await expect(numberOfCurrenciesAfterCreation).to.be.equal(numberOfCurrencies + 1);
    });

    it(
      `should filter by iso code of currency '${Currencies.mad.isoCode}' and check values created in table`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyValues', baseContext);

        // Filter
        await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.mad.isoCode);

        // Check number of currencies
        const numberOfCurrenciesAfterFilter = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
        await expect(numberOfCurrenciesAfterFilter).to.be.at.most(numberOfCurrencies + 1);

        // Check currency created
        const createdCurrency = await this.pageObjects.currenciesPage.getCurrencyFromTable(1);

        await Promise.all([
          expect(createdCurrency.name).to.contains(Currencies.mad.name),
          expect(createdCurrency.symbol).to.contains(Currencies.mad.symbol),
          expect(createdCurrency.isoCode).to.contains(Currencies.mad.isoCode),
          expect(createdCurrency.exchangeRate).to.be.above(0),
          expect(createdCurrency.enabled).to.be.equal(Currencies.mad.enabled),
        ]);
      },
    );

    it('should go to FO and check the new currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCurrencyInFO', baseContext);

      // View my shop and int pages
      page = await this.pageObjects.currenciesPage.viewMyShop();
      this.pageObjects = await init();

      // Check currency
      await this.pageObjects.foBasePage.changeCurrency(`${Currencies.mad.isoCode} ${Currencies.mad.symbol}`);

      // Go back to BO
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterCreation', baseContext);

      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Disable and check currency in FO', async () => {
    it(`should filter by iso code of currency '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDisableCurrency', baseContext);

      // Filter
      await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.mad.isoCode);

      // Check number of currencies
      const numberOfCurrenciesAfterFilter = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
      await expect(numberOfCurrenciesAfterFilter).to.be.at.most(numberOfCurrencies + 1);

      // Check existence of currency created
      const textColumn = await this.pageObjects.currenciesPage.getTextColumnFromTableCurrency(1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.mad.isoCode);
    });

    it('should disable currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCurrency', baseContext);

      // Disable currency and check successful message
      const isActionPerformed = await this.pageObjects.currenciesPage.updateEnabledValue(1, false);

      if (isActionPerformed) {
        const resultMessage = await this.pageObjects.currenciesPage.getTextContent(
          this.pageObjects.currenciesPage.alertSuccessBlockParagraph,
        );

        await expect(resultMessage).to.contains(this.pageObjects.currenciesPage.successfulUpdateStatusMessage);
      }

      // Check currency disabled
      const currencyStatus = await this.pageObjects.currenciesPage.getToggleColumnValue(1);
      await expect(currencyStatus).to.be.equal(false);
    });

    it('should go to FO and check the new currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledCurrency', baseContext);

      // View my shop and init pages
      page = await this.pageObjects.currenciesPage.viewMyShop();
      this.pageObjects = await init();

      // Check currency non existence
      let textError = '';

      try {
        await this.pageObjects.foBasePage.changeCurrency(`${Currencies.mad.isoCode} ${Currencies.mad.symbol}`);
      } catch (e) {
        textError = e.toString();
      }

      await expect(textError).to.contains(
        `${Currencies.mad.isoCode} ${Currencies.mad.symbol} was not found as option of select`,
      );

      // Go back to BO
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDisable', baseContext);

      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies + 1);
    });
  });

  describe('Delete currency created ', async () => {
    it(`should filter by iso code of currency '${Currencies.mad.isoCode}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await this.pageObjects.currenciesPage.filterTable('input', 'iso_code', Currencies.mad.isoCode);

      const numberOfCurrenciesAfterFilter = await this.pageObjects.currenciesPage.getNumberOfElementInGrid();
      await expect(numberOfCurrenciesAfterFilter).to.be.equal(numberOfCurrencies);

      // Check currency to delete
      const textColumn = await this.pageObjects.currenciesPage.getTextColumnFromTableCurrency(1, 'iso_code');
      await expect(textColumn).to.contains(Currencies.mad.isoCode);
    });

    it('should delete currency', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

      const result = await this.pageObjects.currenciesPage.deleteCurrency(1);
      await expect(result).to.be.equal(this.pageObjects.currenciesPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCurrenciesAfterReset = await this.pageObjects.currenciesPage.resetAndGetNumberOfLines();
      await expect(numberOfCurrenciesAfterReset).to.be.equal(numberOfCurrencies);
    });
  });
});
