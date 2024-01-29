// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import localizationPage from '@pages/BO/international/localization';
import translationsPage from '@pages/BO/international/translations';
import languagesPage from '@pages/BO/international/languages';
// Import FO pages
import {homePage} from '@pages/FO/classic/home';

// Import data
import Languages from '@data/demo/languages';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_international_translations_addUpdateLanguage';

describe('BO - International - Translation : Add update a language', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLanguages: number = 0;

  // before and after functions
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

  it('should go to \'International > Translations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.internationalParentLink,
      dashboardPage.translationsLink,
    );
    await translationsPage.closeSfToolBar(page);

    const pageTitle = await translationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it(`should select from update language the '${Languages.english.name}' language`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseLanguage1', baseContext);

    const textResult = await translationsPage.addUpdateLanguage(page, Languages.english.name);
    expect(textResult).to.equal(translationsPage.successAlertMessage);
  });

  it(`should select from add language the '${Languages.deutsch.name}' language`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseLanguage2', baseContext);

    const textResult = await translationsPage.addUpdateLanguage(page, Languages.deutsch.name);
    expect(textResult).to.equal(translationsPage.successAlertMessage);
  });

  it('should go to FO page and check the new language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFOAndCheckLanguage', baseContext);

    page = await translationsPage.viewMyShop(page);
    await homePage.changeLanguage(page, Languages.deutsch.isoCode);

    const isHomePage = await homePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await homePage.closePage(browserContext, page, 0);

    const pageTitle = await translationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(translationsPage.pageTitle);
  });

  it(`should check that the language '${Languages.deutsch.name}' is visible in update a language list`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLanguage', baseContext);

    const languagesInUpdateSection = await translationsPage.getLanguagesFromUpdateResult(page);
    expect(languagesInUpdateSection).to.contains(Languages.deutsch.name);
  });

  // Post-condition : Delete language
  describe('POST-TEST: Delete language', async () => {
    it('should go to localization page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.internationalParentLink,
        dashboardPage.localizationLink,
      );

      const pageTitle = await localizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(localizationPage.pageTitle);
    });

    it('should go to languages page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await localizationPage.goToSubTabLanguages(page);

      const pageTitle = await languagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(languagesPage.pageTitle);
    });

    it('should reset all filters and get number of languages in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguages).to.be.above(0);
    });

    it(`should filter language by name '${Languages.deutsch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await languagesPage.filterTable(page, 'input', 'name', Languages.deutsch.name);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(Languages.deutsch.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

      const textResult = await languagesPage.deleteLanguage(page, 1);
      expect(textResult).to.to.contains(languagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages - 1);
    });
  });
});
