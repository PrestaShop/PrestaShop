// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boStockPage,
  type BrowserContext,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_stocks_sortAndPagination';

describe('BO - Catalog - Stocks : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

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

  it('should go to \'Catalog > Stocks\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.stocksLink,
    );
    await boStockPage.closeSfToolBar(page);

    const pageTitle = await boStockPage.getPageTitle(page);
    expect(pageTitle).to.contains(boStockPage.pageTitle);
  });

  it('should reset filter and get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProductsInList', baseContext);

    numberOfProducts = await boStockPage.resetFilter(page);
    expect(numberOfProducts).to.be.above(0);
  });

  describe('Pagination next and previous', async () => {
    it('should go to the next page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNextPage', baseContext);

      const pageNumber = await boStockPage.paginateTo(page, 2);
      expect(pageNumber).to.eq(2);
    });

    it('should go back to the first page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFirstPage', baseContext);

      const pageNumber = await boStockPage.paginateTo(page, 1);
      expect(pageNumber).to.eq(1);
    });
  });

  describe('Sort stock table', async () => {
    const sortTests = [
      {
        args:
          {
            testIdentifier: 'sortByIdDesc',
            sortBy: 'product_id',
            sortDirection: 'desc',
            isNumber: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdAsc',
            sortBy: 'product_id',
            sortDirection: 'asc',
            isNumber: true,
          },
      },
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

        const nonSortedTable = await boStockPage.getAllRowsColumnContent(page, test.args.sortBy);

        await boStockPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boStockPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isNumber) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseInt(text, 10));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseInt(text, 10));

          const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

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
