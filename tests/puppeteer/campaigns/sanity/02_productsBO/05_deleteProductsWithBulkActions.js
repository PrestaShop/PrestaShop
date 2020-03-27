require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_deleteProduct';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const ProductFaker = require('@data/faker/product');

let browser;
let page;
const productToCreate = {
  name: 'product To Delete 1',
  type: 'Standard product',
};
const firstProductData = new ProductFaker(productToCreate);
productToCreate.name = 'product To Delete 2';
const secondProductData = new ProductFaker(productToCreate);

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
  };
};

// Create 2 Standard products in BO and Delete it with Bulk Actions
describe('Create Standard product in BO and Delete it with Bulk Actions', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage1', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  const productsData = [firstProductData, secondProductData];
  productsData.forEach((productData, index) => {
    it('should create new product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createProduct${index + 1}`, baseContext);
      await this.pageObjects.productsPage.goToAddProductPage();
      const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
      await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
    });

    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index + 1}`, baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.productsLink);
      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });
  });

  it('should delete products with bulk Actions', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);
    // Filter By reference first
    await this.pageObjects.productsPage.filterProducts('name', 'product To Delete ');
    const deleteTextResult = await this.pageObjects.productsPage.deleteAllProductsWithBulkActions();
    await expect(deleteTextResult).to.equal(this.pageObjects.productsPage.productMultiDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersLast', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });
});
