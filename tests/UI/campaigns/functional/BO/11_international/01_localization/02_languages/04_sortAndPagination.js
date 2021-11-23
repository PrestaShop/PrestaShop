require('module-alias/register');

const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const files = require('@utils/files');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const localizationPage = require('@pages/BO/international/localization');
const languagesPage = require('@pages/BO/international/languages');
const addLanguagePage = require('@pages/BO/international/languages/add');

// Import pages
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_international_localization_languages_sortAndPagination';

// Import data
const {Languages} = require('@data/demo/languages');

let browserContext;
let page;
let numberOfLanguages = 0;

describe('BO - International - Languages : Sort and pagination', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    await Promise.all([
      files.generateImage(Languages.croatian.flag),
      files.generateImage(Languages.croatian.noPicture),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(Languages.croatian.flag),
      files.deleteFile(Languages.croatian.noPicture),
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

  // 1 - Sort table
  describe('Sort Languages table', async () => {
    [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_lang', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByIsoCodeAsc', sortBy: 'iso_code', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByIsoCodeDesc', sortBy: 'iso_code', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByLanguageCodeAsc', sortBy: 'language_code', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByLanguageCodeDesc', sortBy: 'language_code', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByDateFormatLiteAsc', sortBy: 'date_format_lite', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByDateFormatLiteDesc', sortBy: 'date_format_lite', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByDateFormatFullAsc', sortBy: 'date_format_full', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByDateFormatFullDesc', sortBy: 'date_format_full', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_lang', sortDirection: 'asc', isFloat: true,
        },
      },
    ].forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' And check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        // Get non sorted elements
        let nonSortedTable = await languagesPage.getAllRowsColumnContent(page, test.args.sortBy);
        await languagesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        // Get sorted elements
        let sortedTable = await languagesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        // Sort non sorted array
        const expectedResult = await languagesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 2 - Pagination
  describe('Pagination of Languages table', async () => {
    const tests = [
      {args: {languageData: Languages.spanish}},
      {args: {languageData: Languages.deutsch}},
      {args: {languageData: Languages.turkish}},
      {args: {languageData: Languages.spanishAR}},
      {args: {languageData: Languages.dutch}},
      {args: {languageData: Languages.portuguese}},
      {args: {languageData: Languages.croatian}},
      {args: {languageData: Languages.simplifiedChinese}},
      {args: {languageData: Languages.traditionalChinese}},
    ];
    describe('Create 9 Languages', async () => {
      tests.forEach((test, index) => {
        it('should go to add new language page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddNewLanguagePage${index}`, baseContext);

          await languagesPage.goToAddNewLanguage(page);
          const pageTitle = await addLanguagePage.getPageTitle(page);
          await expect(pageTitle).to.contains(addLanguagePage.pageTitle);
        });

        it(`Create language n°${index + 1} in BO`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createNewLanguage${index}`, baseContext);

          const textResult = await addLanguagePage.createEditLanguage(page, test.args.languageData);
          await expect(textResult).to.to.contains(languagesPage.successfulCreationMessage);

          const numberOfLanguagesAfterCreation = await languagesPage.getNumberOfElementInGrid(page);
          await expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + 1 + index);
        });
      });
    });

    describe('Pagination next and previous', async () => {
      it('should change the item number to 10 per page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

        const paginationNumber = await languagesPage.selectPaginationLimit(page, '10');
        expect(paginationNumber).to.contains('(page 1 / 2)');
      });

      it('should click on next', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

        const paginationNumber = await languagesPage.paginationNext(page);
        expect(paginationNumber).to.contains('(page 2 / 2)');
      });

      it('should click on previous', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

        const paginationNumber = await languagesPage.paginationPrevious(page);
        expect(paginationNumber).to.contains('(page 1 / 2)');
      });

      it('should change the item number to 50 per page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

        const paginationNumber = await languagesPage.selectPaginationLimit(page, '50');
        expect(paginationNumber).to.contains('(page 1 / 1)');
      });
    });

    describe('Delete created Languages', async () => {
      tests.forEach((test, index) => {
        it(`should filter language by name '${test.args.languageData.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

          // Filter
          await languagesPage.filterTable(page, 'input', 'name', test.args.languageData.name);

          // Check number of languages
          const numberOfLanguagesAfterFilter = await languagesPage.getNumberOfElementInGrid(page);
          await expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

          const textColumn = await languagesPage.getTextColumnFromTable(page, 1, 'name');
          await expect(textColumn).to.contains(test.args.languageData.name);
        });

        it('should delete language', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `deleteLanguage${index}`, baseContext);

          const textResult = await languagesPage.deleteLanguage(page, 1);
          await expect(textResult).to.to.contains(languagesPage.successfulDeleteMessage);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `resetAfterDelete${index}`, baseContext);

          const numberOfLanguagesAfterReset = await languagesPage.resetAndGetNumberOfLines(page);
          await expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages + 8 - index);
        });
      });
    });
  });
});
