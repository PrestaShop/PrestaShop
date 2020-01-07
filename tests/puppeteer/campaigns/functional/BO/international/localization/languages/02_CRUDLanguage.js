require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const LocalizationPage = require('@pages/BO/international/localization');
const LanguagesPage = require('@pages/BO/international/languages');
const AddLanguagePage = require('@pages/BO/international/languages/add');
const FOBasePage = require('@pages/FO/FObasePage');
// Importing data
const LanguageFaker = require('@data/faker/language');

let browser;
let page;
const createLanguageData = new LanguageFaker({isoCode: 'de'});
const editLanguageData = new LanguageFaker({isoCode: 'nl', status: false});
let numberOfLanguages = 0;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    localizationPage: new LocalizationPage(page),
    languagesPage: new LanguagesPage(page),
    addLanguagePage: new AddLanguagePage(page),
    foBasePage: new FOBasePage(page),
  };
};

/*
Create enabled language
Verify that language exist in FO
Update language (and disable it)
Verify that language do not exist in FO
Delete language
 */
describe('CRUD language', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await Promise.all([
      files.deleteFile(createLanguageData.flag),
      files.deleteFile(createLanguageData.noPicture),
      files.deleteFile(editLanguageData.flag),
      files.deleteFile(editLanguageData.noPicture),
    ]);
  });

  // Login into BO and go to languages page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to languages page', async function () {
    await this.pageObjects.localizationPage.goToSubTabLanguages();
    const pageTitle = await this.pageObjects.languagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    numberOfLanguages = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
    await expect(numberOfLanguages).to.be.above(0);
  });

  describe('Create Language', async () => {
    it('should go to add new language page', async function () {
      await this.pageObjects.languagesPage.goToAddNewLanguage();
      const pageTitle = await this.pageObjects.addLanguagePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addLanguagePage.pageTitle);
    });

    it('should create new language', async function () {
      const textResult = await this.pageObjects.addLanguagePage.createEditLanguage(createLanguageData);
      await expect(textResult).to.to.contains(this.pageObjects.languagesPage.successfulCreationMessage);
      const numberOfLanguagesAfterCreation = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + 1);
    });

    it(`should go to FO and check that '${createLanguageData.name}' exist`, async function () {
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      const isLanguageInFO = await this.pageObjects.foBasePage.languageExist(createLanguageData.isoCode);
      await expect(isLanguageInFO, `${createLanguageData.name} was not found as a language in FO`).to.be.true;
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });

  describe('Update Language', async () => {
    it(`should filter language by name '${createLanguageData.name}'`, async function () {
      await this.pageObjects.languagesPage.filterTable('input', 'name', createLanguageData.name);
      const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);
      const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(1, 'name');
      await expect(textColumn).to.contains(createLanguageData.name);
    });

    it('should go to edit language page', async function () {
      await this.pageObjects.languagesPage.goToEditLanguage(1);
      const pageTitle = await this.pageObjects.addLanguagePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addLanguagePage.pageEditTitle);
    });

    it('should edit language', async function () {
      const textResult = await this.pageObjects.addLanguagePage.createEditLanguage(editLanguageData);
      await expect(textResult).to.to.contains(this.pageObjects.languagesPage.successfulUpdateMessage);
      const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
      await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages + 1);
    });

    it(`should go to FO and check that '${editLanguageData.name}' do not exist`, async function () {
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      const isLanguageInFO = await this.pageObjects.foBasePage.languageExist(editLanguageData.isoCode);
      await expect(isLanguageInFO, `${editLanguageData.name} was found as a language in FO`).to.be.false;
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });

  describe('Delete Language', async () => {
    it(`should filter language by name '${editLanguageData.name}'`, async function () {
      await this.pageObjects.languagesPage.filterTable('input', 'name', editLanguageData.name);
      const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);
      const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(1, 'name');
      await expect(textColumn).to.contains(editLanguageData.name);
    });

    it('should delete language', async function () {
      const textResult = await this.pageObjects.languagesPage.deleteLanguage(1);
      await expect(textResult).to.to.contains(this.pageObjects.languagesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfLanguagesAfterReset = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
      await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages);
    });
  });
});
