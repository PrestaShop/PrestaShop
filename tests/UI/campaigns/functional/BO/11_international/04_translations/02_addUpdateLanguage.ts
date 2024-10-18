// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLanguagesPage,
  boLoginPage,
  boLocalizationPage,
  boTranslationsPage,
  type BrowserContext,
  dataLanguages,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_translations_addUpdateLanguage';

describe('BO - International - Translation : Add update a language', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLanguages: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'International > Translations\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTranslationsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.translationsLink,
    );
    await boTranslationsPage.closeSfToolBar(page);

    const pageTitle = await boTranslationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTranslationsPage.pageTitle);
  });

  it(`should select from update language the '${dataLanguages.english.name}' language`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseLanguage1', baseContext);

    const textResult = await boTranslationsPage.addUpdateLanguage(page, dataLanguages.english.name);
    expect(textResult).to.equal(boTranslationsPage.successAlertMessage);
  });

  it(`should select from add language the '${dataLanguages.deutsch.name}' language`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'chooseLanguage2', baseContext);

    const textResult = await boTranslationsPage.addUpdateLanguage(page, dataLanguages.deutsch.name);
    expect(textResult).to.equal(boTranslationsPage.successAlertMessage);
  });

  it('should go to FO page and check the new language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFOAndCheckLanguage', baseContext);

    page = await boTranslationsPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, dataLanguages.deutsch.isoCode);

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const pageTitle = await boTranslationsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTranslationsPage.pageTitle);
  });

  it(`should check that the language '${dataLanguages.deutsch.name}' is visible in update a language list`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkLanguage', baseContext);

    const languagesInUpdateSection = await boTranslationsPage.getLanguagesFromUpdateResult(page);
    expect(languagesInUpdateSection).to.contains(dataLanguages.deutsch.name);
  });

  // Post-condition : Delete language
  describe('POST-TEST: Delete language', async () => {
    it('should go to localization page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.internationalParentLink,
        boDashboardPage.localizationLink,
      );

      const pageTitle = await boLocalizationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
    });

    it('should go to languages page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);

      await boLocalizationPage.goToSubTabLanguages(page);

      const pageTitle = await boLanguagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesPage.pageTitle);
    });

    it('should reset all filters and get number of languages in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfLanguages = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguages).to.be.above(0);
    });

    it(`should filter language by name '${dataLanguages.deutsch.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await boLanguagesPage.filterTable(page, 'input', 'name', dataLanguages.deutsch.name);

      const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(dataLanguages.deutsch.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

      const textResult = await boLanguagesPage.deleteLanguage(page, 1);
      expect(textResult).to.to.contains(boLanguagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages - 1);
    });
  });
});
