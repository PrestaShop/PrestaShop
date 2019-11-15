require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const ProductFaker = require('@data/faker/product');

let browser;
let page;
let firstProductData;
let secondProductData;

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
    const productToCreate = {
      name: 'product To Delete 1',
      type: 'Standard product',
      productHasCombinations: false,
    };
    firstProductData = await (new ProductFaker(productToCreate));
    productToCreate.name = 'product To Delete 2';
    secondProductData = await (new ProductFaker(productToCreate));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  loginCommon.loginBO();

  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create First Product', async function () {
    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(firstProductData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink);
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should create Second Product', async function () {
    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(secondProductData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink);
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should delete products with bulk Actions', async function () {
    // Filter By reference first
    await this.pageObjects.productsPage.filterProducts('name', 'product To Delete ');
    const deleteTextResult = await this.pageObjects.productsPage.deleteAllProductsWithBulkActions();
    await expect(deleteTextResult).to.equal(this.pageObjects.productsPage.productMultiDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });
});
