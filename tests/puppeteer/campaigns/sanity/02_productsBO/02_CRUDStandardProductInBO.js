require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ProductsPage = require('@pages/BO/products');
const AddProductPage = require('@pages/BO/addProduct');
const FOProductPage = require('@pages/FO/product');
const ProductFaker = require('@data/faker/product');

let browser;
let page;
let productData;
let editedProductData;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    foProductPage: new FOProductPage(page),
  };
};
// Create, read, update and delete Standard product in BO
describe('Create, read, update and delete Standard product in BO', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    const productToCreate = {
      type: 'Standard product',
      productHasCombinations: false,
    };
    productData = await (new ProductFaker(productToCreate));
    editedProductData = await (new ProductFaker(productToCreate));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  loginCommon.loginBO();
  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.productsParentLink,
      this.pageObjects.boBasePage.productsLink);
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });
  it('should reset all filters', async function () {
    if (await this.pageObjects.productsPage.elementVisible(this.pageObjects.productsPage.filterResetButton, 2000)) {
      await this.pageObjects.productsPage.resetFilter();
    }
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
  it('should create Product', async function () {
    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(productData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });
  it('should preview and check product in FO', async function () {
    page = await this.pageObjects.addProductPage.previewProduct();
    this.pageObjects = await init();
    const result = await this.pageObjects.foProductPage.checkProduct(productData);
    await Promise.all([
      expect(result.name).to.be.true,
      expect(result.price).to.be.true,
      expect(result.quantity_wanted).to.be.true,
      expect(result.description).to.be.true,
    ]);
    page = await this.pageObjects.foProductPage.closePage(browser, 1);
    this.pageObjects = await init();
  });
  it('should edit Product', async function () {
    const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(editedProductData, false);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });
  it('should preview and check product in FO', async function () {
    page = await this.pageObjects.addProductPage.previewProduct();
    this.pageObjects = await init();
    const result = await this.pageObjects.foProductPage.checkProduct(editedProductData);
    await Promise.all([
      expect(result.name).to.be.true,
      expect(result.price).to.be.true,
      expect(result.quantity_wanted).to.be.true,
      expect(result.description).to.be.true,
    ]);
    page = await this.pageObjects.foProductPage.closePage(browser, 1);
    this.pageObjects = await init();
  });
  it('should delete Product and be on product list page', async function () {
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });
});
