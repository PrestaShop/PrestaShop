// Import utils
import helper from '@utils/helpers';
import basicHelper from '@utils/basicHelper';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import searchPage from '@pages/BO/shopParameters/search';
import addSearchPage from '@pages/BO/shopParameters/search/add';

// Import data
import SearchAliasData from '@data/faker/search';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_search_search_filterSortPaginationAndBulkActions';

/*
Create 19 aliases
Pagination
Filter table
Sort table
Enable status by bulk actions
Disable status by bulk actions
Delete th created aliases by bulk actions
 */
describe('BO - Shop Parameters - Search : Filter, sort, pagination and bulk actions search', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearch: number = 0;

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

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.searchLink,
    );

    const pageTitle = await searchPage.getPageTitle(page);
    expect(pageTitle).to.contains(searchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await searchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create 19 aliases
  const creationTests: number[] = new Array(19).fill(0, 0, 19);
  describe('Create 19 aliases in BO', async () => {
    creationTests.forEach((test: number, index: number) => {
      const aliasData: SearchAliasData = new SearchAliasData({alias: `todelete${index}`});

      it('should go to add new search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAliasPage${index}`, baseContext);

        await searchPage.goToAddNewAliasPage(page);

        const pageTitle = await addSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(addSearchPage.pageTitleCreate);
      });

      it(`should create alias n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAlias${index}`, baseContext);

        const textResult = await addSearchPage.setAlias(page, aliasData);
        expect(textResult).to.contains(searchPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await searchPage.getNumberOfElementInGrid(page);
        expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1 + index);
      });
    });
  });

  // 2 - Pagination aliases
  describe('Pagination', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await searchPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await searchPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await searchPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await searchPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 - Filter aliases
  describe('Filter aliases table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterAliases',
            filterType: 'input',
            filterBy: 'alias',
            filterValue: 'todelete5',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSearch',
            filterType: 'input',
            filterBy: 'search',
            filterValue: 'blouse',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterStatus',
            filterType: 'select',
            filterBy: 'active',
            filterValue: 'Yes',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await searchPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfGroupsAfterFilter = await searchPage.getNumberOfElementInGrid(page);
        expect(numberOfGroupsAfterFilter).to.be.at.most(numberOfSearch);

        for (let row = 1; row <= numberOfGroupsAfterFilter; row++) {
          const textColumn = await searchPage.getTextColumn(page, row, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfGroupsAfterReset = await searchPage.resetAndGetNumberOfLines(page);
        expect(numberOfGroupsAfterReset).to.equal(numberOfSearch + 19);
      });
    });
  });

  // 4 - Sort aliases
  describe('Sort aliases table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'sortByAliasesAsc', sortBy: 'alias', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByAliasesDesc', sortBy: 'alias', sortDirection: 'desc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySearchAsc', sortBy: 'search', sortDirection: 'asc',
          },
      },
      {
        args:
          {
            testIdentifier: 'sortBySearchDesc', sortBy: 'search', sortDirection: 'desc',
          },
      },
    ].forEach((test: { args: { testIdentifier: string, sortBy: string, sortDirection: string} }) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await searchPage.getAllRowsColumnContent(page, test.args.sortBy);
        await searchPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await searchPage.getAllRowsColumnContent(page, test.args.sortBy);
        const expectedResult = await basicHelper.sortArray(nonSortedTable);

        if (test.args.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 5 - Enable/Disable aliases by bulk actions
  describe('Enable/Disable the status by bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToEnableDisable', baseContext);

      await searchPage.resetFilter(page);
      await searchPage.filterTable(page, 'input', 'alias', 'todelete');

      const textAlias = await searchPage.getTextColumn(page, 1, 'alias');
      expect(textAlias).to.contains('todelete');
    });

    [
      {args: {action: 'disable', value: false}},
      {args: {action: 'enable', value: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Status`, baseContext);

        const textResult = await searchPage.bulkSetStatus(page, test.args.value);
        expect(textResult).to.contains(searchPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await searchPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await searchPage.getStatus(page, i);
          expect(textColumn).to.equal(test.args.value);
        }
      });
    });
  });

  // 6 - Delete aliases by bulk actions
  describe('Delete aliases by bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await searchPage.resetFilter(page);
      await searchPage.filterTable(page, 'input', 'alias', 'todelete');

      const textAlias = await searchPage.getTextColumn(page, 1, 'alias');
      expect(textAlias).to.contains('todelete');
    });

    it('should delete aliases', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAliases', baseContext);

      const textResult = await searchPage.bulkDeleteAliases(page);
      expect(textResult).to.contains(searchPage.successfulMultiDeleteMessage);

      const numberOfSearchAfterDelete = await searchPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
