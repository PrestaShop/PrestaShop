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
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_defaultCurrency';

let browser;
let page;
const contentToImport = {
  importCurrencies: true,
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
Choose default currency CLP then check it in FO
Choose default currency Euro then check in FO
Delete localization pack
 */

describe('Update default currency', async () => {
  describe('Import a localization pack', async () => {
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

    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);
      const textResult = await this.pageObjects.localizationPage.importLocalizationPack('Chile', contentToImport);
      await expect(textResult).to.equal(this.pageObjects.localizationPage.importLocalizationPackSuccessfulMessage);
    });
  });

  const tests = [
    {
      args: {
        defaultCurrency: `${Currencies.chileanPeso.name} (${Currencies.chileanPeso.isoCode})`,
        currency: `${Currencies.chileanPeso.isoCode} ${Currencies.chileanPeso.symbol}`,
      },
    },
    {
      args: {
        defaultCurrency: `${Currencies.euro.name} (${Currencies.euro.isoCode})`,
        currency: `${Currencies.euro.isoCode} ${Currencies.euro.symbol}`,
      },
    },
  ];
  tests.forEach((test, index) => {
    describe(`Choose default currency '${test.args.defaultCurrency}' and check it in FO`, async () => {
      before(async function () {
        browser = await helper.createBrowser();
        page = await helper.newTab(browser);
        this.pageObjects = await init();
      });
      after(async () => {
        await helper.closeBrowser(browser);
      });

      loginCommon.loginBO();

      it('should go to localization page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          'goToLocalizationPageToChooseDefaultCurrency',
          baseContext,
        );
        await this.pageObjects.boBasePage.goToSubMenu(
          this.pageObjects.boBasePage.internationalParentLink,
          this.pageObjects.boBasePage.localizationLink,
        );
        await this.pageObjects.boBasePage.closeSfToolBar();
        const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
      });

      it('should choose default currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCurrency${index}`, baseContext);
        const textResult = await this.pageObjects.localizationPage.setDefaultCurrency(test.args.defaultCurrency);
        await expect(textResult).to.equal('Update successful');
      });

      it('should go to FO and check the existence of currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCurrencyInFO${index}`, baseContext);
        page = await this.pageObjects.boBasePage.viewMyShop();
        this.pageObjects = await init();
        const defaultCurrency = await this.pageObjects.foBasePage.getDefaultCurrency();
        await expect(defaultCurrency).to.equal(test.args.currency);
        page = await this.pageObjects.foBasePage.closePage(browser, 1);
        this.pageObjects = await init();
      });

      if (index === (tests.length - 1)) {
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
      }
    });
  });
});
