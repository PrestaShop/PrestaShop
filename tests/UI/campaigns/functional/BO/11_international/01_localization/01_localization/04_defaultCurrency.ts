// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import localizationPage from '@pages/BO/international/localization';
import currenciesPage from '@pages/BO/international/currencies';
import languagesPage from '@pages/BO/international/languages';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';

// Import Data
import Currencies from '@data/demo/currencies';
import Languages from '@data/demo/languages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_localization_localization_defaultCurrency';

/*
Import localization pack for 'chile' in BO
Choose default currency CLP then check it in FO
Choose default currency Euro then check in FO
Delete localization pack
 */

describe('BO - International - Localization : Update default currency', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const contentToImport: ImportContent = {
    importCurrencies: true,
  };

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

    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPageToImportPack', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.localizationLink,
      );
      await localizationPage.closeSfToolBar(page);

      const pageTitle = await localizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);

      const textResult = await localizationPage.importLocalizationPack(page, 'Chile', contentToImport);
      expect(textResult).to.equal(localizationPage.importLocalizationPackSuccessfulMessage);
    });
  });

  const currenciesToTest = [
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

  currenciesToTest.forEach((test, index: number) => {
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

      it('should go to \'International > Localization\' page', async function () {
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
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should choose default currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCurrency${index}`, baseContext);

        const textResult = await localizationPage.setDefaultCurrency(page, test.args.defaultCurrency);
        expect(textResult).to.contain(localizationPage.successfulSettingsUpdateMessage);
      });

      it('should go to FO and check the existence of currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCurrencyInFO${index}`, baseContext);

        // View my shop and init pages
        page = await localizationPage.viewMyShop(page);

        const defaultCurrency = await foHomePage.getDefaultCurrency(page);
        expect(defaultCurrency).to.equal(test.args.currency);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        page = await foHomePage.closePage(browserContext, page, 0);

        const pageTitle = await localizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      if (index === (currenciesToTest.length - 1)) {
        describe('Delete currency added by importing localization pack', async () => {
          it('should go to currencies page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

            await localizationPage.goToSubTabCurrencies(page);

            const pageTitle = await currenciesPage.getPageTitle(page);
            expect(pageTitle).to.contains(currenciesPage.pageTitle);
          });

          it(`should filter by iso code of currency '${Currencies.chileanPeso.isoCode}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'filterCurrencies', baseContext);

            await currenciesPage.filterTable(page, 'input', 'iso_code', Currencies.chileanPeso.isoCode);

            const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
            expect(textColumn).to.contains(Currencies.chileanPeso.isoCode);
          });

          it('should delete currency', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'deleteCurrency', baseContext);

            const result = await currenciesPage.deleteCurrency(page, 1);
            expect(result).to.be.equal(currenciesPage.successfulDeleteMessage);
          });

          it('should reset filters', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'resetCurrencies', baseContext);

            const numberOfCurrenciesAfterReset = await currenciesPage.resetAndGetNumberOfLines(page);
            expect(numberOfCurrenciesAfterReset).to.be.at.least(1);
          });
        });

        describe('Delete language added by importing localization pack', async () => {
          it('should go to languages page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

            await localizationPage.goToSubTabLanguages(page);

            const pageTitle = await languagesPage.getPageTitle(page);
            expect(pageTitle).to.contains(languagesPage.pageTitle);
          });

          it(`should filter language by name '${Languages.spanish.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'filterLanguages', baseContext);

            await languagesPage.filterTable(page, 'input', 'name', Languages.spanish.name);

            const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
            expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

            const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
            expect(textColumn).to.contains(Languages.spanish.name);
          });

          it('should delete language', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

            const textResult = await languagesPage.deleteLanguage(page, 1);
            expect(textResult).to.to.contains(languagesPage.successfulDeleteMessage);
          });

          it('should reset all filters', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'resetLanguages', baseContext);

            const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
            expect(numberOfLanguagesAfterReset).to.be.at.least(1);
          });
        });
      }
    });
  });
});
