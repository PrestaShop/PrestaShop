// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import basicHelper from '@utils/basicHelper';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import stocksPage from '@pages/BO/catalog/stocks';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_sortAndPagination';

describe('BO - Catalog - Stocks : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

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

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.stocksLink,
    );
    await stocksPage.closeSfToolBar(page);

    const pageTitle = await stocksPage.getPageTitle(page);
    expect(pageTitle).to.contains(stocksPage.pageTitle);
  });

  it('should reset filter and get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await stocksPage.resetFilter(page);
    expect(numberOfProducts).to.be.above(0);
  });

  describe('Pagination next and previous', async () => {
    it('should go to the next page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNextPage', baseContext);

      const pageNumber = await stocksPage.paginateTo(page, 2);
      expect(pageNumber).to.eq(2);
    });

    it('should go back to the first page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFirstPage', baseContext);

      const pageNumber = await stocksPage.paginateTo(page, 1);
      expect(pageNumber).to.eq(1);
    });
  });

  describe('Sort stock table', async () => {
    const sortTests = [
      {args: {testIdentifier: 'sortByIdDesc', sortBy: 'product_id', sortDirection: 'desc', isNumber: true}},
      {args: {testIdentifier: 'sortByIdAsc', sortBy: 'product_id', sortDirection: 'asc', isNumber: true}},
      {args: {testIdentifier: 'sortByProductNameAsc', sortBy: 'product_name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByProductNameDesc', sortBy: 'product_name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByReferenceAsc', sortBy: 'reference', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByReferenceDesc', sortBy: 'reference', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortBySupplierAsc', sortBy: 'supplier', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortBySupplierDesc', sortBy: 'supplier', sortDirection: 'desc'}},
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await stocksPage.getAllRowsColumnContent(page, test.args.sortBy);

        await stocksPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await stocksPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isNumber) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseInt(text, 10));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseInt(text, 10));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });
});
