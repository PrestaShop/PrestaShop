// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLanguagesPage,
  boLanguagesCreatePage,
  boLocalizationPage,
  boLoginPage,
  type BrowserContext,
  FakerLanguage,
  foClassicHomePage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_languages_CRUDLanguages';

/*
Create enabled language
Verify that language exist in FO
Update language (and disable it)
Verify that language do not exist in FO
Delete language
 */
describe('BO - International - Languages : CRUD language', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLanguages: number = 0;

  const createLanguageData: FakerLanguage = new FakerLanguage({isoCode: 'de'});
  const editLanguageData: FakerLanguage = new FakerLanguage({isoCode: 'nl', enabled: false});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(createLanguageData.flag),
      utilsFile.generateImage(createLanguageData.noPicture),
      utilsFile.generateImage(editLanguageData.flag),
      utilsFile.generateImage(editLanguageData.noPicture),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await Promise.all([
      utilsFile.deleteFile(createLanguageData.flag),
      utilsFile.deleteFile(createLanguageData.noPicture),
      utilsFile.deleteFile(editLanguageData.flag),
      utilsFile.deleteFile(editLanguageData.noPicture),
    ]);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );
    await boLocalizationPage.closeSfToolBar(page);

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it('should go to \'Languages\' page', async function () {
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

  describe('Create Language', async () => {
    it('should go to add new language page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewLanguages', baseContext);

      await boLanguagesPage.goToAddNewLanguage(page);

      const pageTitle = await boLanguagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesCreatePage.pageTitle);
    });

    it('should create new language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewLanguages', baseContext);

      const textResult = await boLanguagesCreatePage.createEditLanguage(page, createLanguageData);
      expect(textResult).to.to.contains(boLanguagesPage.successfulCreationMessage);

      const numberOfLanguagesAfterCreation = await boLanguagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + 1);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo1', baseContext);

      // View my shop and get the new tab
      page = await boLanguagesPage.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should check that '${createLanguageData.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedLanguageFO', baseContext);

      const isLanguageInFO = await foClassicHomePage.languageExists(page, createLanguageData.isoCode);
      expect(isLanguageInFO, `${createLanguageData.name} was not found as a language in FO`).to.eq(true);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boLanguagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesPage.pageTitle);
    });
  });

  describe('Update Language', async () => {
    it(`should filter language by name '${createLanguageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await boLanguagesPage.filterTable(page, 'input', 'name', createLanguageData.name);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await boLanguagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(createLanguageData.name);
    });

    it('should go to edit language page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditLanguagePage', baseContext);

      await boLanguagesPage.goToEditLanguage(page, 1);

      const pageTitle = await boLanguagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesCreatePage.pageEditTitle);
    });

    it('should edit language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editLanguage', baseContext);

      const textResult = await boLanguagesCreatePage.createEditLanguage(page, editLanguageData);
      expect(textResult).to.to.contains(boLanguagesPage.successfulUpdateMessage);

      const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages + 1);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo2', baseContext);

      // View my shop and get the new tab
      page = await boLanguagesPage.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.eq(true);
    });

    it(`should check that '${editLanguageData.name}' does not exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedLanguageFO', baseContext);

      const isLanguageInFO = await foClassicHomePage.languageExists(page, editLanguageData.isoCode);
      expect(isLanguageInFO, `${editLanguageData.name} was found as a language in FO`).to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boLanguagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boLanguagesPage.pageTitle);
    });
  });

  describe('Delete Language', async () => {
    it(`should filter language by name '${editLanguageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await boLanguagesPage.filterTable(page, 'input', 'name', editLanguageData.name);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await boLanguagesPage.getNumberOfElementInGrid(page);
      expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'name');
      expect(textColumn).to.contains(editLanguageData.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

      const textResult = await boLanguagesPage.deleteLanguage(page, 1);
      expect(textResult).to.to.contains(boLanguagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
      expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages);
    });
  });
});
