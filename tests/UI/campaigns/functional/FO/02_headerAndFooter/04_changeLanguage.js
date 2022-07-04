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
Pre-condition:
- Delete the french language
Scenario:
- Go to FO and check that there is only one language

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
  describe('Disable \'Français\' language', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'International > Localization\' page', async function () {
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

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await localizationPage.goToSubTabLanguages(page);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguages).to.be.above(0);
    });

    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguages).to.be.above(0);
    });
  });

  describe('Check that the languages list is not visible', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

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


  // Enable language
  describe('Enable \'Français\' language', async () => {
    it('should go to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openBOPage', baseContext);

      await homePage.goToBO(page);

      const pageTitle = await dashboardPage.getPageTitle(page);
      await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    });

    it('should go to \'International > Localization\' page', async function () {
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

    it('should go to \'Languages\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await localizationPage.goToSubTabLanguages(page);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });

    it('should filter by iso_code \'fr\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      // Filter table
      await languagesPage.filterTable(page, 'input', 'iso_code', Languages.french.isoCode);

      // Check number od languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'iso_code');
      await expect(textColumn).to.contains(Languages.french.isoCode);
    });

    it('should enable language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableLanguage', baseContext);

      const isActionPerformed = await languagesPage.setStatus(page, 1, true);

      if (isActionPerformed) {
        const resultMessage = await languagesPage.getAlertSuccessBlockParagraphContent(page);
        await expect(resultMessage).to.contains(languagesPage.successfulUpdateStatusMessage);
      }
      const languageStatus = await languagesPage.getStatus(page, 1);
      await expect(languageStatus).to.be.true;
    });
    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'quickEditReset', baseContext);

      const numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguages).to.be.above(0);
    });
  });

  describe('Change language and check it', async () => {
    it('should go to FO home page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      await homePage.goToFo(page);

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should check that the languages list is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLanguageListVisible', baseContext);

      const isVisible = await homePage.isLanguageListVisible(page);
      await expect(isVisible, 'Language list is not visible!').to.be.true;
    });

    it('should change the shop language to \'Français\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLanguagesLink', baseContext);

      await homePage.changeLanguage(page, 'fr');

      const language = await homePage.getDefaultShopLanguage(page);
      expect(language).to.equal('Français');
    });

    it('should change the shop language to \'English\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkLanguagesLink', baseContext);

      await homePage.changeLanguage(page, 'en');

      const language = await homePage.getDefaultShopLanguage(page);
      expect(language).to.equal('English');
    });
  });
});
