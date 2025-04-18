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
  dataLanguages,
  type Page,
  utilsCore,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_languages_sortAndPagination';

describe('BO - International - Languages : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfLanguages: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(dataLanguages.croatian.flag),
      utilsFile.generateImage(dataLanguages.croatian.noPicture),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      utilsFile.deleteFile(dataLanguages.croatian.flag),
      utilsFile.deleteFile(dataLanguages.croatian.noPicture),
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
      {args: {testIdentifier: 'sortByLocaleAsc', sortBy: 'locale', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByLocaleDesc', sortBy: 'locale', sortDirection: 'desc'}},
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
        const nonSortedTable = await boLanguagesPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boLanguagesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        // Get sorted elements
        const sortedTable = await boLanguagesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 2 - Pagination
  describe('Pagination of Languages table', async () => {
    const tests = [
      {args: {languageData: dataLanguages.spanish}},
      {args: {languageData: dataLanguages.deutsch}},
      {args: {languageData: dataLanguages.turkish}},
      {args: {languageData: dataLanguages.spanishAR}},
      {args: {languageData: dataLanguages.dutch}},
      {args: {languageData: dataLanguages.portuguese}},
      {args: {languageData: dataLanguages.croatian}},
      {args: {languageData: dataLanguages.simplifiedChinese}},
      {args: {languageData: dataLanguages.traditionalChinese}},
    ];
    describe('Create 9 Languages', async () => {
      tests.forEach((test, index: number) => {
        it('should go to add new language page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToAddNewLanguagePage${index}`, baseContext);

          await boLanguagesPage.goToAddNewLanguage(page);

          const pageTitle = await boLanguagesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boLanguagesCreatePage.pageTitle);
        });

        it(`Create language n°${index + 1} in BO`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createNewLanguage${index}`, baseContext);

          const textResult = await boLanguagesCreatePage.createEditLanguage(page, test.args.languageData);
          expect(textResult).to.to.contains(boLanguagesPage.successfulCreationMessage);

          const numberOfLanguagesAfterCreation = await boLanguagesPage.getNumberOfElementInGrid(page);
          expect(numberOfLanguagesAfterCreation).to.be.equal(numberOfLanguages + 1 + index);
        });
      });
    });

    describe('Pagination next and previous', async () => {
      it('should change the item number to 10 per page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

        const paginationNumber = await boLanguagesPage.selectPaginationLimit(page, 10);
        expect(paginationNumber).to.contains('(page 1 / 2)');
      });

      it('should click on next', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

        const paginationNumber = await boLanguagesPage.paginationNext(page);
        expect(paginationNumber).to.contains('(page 2 / 2)');
      });

      it('should click on previous', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

        const paginationNumber = await boLanguagesPage.paginationPrevious(page);
        expect(paginationNumber).to.contains('(page 1 / 2)');
      });

      it('should change the item number to 50 per page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

        const paginationNumber = await boLanguagesPage.selectPaginationLimit(page, 50);
        expect(paginationNumber).to.contains('(page 1 / 1)');
      });
    });

    describe('Delete created Languages', async () => {
      tests.forEach((test, index: number) => {
        it(`should filter language by name '${test.args.languageData.name}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

          // Filter
          await boLanguagesPage.filterTable(page, 'input', 'name', test.args.languageData.name);

          // Check number of languages
          const numberOfLanguagesAfterFilter = await boLanguagesPage.getNumberOfElementInGrid(page);
          expect(numberOfLanguagesAfterFilter).to.be.at.least(1);

          const textColumn = await boLanguagesPage.getTextColumnFromTable(page, 1, 'name');
          expect(textColumn).to.contains(test.args.languageData.name);
        });

        it('should delete language', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `deleteLanguage${index}`, baseContext);

          const textResult = await boLanguagesPage.deleteLanguage(page, 1);
          expect(textResult).to.to.contains(boLanguagesPage.successfulDeleteMessage);
        });

        it('should reset all filters', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `resetAfterDelete${index}`, baseContext);

          const numberOfLanguagesAfterReset = await boLanguagesPage.resetAndGetNumberOfLines(page);
          expect(numberOfLanguagesAfterReset).to.be.equal(numberOfLanguages + 8 - index);
        });
      });
    });
  });
});
