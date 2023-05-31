// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import stocksPage from '@pages/BO/catalog/stocks';
import dashboardPage from '@pages/BO/dashboard';
import movementsPage from '@pages/BO/catalog/stocks/movements';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import basicHelper from "@utils/basicHelper";

const baseContext: string = 'functional_BO_catalog_stocks_movements_sortAndPagination';

describe('BO - Catalog - Movements : Sort and pagination', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  describe('PRE-TEST: Bulk edit the quantity of products in the first page of stocks table', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );
      await stocksPage.closeSfToolBar(page);

      const pageTitle = await stocksPage.getPageTitle(page);
      await expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    /*it('should bulk edit quantity of all products in the first page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkEditQuantity', baseContext);

      // Update quantity and check successful message
      const updateMessage = await stocksPage.bulkEditQuantityWithInput(page, 301);
      await expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
    });*/
  });

  describe('Sort movements table', async () => {
    it('should go to Movements page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPage', baseContext);

      await stocksPage.goToSubTabMovements(page);

      const pageTitle = await movementsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(movementsPage.pageTitle);
    });

    const sortTests = [
      {args: {testIdentifier: 'sortByDateDesc', sortBy: 'date_add', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByDateAsc', sortBy: 'date_add', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByProductIDAsc', sortBy: 'product_id', sortDirection: 'asc', isNumber: true}},
      {args: {testIdentifier: 'sortByProductIDDesc', sortBy: 'product_id', sortDirection: 'desc', isNumber: true}},
      {args: {testIdentifier: 'sortByProductNameAsc', sortBy: 'product_name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByProductNameDesc', sortBy: 'product_name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByReferenceAsc', sortBy: 'reference', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByReferenceDesc', sortBy: 'reference', sortDirection: 'desc'}},
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await movementsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await movementsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await movementsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isNumber) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseInt(text, 10));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseInt(text, 10));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  describe('Bulk edit the quantity of products in the second page of stocks table', async () => {
    it('should go to \'Catalog > Stocks\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage2', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.stocksLink,
      );

      const pageTitle = await stocksPage.getPageTitle(page);
      await expect(pageTitle).to.contains(stocksPage.pageTitle);
    });

    it('should go to the second page and bulk edit the quantity of all products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSecondPage', baseContext);

      await stocksPage.paginateTo(page, 2);

      const updateMessage = await stocksPage.bulkEditQuantityWithInput(page, 301);
      await expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);
    });
  });

  describe('Pagination next and previous', async () => {
    it('should go to Movements page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMovementsPage2', baseContext);

      await stocksPage.goToSubTabMovements(page);

      const pageTitle = await movementsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(movementsPage.pageTitle);
    });

    it('should go to the next page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNextPage', baseContext);

      const pageNumber = await movementsPage.paginateTo(page, 2);
      await expect(pageNumber).to.eq(2);
    });

    it('should go back to the first page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToFirstPage', baseContext);

      const pageNumber = await movementsPage.paginateTo(page, 1);
      await expect(pageNumber).to.eq(1);
    });
  });
});
