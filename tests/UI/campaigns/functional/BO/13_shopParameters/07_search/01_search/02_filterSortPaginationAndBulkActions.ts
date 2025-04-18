import testContext from '@utils/testContext';
import {expect} from 'chai';
import {faker} from '@faker-js/faker';

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
  boSearchAliasPage,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_filterSortPaginationAndBulkActions';

/*
 * Create 19 aliases
 * Pagination
 * Filter table
 * Sort table
 * Enable status by bulk actions
 * Disable status by bulk actions
 * Delete the created aliases by bulk actions
 */
describe('BO - Shop Parameters - Search : Filter, sort, pagination and bulk actions', async () => {
  const numAliases: number = 20;
  const fakerWord: string = faker.lorem.word();

  let browserContext: BrowserContext;
  let page: Page;
  let numberOfSearch: number = 0;

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

  it('should go to \'Aliases\' tab', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAliasesTab', baseContext);

    await boSearchPage.goToAliasesPage(page);

    const pageTitle = await boSearchAliasPage.getPageTitle(page);
    expect(pageTitle).to.equals(boSearchAliasPage.pageTitle);
  });

  it('should reset all filters and get number of aliases in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfSearch = await boSearchAliasPage.resetAndGetNumberOfLines(page);
    expect(numberOfSearch).to.be.above(0);
  });

  // 1 - Create 20 aliases
  const creationTests: number[] = new Array(numAliases).fill(0, 0, numAliases);
  describe(`Create ${numAliases} aliases in BO`, async () => {
    creationTests.forEach((test: number, index: number) => {
      const aliasData: FakerSearchAlias = new FakerSearchAlias({
        search: `todelete${index}`,
        alias: `alias_${fakerWord}_${index}`,
      });

      it('should go to add new search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddAliasPage${index}`, baseContext);

        await boSearchAliasPage.goToAddNewAliasPage(page);

        const pageTitle = await boSearchAliasCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchAliasCreatePage.pageTitleCreate);
      });

      it(`should create alias n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAlias${index}`, baseContext);

        const textResult = await boSearchAliasCreatePage.setAlias(page, aliasData);
        expect(textResult).to.contains(boSearchPage.successfulCreationMessage);

        const numberOfElementAfterCreation = await boSearchAliasPage.getNumberOfElementInGrid(page);
        expect(numberOfElementAfterCreation).to.be.equal(numberOfSearch + 1 + index);
      });
    });
  });

  // 2 - Pagination aliases
  describe('Pagination', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await boSearchAliasPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boSearchAliasPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boSearchAliasPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await boSearchAliasPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 - Filter aliases
  describe('Filter aliases table', async () => {
    [
      {
        testIdentifier: 'filterAliases',
        filterType: 'input',
        filterBy: 'search',
        filterValue: 'todelete5',
      },
      {
        testIdentifier: 'filterSearch',
        filterType: 'input',
        filterBy: 'alias',
        filterValue: 'bloose',
      },
    ].forEach((test: {
      testIdentifier: string
      filterType: string
      filterBy: string
      filterValue: string
    }) => {
      it(`should filter by ${test.filterBy} '${test.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        await boSearchAliasPage.filterTable(
          page,
          test.filterType,
          test.filterBy,
          test.filterValue,
        );

        const numberOfGroupsAfterFilter = await boSearchAliasPage.getNumberOfElementInGrid(page);
        expect(numberOfGroupsAfterFilter).to.be.gt(0).and.at.most(numberOfSearch);

        for (let row = 1; row <= numberOfGroupsAfterFilter; row++) {
          const textColumn = await boSearchAliasPage.getTextColumn(page, row, test.filterBy);
          expect(textColumn).to.contains(test.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.testIdentifier}Reset`, baseContext);

        const numberOfGroupsAfterReset = await boSearchAliasPage.resetAndGetNumberOfLines(page);
        expect(numberOfGroupsAfterReset).to.equal(numberOfSearch + numAliases);
      });
    });
  });

  // 4 - Sort aliases
  describe('Sort aliases table', async () => {
    [
      {
        testIdentifier: 'sortBySearchAsc',
        sortBy: 'search',
        sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortBySearchDesc',
        sortBy: 'search',
        sortDirection: 'desc',
      },
    ].forEach((test: {testIdentifier: string, sortBy: string, sortDirection: string}) => {
      it(`should sort by '${test.sortBy}' '${test.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        const nonSortedTable = await boSearchAliasPage.getAllRowsColumnContent(page, test.sortBy);
        await boSearchAliasPage.sortTable(page, test.sortBy, test.sortDirection);

        const sortedTable = await boSearchAliasPage.getAllRowsColumnContent(page, test.sortBy);
        const expectedResult = await utilsCore.sortArray(nonSortedTable);

        if (test.sortDirection === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 5 - Delete aliases by bulk actions
  describe('Delete aliases by bulk actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boSearchAliasPage.resetFilter(page);
      await boSearchAliasPage.filterTable(page, 'input', 'search', 'todelete');

      const textAlias = await boSearchAliasPage.getTextColumn(page, 1, 'search');
      expect(textAlias).to.contains('todelete');
    });

    it('should delete aliases', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAliases', baseContext);

      const textResult = await boSearchAliasPage.bulkDeleteAliases(page);
      expect(textResult).to.contains(boSearchAliasPage.successfulMultiDeleteMessage);

      const numberOfSearchAfterDelete = await boSearchAliasPage.resetAndGetNumberOfLines(page);
      expect(numberOfSearchAfterDelete).to.be.equal(numberOfSearch);
    });
  });
});
