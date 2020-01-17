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
const stocks = {};

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    stocksPage: new StocksPage(page),
  };
};

/*
Filter by Products demo_8
Add and subtract quantity for all products in list and check result
 */
describe('Bulk Edit Quantity', async () => {
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

  describe('Bulk edit quantity by setting input value', async () => {
    it(`should filter by name '${Products.demo_8.name}'`, async function () {
      await this.pageObjects.stocksPage.simpleFilter(Products.demo_8.name);
      const numberOfProductsAfterFilter = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.at.most(numberOfProducts);
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await this.pageObjects.stocksPage.getTextColumnFromTableStocks(i, 'name');
        await expect(textColumn).to.contains(Products.demo_8.name);
        // Get physical and available quantities of product
        stocks[`product${i}`] = {
          physical: await this.pageObjects.stocksPage.getTextColumnFromTableStocks(i, 'physical'),
          available: await this.pageObjects.stocksPage.getTextColumnFromTableStocks(i, 'available'),
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
        // Update quantity and check successful message
        const updateMessage = await this.pageObjects.stocksPage.bulkEditQuantityWithInput(test.args.updateValue);
        await expect(updateMessage).to.contains(this.pageObjects.stocksPage.successfulUpdateMessage);
        const numberOfProductsInList = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
        // Check physical and available quantities of product after update
        for (let i = 1; i <= numberOfProductsInList; i++) {
          const quantityToCheck = await this.pageObjects.stocksPage.getStockQuantityForProduct(i);
          await expect(quantityToCheck.physical).to.be.equal(stocks[`product${i}`].physical + test.args.updateValue);
          stocks[`product${i}`].physical = quantityToCheck.physical;
          await expect(quantityToCheck.available).to.be.equal(stocks[`product${i}`].available + test.args.updateValue);
          stocks[`product${i}`].available = quantityToCheck.available;
        }
      });
    });

    it('should reset all filters', async function () {
      const numberOfProductsAfterReset = await this.pageObjects.stocksPage.resetFilter();
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
