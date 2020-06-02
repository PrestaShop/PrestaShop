require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const StocksPage = require('@pages/BO/catalog/stocks');

// Import data
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_stocks_filterStocksByStatus';

let browser;
let page;

let numberOfProducts = 0;

const productData = new ProductFaker({type: 'Standard product', status: false});

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    stocksPage: new StocksPage(page),
  };
};

/*
Create new disabled product
Filter stocks page by status and check existence of product
Delete product
 */
describe('Filter stocks by status', async () => {
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

  it('should go to Products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToCreate', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.productsLink,
    );

    await this.pageObjects.productsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    await this.pageObjects.productsPage.resetFilterCategory();
    numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  describe('Create new product', async () => {
    it('should create Product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await this.pageObjects.productsPage.goToAddProductPage();
      const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
      await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
    });
  });

  describe('Check the disabled product in stocks page', async () => {
    it('should go to stocks page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToStocksPage', baseContext);

      await this.pageObjects.addProductPage.goToSubMenu(
        this.pageObjects.addProductPage.catalogParentLink,
        this.pageObjects.addProductPage.stocksLink,
      );

      const pageTitle = await this.pageObjects.stocksPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.stocksPage.pageTitle);
    });

    it('should filter by status \'disabled\' and check the existence of the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterStatus', baseContext);

      await this.pageObjects.stocksPage.filterByStatus('disabled');

      const textColumn = await this.pageObjects.stocksPage.getTextColumnFromTableStocks(1, 'name');
      await expect(textColumn).to.contains(productData.name);
    });
  });

  describe('Delete product', async () => {
    it('should go to products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await this.pageObjects.stocksPage.goToSubMenu(
        this.pageObjects.stocksPage.catalogParentLink,
        this.pageObjects.stocksPage.productsLink,
      );

      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const testResult = await this.pageObjects.productsPage.deleteProduct(productData);
      await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);

      const numberOfProductsAfterDelete = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProductsAfterDelete).to.equal(numberOfProducts);
    });
  });
});
