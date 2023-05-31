// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import stocksPage from '@pages/BO/catalog/stocks';
import movementsPage from '@pages/BO/catalog/stocks/movements';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_stocks_updateQuantity';

// Update Quantity
describe('BO - Catalog - Stocks : Update Quantity', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  const productStock: any = Products.demo_18;

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
    await expect(pageTitle).to.contains(stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);

    numberOfProducts = await stocksPage.getTotalNumberOfProducts(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  /*
  Add/Subtract stocks quantity from product
  by writing in input and not using the number up/down buttons
  and check the movements
   */
  [
    {args: {action: 'add', updateValue: 5}},
    {args: {action: 'subtract', updateValue: -5}},
  ].forEach((test, index: number) => {
    describe(`Update (${test.args.action}) quantity and check movement`, async () => {
      it(`should filter by name '${productStock.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterStocks${test.args.action}`, baseContext);

        await stocksPage.simpleFilter(page, productStock.name);

        const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
        await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

        const textColumn = await stocksPage.getTextColumnFromTableStocks(page, 1, 'name');
        await expect(textColumn).to.contains(productStock.name);

        // Get physical and available quantities of product
        productStock.stocks = {
          physical: parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'physical'), 10),
          available: parseInt(await stocksPage.getTextColumnFromTableStocks(page, 1, 'available'), 10),
        };

        await expect(productStock.stocks.physical).to.be.above(0);
        await expect(productStock.stocks.available).to.be.above(0);
      });

      it(`should ${test.args.action} quantity by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantity`, baseContext);

        // Update Quantity and check successful message
        const updateMessage = await stocksPage.updateRowQuantityWithInput(page, 1, test.args.updateValue);
        await expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);

        // Check physical and available quantities of product after update
        const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, 1);

        await expect(quantityToCheck.physical).to.be.equal(productStock.stocks.physical + test.args.updateValue);
        productStock.stocks.physical = quantityToCheck.physical;

        await expect(quantityToCheck.available).to.be.equal(productStock.stocks.available + test.args.updateValue);
        productStock.stocks.available = quantityToCheck.available;
      });

      it('should go to Movements page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMovementsPage${index}`, baseContext);

        await stocksPage.goToSubTabMovements(page);

        const pageTitle = await movementsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(movementsPage.pageTitle);
      });

      it(`should filter by product name '${productStock.name}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterMovements${test.args.action}`, baseContext);

        await movementsPage.simpleFilter(page, productStock.name);

        const numberOfMovements = await movementsPage.getNumberOfElementInGrid(page);
        await expect(numberOfMovements).to.be.at.least(1);

        const productName = await movementsPage.getTextColumnFromTable(page, numberOfMovements, 'product_name');
        await expect(productName).to.equal(productStock.name);

        // Check movement quantity
        const movementQuantity = await movementsPage.getTextColumnFromTable(page, numberOfMovements, 'quantity');
        await expect(parseFloat(movementQuantity)).to.equal(test.args.updateValue);
      });

      it('should go back to stocks page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToStocksPageAfter${test.args.action}`, baseContext);

        await movementsPage.goToSubTabStocks(page);

        const pageTitle = await stocksPage.getPageTitle(page);
        await expect(pageTitle).to.contains(stocksPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterStocks${index}`, baseContext);

        const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
