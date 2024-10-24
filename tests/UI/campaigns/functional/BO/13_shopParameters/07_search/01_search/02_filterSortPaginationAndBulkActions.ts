// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  boSearchAliasCreatePage,
  type BrowserContext,
  FakerSearchAlias,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_filterSortPaginationAndBulkActions';

/*
Create 19 aliases
Pagination
Filter table
Sort table
Enable status by bulk actions
Disable status by bulk actions
Delete the created aliases by bulk actions
 */
describe('BO - Shop Parameters - Search : Filter, sort, pagination and bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearch: number = 0;

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

  it('should go to \'Shop Parameters > Search\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.searchLink,
    );

    const pageTitle = await boSearchPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSearchPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await boSearchPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create 19 aliases
  const creationTests: number[] = new Array(19).fill(0, 0, 19);
  describe('Create 19 aliases in BO', async () => {
    creationTests.forEach((test: number, index: number) => {
      const aliasData: FakerSearchAlias = new FakerSearchAlias({alias: `todelete${index}`});

      it('should go to add new search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAliasPage${index}`, baseContext);

        await boSearchPage.goToAddNewAliasPage(page);

        const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleCreate);
      });

      it(`should create alias n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAlias${index}`, baseContext);

        const textResult = await boSearchAliasCreatePage.setAlias(page, aliasData);
        expect(textResult).to.contains(boSearchPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await boSearchPage.getNumberOfElementInGrid(page);
        expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1 + index);
      });
    });
  });

  // 2 - Pagination aliases
  describe('Pagination', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boSearchPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boSearchPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boSearchPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boSearchPage.selectPaginationLimit(page, 50);
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

        await boSearchPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfGroupsAfterFilter = await boSearchPage.getNumberOfElementInGrid(page);
        expect(numberOfGroupsAfterFilter).to.be.at.most(numberOfSearch);

        for (let row = 1; row <= numberOfGroupsAfterFilter; row++) {
          const textColumn = await boSearchPage.getTextColumn(page, row, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfGroupsAfterReset = await boSearchPage.resetAndGetNumberOfLines(page);
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

        const nonSortedTable = await boSearchPage.getAllRowsColumnContent(page, test.args.sortBy);
        await boSearchPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boSearchPage.getAllRowsColumnContent(page, test.args.sortBy);
        const expectedResult = await utilsCore.sortArray(nonSortedTable);

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

      await boSearchPage.resetFilter(page);
      await boSearchPage.filterTable(page, 'input', 'alias', 'todelete');

      const textAlias = await boSearchPage.getTextColumn(page, 1, 'alias');
      expect(textAlias).to.contains('todelete');
    });

    [
      {args: {action: 'disable', value: false}},
      {args: {action: 'enable', value: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Status`, baseContext);

        const textResult = await boSearchPage.bulkSetStatus(page, test.args.value);
        expect(textResult).to.contains(boSearchPage.successfulUpdateStatusMessage);

        const numberOfElementInGrid = await boSearchPage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementInGrid; i++) {
          const textColumn = await boSearchPage.getStatus(page, i);
          expect(textColumn).to.equal(test.args.value);
        }
      });
    });
  });

  // 6 - Delete aliases by bulk actions
  describe('Delete aliases by bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boSearchPage.resetFilter(page);
      await boSearchPage.filterTable(page, 'input', 'alias', 'todelete');

      const textAlias = await boSearchPage.getTextColumn(page, 1, 'alias');
      expect(textAlias).to.contains('todelete');
    });

    it('should delete aliases', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAliases', baseContext);

      const textResult = await boSearchPage.bulkDeleteAliases(page);
      expect(textResult).to.contains(boSearchPage.successfulMultiDeleteMessage);

      const numberOfSearchAfterDelete = await boSearchPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
