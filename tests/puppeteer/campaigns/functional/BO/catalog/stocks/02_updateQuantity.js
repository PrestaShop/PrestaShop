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
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.stocksLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
  });

  it('should get number of products in list', async function () {
    numberOfProducts = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });

  /*
  Add/Subtract stocks quantity from product
  by writing in input and not using the number up/down buttons
   */
  describe('Update quantity by setting input value', async () => {
    it(`should filter by name '${Products.demo_1.name}'`, async function () {
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

    const tests = [
      {args: {action: 'add', updateValue: 5}},
      {args: {action: 'subtract', updateValue: -5}},
    ];
    tests.forEach((test) => {
      it(`should ${test.args.action} quantity by setting input value`, async function () {
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
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.stocksPage.resetFilter();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
