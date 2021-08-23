require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const {Products} = require('@data/demo/products');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const stocksPage = require('@pages/BO/catalog/stocks');

const baseContext = 'functional_BO_catalog_stocks_bulkEditQuantity';

let browserContext;
let page;

let numberOfProducts = 0;
const stocks = {};

/*
Filter by Products demo_8
Add and subtract quantity for all products in list and check result
 */
describe('BO - Catalog - Stocks : Bulk edit quantity', async () => {
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

  describe('Bulk edit quantity by setting input value', async () => {
    it(`should filter by name '${Products.demo_8.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdateQuantities', baseContext);

      await stocksPage.simpleFilter(page, Products.demo_8.name);

      const numberOfProductsAfterFilter = await stocksPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);

      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await stocksPage.getTextColumnFromTableStocks(page, i, 'name');
        await expect(textColumn).to.contains(Products.demo_8.name);

        // Get physical and available quantities of product
        stocks[`product${i}`] = {
          physical: await stocksPage.getTextColumnFromTableStocks(page, i, 'physical'),
          available: await stocksPage.getTextColumnFromTableStocks(page, i, 'available'),
        };

        await expect(stocks[`product${i}`].physical).to.be.above(0);
        await expect(stocks[`product${i}`].available).to.be.above(0);
      }
    });

    const tests = [
      {args: {action: 'add', updateValue: 100}},
      {args: {action: 'subtract', updateValue: -100}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} to quantities by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantities`, baseContext);

        // Update quantity and check successful message
        const updateMessage = await stocksPage.bulkEditQuantityWithInput(page, test.args.updateValue);
        await expect(updateMessage).to.contains(stocksPage.successfulUpdateMessage);

        const numberOfProductsInList = await stocksPage.getNumberOfProductsFromList(page);

        // Check physical and available quantities of product after update
        for (let i = 1; i <= numberOfProductsInList; i++) {
          const quantityToCheck = await stocksPage.getStockQuantityForProduct(page, i);

          await expect(quantityToCheck.physical).to.be.equal(stocks[`product${i}`].physical + test.args.updateValue);
          stocks[`product${i}`].physical = quantityToCheck.physical;

          await expect(quantityToCheck.available).to.be.equal(stocks[`product${i}`].available + test.args.updateValue);
          stocks[`product${i}`].available = quantityToCheck.available;
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterEditQuantities', baseContext);

      const numberOfProductsAfterReset = await stocksPage.resetFilter(page);
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
