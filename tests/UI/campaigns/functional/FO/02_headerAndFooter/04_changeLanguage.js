require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import FO pages
const homePage = require('@pages/FO/home');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');

// Import data
const {Languages} = require('@data/demo/languages');

const baseContext = 'functional_FO_headerAndFooter_changeLanguage';

let browserContext;
let page;

/*
Scenario:
- Disable french language
- Go to FO and check that there is only one language
- Enable french language
- Go to Fo and change language
 */
describe('FO - Header and Footer : Change language', async () => {
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
      await expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage1', baseContext);

      await localizationPage.goToSubTabLanguages(page);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst1', baseContext);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguages).to.be.above(0);
    });

    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit1', baseContext);

      // Filter table
      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Languages.french.isoCode);
    });

    it('should disable language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableLanguage', baseContext);

      const isActionPerformed = await languagesPage.setStatus(page, 1, false);

      if (isActionPerformed) {
        const resultMessage = await languagesPage.getAlertSuccessBlockParagraphContent(page);
        await expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
      }
      const languageStatus = await languagesPage.getStatus(page, 1);
      await expect(languageStatus).to.be.false;
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset1', baseContext);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguages).to.be.above(0);
    });
  });

  // 2 - Check that languages list is not visible
  describe('Check that the languages list is not visible', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO1', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check that the languages list is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLanguageListNotVisible', baseContext);

      const isVisible = await homePage.isLanguageListVisible(page);
      await expect(isVisible, 'Language list is visible!').to.be.false;
    });

    it('should check that the shop language is \'English\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkShopLanguage', baseContext);

      const language = await homePage.getShopLanguage(page);
      await expect(language).to.equal('en-US');
    });
  });

  // 3 - Enable language
  describe('Enable \'French\' language', async () => {
    it('should go to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openBOPage2', baseContext);

      await homePage.goToBO(page);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
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
      await expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage2', baseContext);

      await localizationPage.goToSubTabLanguages(page);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });

    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit2', baseContext);

      // Filter table
      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Languages.french.isoCode);
    });

    it('should enable language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableLanguage', baseContext);

      const isActionPerformed = await languagesPage.setStatus(page, 1, true);

      if (isActionPerformed) {
        const resultMessage = await languagesPage.getAlertSuccessBlockParagraphContent(page);
        await expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
      }
      const languageStatus = await languagesPage.getStatus(page, 1);
      await expect(languageStatus).to.be.true;
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset2', baseContext);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguages).to.be.above(0);
    });
  });

  // 4 - Change language and check it
  describe('Change language and check it', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO2', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check that the languages list is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLanguageListVisible', baseContext);

      const isVisible = await homePage.isLanguageListVisible(page);
      await expect(isVisible, 'Language list is not visible!').to.be.true;
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
