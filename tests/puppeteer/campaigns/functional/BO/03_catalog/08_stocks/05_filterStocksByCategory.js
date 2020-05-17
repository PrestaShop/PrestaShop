require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const StocksPage = require('@pages/BO/catalog/stocks');
const {Products} = require('@data/demo/products');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_stocks_filterStocksByCategory';

let browser;
let page;
let numberOfProducts = 0;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    stocksPage: new StocksPage(page),
  };
};

describe('Filter stocks by category', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  loginCommon.loginBO();

  it('should go to stocks page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);
    await this.pageObjects.stocksPage.goToSubMenu(
      this.pageObjects.stocksPage.catalogParentLink,
      this.pageObjects.stocksPage.stocksLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
    numberOfProducts = await this.pageObjects.stocksPage.getTotalNumberOfProducts();
    await expect(numberOfProducts).to.be.above(0);
  });

  Object.values(Products).forEach((product, index) => {
    it(
      `should filter by category '${product.category}' and check product '${product.name}' existence`,
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByCategory${index + 1}`, baseContext);
        await this.pageObjects.stocksPage.filterByCategory(product.category);
        const numberOfProductsAfterFilter = await this.pageObjects.stocksPage.getNumberOfProductsFromList();
        await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
        const productsNamesAfterFilter = await this.pageObjects.stocksPage.getAllProductsName();
        await expect(
          productsNamesAfterFilter.some(x => x.includes(product.name)),
          `${product.name} was not found after filter by ${product.category}`,
        ).to.be.true;
      });

    it('should reset filter category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilter${index + 1}`, baseContext);
      const numberOfProductsAfterReset = await this.pageObjects.stocksPage.resetAndGetNumberOfProductsFromList();
      await expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts);
    });
  });
});
