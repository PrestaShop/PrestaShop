// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import stocksPage from '@pages/BO/catalog/stocks';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_bulkEditQuantity';

/*
Filter by Products demo_8
Add and subtract quantity for all products in list and check result
 */
describe('BO - Catalog - Stocks : Bulk edit quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  const stocks: any = {};

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

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

    numberOfProducts = await stocksPage.getTotalNumberOfProducts(page);
    expect(numberOfProducts).to.be.above(0);
  });

  describe('Bulk edit quantity by setting input value', async () => {
    it(`should filter by name '${Products.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateQuantities', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_8.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await stocksPage.getTextColumnFromTableStocks(page, i, 'product_name');
        expect(textColumn).to.contains(Products.demo_8.name);

        // Get physical and available quantities of product
        stocks[`product${i}`] = {
          physical: parseInt(await stocksPage.getTextColumnFromTableStocks(page, i, 'physical'), 10),
          available: parseInt(await stocksPage.getTextColumnFromTableStocks(page, i, 'available'), 10),
        };

        expect(stocks[`product${i}`].physical).to.be.above(0);
        expect(stocks[`product${i}`].available).to.be.above(0);
      }
    });

    [
      {args: {action: 'add', updateValue: 100}},
      {args: {action: 'subtract', updateValue: -100}},
    ].forEach((test) => {
      it(`should ${test.args.action} to quantities by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantities`, baseContext);

        // Update quantity and check successful message
        const updateMessage = await stocksPage.bulkEditQuantityWithInput(page, test.args.updateValue);
        expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);

        const numberOfProductsInList = await stocksPage.getNumberOfProductsFromList(page);

        // Check physical and available quantities of product after update
        for (let i = 1; i <= numberOfProductsInList; i++) {
          const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, i);

          expect(quantityToCheck.physical).to.be.equal(stocks[`product${i}`].physical + test.args.updateValue);
          stocks[`product${i}`].physical = quantityToCheck.physical;

          expect(quantityToCheck.available).to.be.equal(stocks[`product${i}`].available + test.args.updateValue);
          stocks[`product${i}`].available = quantityToCheck.available;
        }
      });
    });
  });

  describe('Bulk edit quantity by using the arrow up/down', async () => {
    [
      {args: {action: 'add', updateValue: 5, direction: 'up'}},
      {args: {action: 'subtract', updateValue: -5, direction: 'down'}},
    ].forEach((test) => {
      it(`should ${test.args.action} quantity by using the arrow up/down`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantities2`, baseContext);

        // Update quantity and check successful message
        const updateMessage = await stocksPage.bulkEditQuantityWithArrowUpDownButtons(page,
          test.args.updateValue,
          test.args.direction,
        );
        expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);

        const numberOfProductsInList = await stocksPage.getNumberOfProductsFromList(page);

        // Check physical and available quantities of product after update
        for (let i = 1; i <= numberOfProductsInList; i++) {
          const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, i);

          expect(quantityToCheck.physical).to.be.equal(stocks[`product${i}`].physical + test.args.updateValue);
          stocks[`product${i}`].physical = quantityToCheck.physical;

          expect(quantityToCheck.available).to.be.equal(stocks[`product${i}`].available + test.args.updateValue);
          stocks[`product${i}`].available = quantityToCheck.available;
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterEditQuantities', baseContext);

      const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
