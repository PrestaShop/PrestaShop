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
// Importing data
const LanguageFaker = require('@data/faker/language');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_bulkActionsLanguages';

let browser;
let page;
const firstLanguageData = new LanguageFaker({name: 'languageToDelete1', isoCode: 'fi'});
const secondLanguageData = new LanguageFaker({name: 'languageToDelete2', isoCode: 'ca'});
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
  };
};

/*
Create 2 languages
Enable them with bulk actions
Disable them with bulk actions
Delete them with bulk actions
 */
describe('Disable, enable and delete with bulk actions languages', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await Promise.all([
      files.deleteFile(firstLanguageData.flag),
      files.deleteFile(firstLanguageData.noPicture),
      files.deleteFile(secondLanguageData.flag),
      files.deleteFile(secondLanguageData.noPicture),
    ]);
  });

  // Login into BO and go to languages page
  loginCommon.loginBO();

  it('should go to localization page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.internationalParentLink,
      this.pageObjects.boBasePage.localizationLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.localizationPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.localizationPage.pageTitle);
  });

  it('should go to languages page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLanguagesPage', baseContext);
    await this.pageObjects.localizationPage.goToSubTabLanguages();
    const pageTitle = await this.pageObjects.languagesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.languagesPage.pageTitle);
  });

  it('should reset all filters and get number of languages in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);
    numberOfLanguages = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
    await expect(numberOfLanguages).to.be.above(0);
  });

  describe('Create 2 Languages', async () => {
    [firstLanguageData, secondLanguageData].forEach((languageToCreate, index) => {
      it('should go to add new language page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewLanguage${index + 1}`, baseContext);
        await this.pageObjects.languagesPage.goToAddNewLanguage();
        const pageTitle = await this.pageObjects.addLanguagePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addLanguagePage.pageTitle);
      });
      it('should create new language', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createLanguage${index + 1}`, baseContext);
        const textResult = await this.pageObjects.addLanguagePage.createEditLanguage(languageToCreate);
        await expect(textResult).to.to.contains(this.pageObjects.languagesPage.successfulCreationMessage);
        const numberOfLanguagesAfterCreation = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
        await expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + index + 1);
      });
    });
  });

  describe('Enable, disable and delete with bulk actions', async () => {
    const tests = [
      {args: {action: 'disable', toEnable: false}, expected: 'clear'},
      {args: {action: 'enable', toEnable: true}, expected: 'check'},
    ];

    it('should filter language by name \'languageToDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterLanguageToChangeStatus', baseContext);
      await this.pageObjects.languagesPage.filterTable('input', 'name', 'languageToDelete');
      const numberOfLanguagesAfterFilter = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(2);
    });

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);
        const disableTextResult = await this.pageObjects.languagesPage.bulkEditEnabledColumn(
          test.args.toEnable,
        );
        await expect(disableTextResult).to.be.equal(this.pageObjects.languagesPage.successfulUpdateStatusMessage);
        // Check that element in grid are disabled
        const numberOfLanguagesInGrid = await this.pageObjects.languagesPage.getNumberOfElementInGrid();
        await expect(numberOfLanguagesInGrid).to.be.at.most(numberOfLanguages);
        for (let i = 1; i <= numberOfLanguagesInGrid; i++) {
          const textColumn = await this.pageObjects.languagesPage.getTextColumnFromTable(i, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });

    it('should delete with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);
      const deleteTextResult = await this.pageObjects.languagesPage.deleteWithBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.languagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);
      const numberOfLanguagesAfterDelete = await this.pageObjects.languagesPage.resetAndGetNumberOfLines();
      await expect(numberOfLanguagesAfterDelete).to.be.equal(numberOfLanguages);
    });
  });
});
