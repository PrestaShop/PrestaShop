// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {installHummingbird, uninstallHummingbird} from '@commonTests/FO/hummingbird';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import languagesPage from '@pages/BO/international/languages';
import localizationPage from '@pages/BO/international/localization';
// Import FO pages
import homePage from '@pages/FO/hummingbird/home';

// Import data
import Languages from '@data/demo/languages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_hummingbird_headerAndFooter_changeLanguage';

/*
Scenario:
- Disable french language
- Go to FO and check that there is only one language
- Enable french language
- Go to Fo and change language
 */
describe('FO - Header and Footer : Change language', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-condition : Install Hummingbird
  installHummingbird(`${baseContext}_preTest_1`);

  describe('Change language', async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
    });

    // 1 - Disable language
    describe('Disable \'French\' language', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'International > Localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage1', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );
        await localizationPage.closeSfToolBar(page);

        const pageTitle = await localizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should go to \'Languages\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage1', baseContext);

        await localizationPage.goToSubTabLanguages(page);

        const pageTitle = await languagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(languagesPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst1', baseContext);

        const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
        expect(numberOfLanguages).to.be.above(0);
      });

      it('should filter by iso_code \'fr\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit1', baseContext);

        // Filter table
        await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

        // Check number od languages
        const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
        expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

        const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
        expect(textColumn).to.contains(Languages.french.isoCode);
      });

      it('should disable language', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'disableLanguage', baseContext);

        const isActionPerformed = await languagesPage.setStatus(page, 1, false);

        if (isActionPerformed) {
          const resultMessage = await languagesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
        }
        const languageStatus = await languagesPage.getStatus(page, 1);
        expect(languageStatus).to.eq(false);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset1', baseContext);

        const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
        expect(numberOfLanguages).to.be.above(0);
      });
    });

    // 2 - Check that languages list is not visible
    describe('Check that the languages list is not visible', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should check that the languages list is not visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLanguageListNotVisible', baseContext);

        const isVisible = await homePage.isLanguageListVisible(page);
        expect(isVisible, 'Language list is visible!').to.eq(false);
      });

      it('should check that the shop language is \'English\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkShopLanguage', baseContext);

        const language = await homePage.getShopLanguage(page);
        expect(language).to.equal('en-US');
      });
    });

    // 3 - Enable language
    describe('Enable \'French\' language', async () => {
      it('should go to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'openBOPage2', baseContext);

        await homePage.goToBO(page);

        const pageTitle = await dashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(dashboardPage.pageTitle);
      });

      it('should go to \'International > Localization\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage2', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.internationalParentLink,
          dashboardPage.localizationLink,
        );
        await localizationPage.closeSfToolBar(page);

        const pageTitle = await localizationPage.getPageTitle(page);
        expect(pageTitle).to.contains(localizationPage.pageTitle);
      });

      it('should go to \'Languages\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage2', baseContext);

        await localizationPage.goToSubTabLanguages(page);

        const pageTitle = await languagesPage.getPageTitle(page);
        expect(pageTitle).to.contains(languagesPage.pageTitle);
      });

      it('should filter by iso_code \'fr\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit2', baseContext);

        // Filter table
        await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

        // Check number od languages
        const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
        expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

        const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
        expect(textColumn).to.contains(Languages.french.isoCode);
      });

      it('should enable language', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'enableLanguage', baseContext);

        const isActionPerformed = await languagesPage.setStatus(page, 1, true);

        if (isActionPerformed) {
          const resultMessage = await languagesPage.getAlertSuccessBlockParagraphContent(page);
          expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
        }
        const languageStatus = await languagesPage.getStatus(page, 1);
        expect(languageStatus).to.eq(true);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset2', baseContext);

        const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
        expect(numberOfLanguages).to.be.above(0);
      });
    });

    // 4 - Change language and check it
    describe('Change language and check it', async () => {
      it('should go to FO home page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

        await homePage.goToFo(page);

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should check that the languages list is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkLanguageListVisible', baseContext);

        const isVisible = await homePage.isLanguageListVisible(page);
        expect(isVisible, 'Language list is not visible!').to.eq(true);
      });

      it('should change the shop language to \'French\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'ChangeLanguageToFrench', baseContext);

        await homePage.changeLanguage(page, 'fr');

        const language = await homePage.getDefaultShopLanguage(page);
        expect(language, 'Language is not changed to French!').to.equal('Français');
      });

      it('should change the shop language to \'English\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'ChangeLanguageToEnglish', baseContext);

        await homePage.changeLanguage(page, 'en');

        const language = await homePage.getDefaultShopLanguage(page);
        expect(language, 'Language is not changed to English!').to.equal('English');
      });
    });
  });

  // Post-condition : Uninstall Hummingbird
  uninstallHummingbird(`${baseContext}_postTest_1`);
});
