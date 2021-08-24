require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');
const addLanguagePage = require('@pages/BO/international/languages/add');
const foHomePage = require('@pages/FO/home');

// Import data
const LanguageFaker = require('@data/faker/language');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_CRUDLanguages';

let browserContext;
let page;

const createLanguageData = new LanguageFaker({isoCode: 'de'});
const editLanguageData = new LanguageFaker({isoCode: 'nl', enabled: false});
let numberOfLanguages = 0;

/*
Create enabled language
Verify that language exist in FO
Update language (and disable it)
Verify that language do not exist in FO
Delete language
 */
describe('BO - International - Languages : CRUD language', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    await Promise.all([
      files.generateImage(createLanguageData.flag),
      files.generateImage(createLanguageData.noPicture),
      files.generateImage(editLanguageData.flag),
      files.generateImage(editLanguageData.noPicture),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await Promise.all([
      files.deleteFile(createLanguageData.flag),
      files.deleteFile(createLanguageData.noPicture),
      files.deleteFile(editLanguageData.flag),
      files.deleteFile(editLanguageData.noPicture),
    ]);
  });

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

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfLanguages = await languagesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfLanguages).to.be.above(0);
  });

  describe('Create Language', async () => {
    it('should go to add new language page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewLanguages', baseContext);

      await languagesPage.goToAddNewLanguage(page);
      const pageTitle = await addLanguagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addLanguagePage.pageTitle);
    });

    it('should create new language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewLanguages', baseContext);

      const textResult = await addLanguagePage.createEditLanguage(page, createLanguageData);
      await expect(textResult).to.to.contains(languagesPage.successfulCreationMessage);

      const numberOfLanguagesAfterCreation = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + 1);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo1', baseContext);

      // View my shop and get the new tab
      page = await languagesPage.viewMyShop(page);

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it(`should check that '${createLanguageData.name}' exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedLanguageFO', baseContext);

      const isLanguageInFO = await foHomePage.languageExists(page, createLanguageData.isoCode);
      await expect(isLanguageInFO, `${createLanguageData.name} was not found as a language in FO`).to.be.true;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });
  });

  describe('Update Language', async () => {
    it(`should filter language by name '${createLanguageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      // Filter
      await languagesPage.filterTable(page, 'input', 'name', createLanguageData.name);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textColumn).to.contains(createLanguageData.name);
    });

    it('should go to edit language page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditLanguagePage', baseContext);

      await languagesPage.goToEditLanguage(page, 1);
      const pageTitle = await addLanguagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addLanguagePage.pageEditTitle);
    });

    it('should edit language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editLanguage', baseContext);

      const textResult = await addLanguagePage.createEditLanguage(page, editLanguageData);
      await expect(textResult).to.to.contains(languagesPage.successfulUpdateMessage);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages + 1);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo2', baseContext);

      // View my shop and get the new tab
      page = await languagesPage.viewMyShop(page);

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it(`should check that '${editLanguageData.name}' does not exist`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedLanguageFO', baseContext);

      const isLanguageInFO = await foHomePage.languageExists(page, editLanguageData.isoCode);
      await expect(isLanguageInFO, `${editLanguageData.name} was found as a language in FO`).to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

      page = await foHomePage.closePage(browserContext, page, 0);
      const pageTitle = await languagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(languagesPage.pageTitle);
    });
  });

  describe('Delete Language', async () => {
    it(`should filter language by name '${editLanguageData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      // Filter
      await languagesPage.filterTable(page, 'input', 'name', editLanguageData.name);

      // Check number of languages
      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

      const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
      await expect(textColumn).to.contains(editLanguageData.name);
    });

    it('should delete language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteLanguage', baseContext);

      const textResult = await languagesPage.deleteLanguage(page, 1);
      await expect(textResult).to.to.contains(languagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages);
    });
  });
});
