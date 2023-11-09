// Import utils
import helper from '@utils/helpers';
import basicHelper from '@utils/basicHelper';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import titlesPage from '@pages/BO/shopParameters/customerSettings/titles';
import addTitlePage from '@pages/BO/shopParameters/customerSettings/titles/add';

// Import data
import Titles from '@data/demo/titles';
import TitleData from '@data/faker/title';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_filterSortAndPaginationTitles';

describe('BO _ Shop Parameters - Customer Settings : Filter, sort and pagination titles', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.generateImage('image.png');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile('image.png');
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.customerSettingsLink,
    );
    await customerSettingsPage.closeSfToolBar(page);

    const pageTitle = await customerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await customerSettingsPage.goToTitlesPage(page);

    const pageTitle = await titlesPage.getPageTitle(page);
    expect(pageTitle).to.contains(titlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await titlesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTitles).to.be.above(0);
  });

  // 1 - Filter
  describe('Filter titles', async () => {
    const tests = [
      {
        args: {
          testIdentifier: 'filterId', filterType: 'input', filterBy: 'id_gender', filterValue: Titles.Mrs.id.toString(),
        },
      },
      {
        args: {
          testIdentifier: 'filterName', filterType: 'input', filterBy: 'name', filterValue: Titles.Mrs.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterGender', filterType: 'select', filterBy: 'type', filterValue: Titles.Mrs.gender,
        },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await titlesPage.filterTitles(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfTitlesAfterFilter = await titlesPage.getNumberOfElementInGrid(page);
        expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

        const textColumn = await titlesPage.getTextColumn(page, 1, test.args.filterBy);
        expect(textColumn).to.contains(test.args.filterValue);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfTitlesAfterReset = await titlesPage.resetAndGetNumberOfLines(page);
        expect(numberOfTitlesAfterReset).to.equal(numberOfTitles);
      });
    });
  });

  // 2 - Sort
  describe('Sort titles', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_gender', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySocialTitleAsc', sortBy: 'name', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySocialTitleDesc', sortBy: 'name', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_gender', sortDirection: 'asc', isFloat: true,
          },
      },
    ].forEach((test: { args: { testIdentifier: string, sortBy: string, sortDirection: string, isFloat?: boolean } }) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await titlesPage.getAllRowsColumnContent(page, test.args.sortBy);
        await titlesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await titlesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 3 - Create 9 titles
  describe('Create 9 titles', async () => {
    const creationTests: number[] = new Array(9).fill(0, 0, 9);
    creationTests.forEach((value: number, index: number) => {
      const titleToCreate: TitleData = new TitleData({name: `toSortAndPaginate${index}`, imageName: 'image.png'});

      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewTitle${index}`, baseContext);

        await titlesPage.goToAddNewTitle(page);

        const pageTitle = await addTitlePage.getPageTitle(page);
        expect(pageTitle).to.eq(addTitlePage.pageTitleCreate);
      });

      it('should create title and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createTitle${index}`, baseContext);

        const textResult = await addTitlePage.createEditTitle(page, titleToCreate);
        expect(textResult).to.contains(titlesPage.successfulCreationMessage);

        const numberOfTitlesAfterCreation = await titlesPage.getNumberOfElementInGrid(page);
        expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + index + 1);
      });
    });
  });

  // 4 - Pagination
  describe('Pagination titles', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await titlesPage.selectPaginationLimit(page, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await titlesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await titlesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await titlesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 5 - Bulk delete
  describe('Bulk delete titles', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await titlesPage.filterTitles(page, 'input', 'name', 'toSortAndPaginate');

      const numberOfTitlesAfterFilter = await titlesPage.getNumberOfElementInGrid(page);
      expect(numberOfTitlesAfterFilter).to.eq(9);

      for (let i = 1; i <= numberOfTitlesAfterFilter; i++) {
        const textColumn = await titlesPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('toSortAndPaginate');
      }
    });

    it('should delete titles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTitles', baseContext);

      const deleteTextResult = await titlesPage.bulkDeleteTitles(page);
      expect(deleteTextResult).to.be.contains(titlesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfTitlesAfterReset = await titlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterReset).to.be.equal(numberOfTitles);
    });
  });
});
