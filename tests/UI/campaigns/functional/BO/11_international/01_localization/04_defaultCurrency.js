require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const currenciesPage = require('@pages/BO/international/currencies');
const foHomePage = require('@pages/FO/home');

// Import Data
const {Currencies} = require('@data/demo/currencies');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_defaultCurrency';

let browserContext;
let page;
const contentToImport = {
  importCurrencies: true,
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPageToImportPack', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.localizationLink,
      );

      await localizationPage.closeSfToolBar(page);

      const pageTitle = await localizationPage.getPageTitle(page);
      await expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);

      const textResult = await localizationPage.importLocalizationPack(page, 'Chile', contentToImport);
      await expect(textResult).to.equal(localizationPage.importLocalizationPackSuccessfulMessage);
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
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToLocalizationPageToChooseDefaultCurrency${index}`,
          baseContext,
        );

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );

        await localizationPage.closeSfToolBar(page);

        const pageTitle = await localizationPage.getPageTitle(page);
        await expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should choose default currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCurrency${index}`, baseContext);

        const textResult = await localizationPage.setDefaultCurrency(page, test.args.defaultCurrency);
        await expect(textResult).to.equal('Update successful');
      });

      it('should go to FO and check the existence of currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCurrencyInFO${index}`, baseContext);

        // View my shop and init pages
        page = await localizationPage.viewMyShop(page);

        const defaultCurrency = await foHomePage.getDefaultCurrency(page);
        await expect(defaultCurrency).to.equal(test.args.currency);

        // Go back to BO
        page = await foHomePage.closePage(browserContext, page, 0);
      });

      if (index === (tests.length - 1)) {
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
      }
    });
  });
});
