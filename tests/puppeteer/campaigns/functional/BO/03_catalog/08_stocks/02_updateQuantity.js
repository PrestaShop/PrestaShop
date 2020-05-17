require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Products} = require('@data/demo/products');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const StocksPage = require('@pages/BO/catalog/stocks');
const MovementsPage = require('@pages/BO/catalog/stocks/movements');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_stocks_updateQuantity';

let browser;
let page;
let numberOfProducts = 0;
const productStock = Products.demo_18;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    stocksPage: new StocksPage(page),
    movementsPage: new MovementsPage(page),
  };
};

// Update Quantity
describe('Update Quantity', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to stocks page
  loginCommon.loginBO();

  it('should go to "Catalog>Stocks" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.stocksLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProducts', baseContext);
    numberOfProducts = await this.pageObjects.stocksPage.getTotalNumberOfProducts();
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
  tests.forEach((test) => {
    describe(`Update (${test.args.action}) quantity and check movement`, async () => {
      it(`should filter by name '${productStock.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterStocks${test.args.action}`, baseContext);
        await this.pageObjects.stocksPage.simpleFilter(productStock.name);
        const numberOfProductsAfterFilter = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
        await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);
        const textColumn = await this.pageObjects.stocksPage.getTextColumnFromTableStocks(1, 'name');
        await expect(textColumn).to.contains(productStock.name);
        // Get physical and available quantities of product
        productStock.stocks = {
          physical: await this.pageObjects.stocksPage.getTextColumnFromTableStocks(1, 'physical'),
          available: await this.pageObjects.stocksPage.getTextColumnFromTableStocks(1, 'available'),
        };
        await expect(productStock.stocks.physical).to.be.above(0);
        await expect(productStock.stocks.available).to.be.above(0);
      });

      it(`should ${test.args.action} quantity by setting input value`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}ToQuantity`, baseContext);
        // Update Quantity and check successful message
        const updateMessage = await this.pageObjects.stocksPage.updateRowQuantityWithInput(1, test.args.updateValue);
        await expect(updateMessage).to.contains(this.pageObjects.stocksPage.successfulUpdateMessage);
        // Check physical and available quantities of product after update
        const quantityToCheck = await this.pageObjects.stocksPage.getStockQuantityForProduct(1);
        await expect(quantityToCheck.physical).to.be.equal(productStock.stocks.physical + test.args.updateValue);
        productStock.stocks.physical = quantityToCheck.physical;
        await expect(quantityToCheck.available).to.be.equal(productStock.stocks.available + test.args.updateValue);
        productStock.stocks.available = quantityToCheck.available;
      });

      it('should go to movements page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToMovementsPageAfter${test.args.action}`,
          baseContext,
        );
        await this.pageObjects.stocksPage.goToSubTabMovements();
        const pageTitle = await this.pageObjects.movementsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.movementsPage.pageTitle);
      });

      it(`should filter by product name '${productStock.name}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterMovements${test.args.action}`, baseContext);
        await this.pageObjects.movementsPage.simpleFilter(productStock.name);
        const numberOfMovements = await this.pageObjects.movementsPage.getNumberOfElementInGrid();
        await expect(numberOfMovements).to.be.at.least(1);
        const productName = await this.pageObjects.movementsPage.getTextColumnFromTable(numberOfMovements, 'name');
        await expect(productName).to.equal(productStock.name);
        // Check movement quantity
        const movementQuantity = await this.pageObjects.movementsPage.getTextColumnFromTable(
          numberOfMovements,
          'quantity',
        );
        await expect(movementQuantity).to.equal(test.args.updateValue);
      });

      it('should go back to stocks page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `gotoStocksPageAfter${test.args.action}`, baseContext);
        await this.pageObjects.movementsPage.goToSubTabStocks();
        const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterStocks${test.args.action}`, baseContext);
        const numberOfProductsAfterReset = await this.pageObjects.stocksPage.resetFilter();
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
