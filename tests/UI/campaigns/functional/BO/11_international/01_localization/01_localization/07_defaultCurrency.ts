// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import currenciesPage from '@pages/BO/international/currencies';
import languagesPage from '@pages/BO/international/languages';

import {
  boDashboardPage,
  boLocalizationPage,
  dataCurrencies,
  dataLanguages,
  foClassicHomePage,
  type ImportContent,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'International > Localization\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPageToImportPack', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.localizationLink,
      );
      await boLocalizationPage.closeSfToolBar(page);

      const pageTitle = await boLocalizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
    });

    it('should import localization pack', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importLocalizationPack', baseContext);

      const textResult = await boLocalizationPage.importLocalizationPack(page, 'Chile', contentToImport);
      expect(textResult).to.equal(boLocalizationPage.importLocalizationPackSuccessfulMessage);
    });
  });

  const currenciesToTest = [
    {
      args: {
        defaultCurrency: `${dataCurrencies.chileanPeso.name} (${dataCurrencies.chileanPeso.isoCode})`,
        currency: `${dataCurrencies.chileanPeso.isoCode} ${dataCurrencies.chileanPeso.symbol}`,
      },
    },
    {
      args: {
        defaultCurrency: `${dataCurrencies.euro.name} (${dataCurrencies.euro.isoCode})`,
        currency: `${dataCurrencies.euro.isoCode} ${dataCurrencies.euro.symbol}`,
      },
    },
  ];

  currenciesToTest.forEach((test, index: number) => {
    describe(`Choose default currency '${test.args.defaultCurrency}' and check it in FO`, async () => {
      before(async function () {
        browserContext = await utilsPlaywright.createBrowserContext(this.browser);
        page = await utilsPlaywright.newTab(browserContext);
      });

      after(async () => {
        await utilsPlaywright.closeBrowserContext(browserContext);
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

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.internationalParentLink,
          boDashboardPage.localizationLink,
        );
        await boLocalizationPage.closeSfToolBar(page);

        const pageTitle = await boLocalizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
      });

      it('should choose default currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setDefaultCurrency${index}`, baseContext);

        const textResult = await boLocalizationPage.setDefaultCurrency(page, test.args.defaultCurrency);
        expect(textResult).to.contain(boLocalizationPage.successfulSettingsUpdateMessage);
      });

      it('should go to FO and check the existence of currency', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkCurrencyInFO${index}`, baseContext);

        // View my shop and init pages
        page = await boLocalizationPage.viewMyShop(page);

        const defaultCurrency = await foClassicHomePage.getDefaultCurrency(page);
        expect(defaultCurrency).to.equal(test.args.currency);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);

        page = await foClassicHomePage.closePage(browserContext, page, 0);

        const pageTitle = await boLocalizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
      });

      if (index === (currenciesToTest.length - 1)) {
        describe('Delete currency added by importing localization pack', async () => {
          it('should go to currencies page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'goToCurrenciesPage', baseContext);

            await boLocalizationPage.goToSubTabCurrencies(page);

            const pageTitle = await currenciesPage.getPageTitle(page);
            expect(pageTitle).to.contains(currenciesPage.pageTitle);
          });

          it(`should filter by iso code of currency '${dataCurrencies.chileanPeso.isoCode}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'filterCurrencies', baseContext);

            await currenciesPage.filterTable(page, 'input', 'iso_code', dataCurrencies.chileanPeso.isoCode);

            const textColumn = await currenciesPage.getTextColumnFromTableCurrency(page, 1, 'iso_code');
            expect(textColumn).to.contains(dataCurrencies.chileanPeso.isoCode);
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

            await boLocalizationPage.goToSubTabLanguages(page);

            const pageTitle = await languagesPage.getPageTitle(page);
            expect(pageTitle).to.contains(languagesPage.pageTitle);
          });

          it(`should filter language by name '${dataLanguages.spanish.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', 'filterLanguages', baseContext);

            await languagesPage.filterTable(page, 'input', 'name', dataLanguages.spanish.name);

            const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
            expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

            const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
            expect(textColumn).to.contains(dataLanguages.spanish.name);
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
