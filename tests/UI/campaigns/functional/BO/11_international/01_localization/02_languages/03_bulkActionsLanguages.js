require('module-alias/register');

const {expect} = require('chai');

const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');
const addLanguagePage = require('@pages/BO/international/languages/add');

// Import data
const LanguageFaker = require('@data/faker/language');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_bulkActionsLanguages';


let browserContext;
let page;
const firstLanguageData = new LanguageFaker({name: 'languageToDelete1', isoCode: 'fi'});
const secondLanguageData = new LanguageFaker({name: 'languageToDelete2', isoCode: 'ca'});
let numberOfLanguages = 0;

/*
Create 2 languages
Enable them with bulk actions
Disable them with bulk actions
Delete them with bulk actions
 */
describe('BO - International - Languages : Bulk disable, enable and delete languages', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    await Promise.all([
      files.generateImage(firstLanguageData.flag),
      files.generateImage(firstLanguageData.noPicture),
      files.generateImage(secondLanguageData.flag),
      files.generateImage(secondLanguageData.noPicture),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await Promise.all([
      files.deleteFile(firstLanguageData.flag),
      files.deleteFile(firstLanguageData.noPicture),
      files.deleteFile(secondLanguageData.flag),
      files.deleteFile(secondLanguageData.noPicture),
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

  describe('Create 2 Languages', async () => {
    [firstLanguageData, secondLanguageData].forEach((languageToCreate, index) => {
      it('should go to add new language page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewLanguage${index + 1}`, baseContext);

        await languagesPage.goToAddNewLanguage(page);
        const pageTitle = await addLanguagePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addLanguagePage.pageTitle);
      });

      it('should create new language', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createLanguage${index + 1}`, baseContext);

        const textResult = await addLanguagePage.createEditLanguage(page, languageToCreate);
        await expect(textResult).to.to.contains(languagesPage.successfulCreationMessage);

        const numberOfLanguagesAfterCreation = await languagesPage.getNumberOfElementInGrid(page);
        await expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + index + 1);
      });
    });
  });

  describe('Enable, disable and delete with bulk actions', async () => {
    const tests = [
      {args: {action: 'disable', toEnable: false}},
      {args: {action: 'enable', toEnable: true}},
    ];

    it('should filter language by name \'languageToDelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterLanguageToChangeStatus', baseContext);

      // Filter
      await languagesPage.filterTable(page, 'input', 'name', 'languageToDelete');

      const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
      await expect(numberOfLanguagesAfterFilter).to.be.at.least(2);
    });

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}`, baseContext);

        const disableTextResult = await languagesPage.bulkSetStatus(
          page,
          test.args.toEnable,
        );

        await expect(disableTextResult).to.be.equal(languagesPage.successfulUpdateStatusMessage);

        // Check that element in grid are disabled
        const numberOfLanguagesInGrid = await languagesPage.getNumberOfElementInGrid(page);
        await expect(numberOfLanguagesInGrid).to.be.at.most(numberOfLanguages);

        for (let i = 1; i <= numberOfLanguagesInGrid; i++) {
          const textColumn = await languagesPage.getStatus(page, i);
          await expect(textColumn).to.equal(test.args.toEnable);
        }
      });
    });

    it('should delete with bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await languagesPage.deleteWithBulkActions(page);
      await expect(deleteTextResult).to.be.equal(languagesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfLanguagesAfterDelete = await languagesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfLanguagesAfterDelete).to.be.equal(numberOfLanguages);
    });
  });
});
