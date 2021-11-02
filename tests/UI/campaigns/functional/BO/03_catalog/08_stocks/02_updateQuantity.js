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
const movementsPage = require('@pages/BO/catalog/stocks/movements');

const baseContext = 'functional_BO_catalog_stocks_updateQuantity';

let browserContext;
let page;
let numberOfProducts = 0;

const productStock = Products.demo_18;

// Update Quantity
describe('BO - Catalog - Stocks : Update Quantity', async () => {
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
  const tests = [
    {args: {action: 'add', updateValue: 5}},
    {args: {action: 'subtract', updateValue: -5}},
  ];

  tests.forEach((test, index) => {
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
          physical: await stocksPage.getTextColumnFromTableStocks(page, 1, 'physical'),
          available: await stocksPage.getTextColumnFromTableStocks(page, 1, 'available'),
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

        const productName = await movementsPage.getTextColumnFromTable(page, numberOfMovements, 'name');
        await expect(productName).to.equal(productStock.name);

        // Check movement quantity
        const movementQuantity = await movementsPage.getTextColumnFromTable(page, numberOfMovements, 'quantity');
        await expect(movementQuantity).to.equal(test.args.updateValue);
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
