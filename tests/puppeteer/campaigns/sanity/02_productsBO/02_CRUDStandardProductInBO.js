require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_CRUDStandardProductInBO';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const FOProductPage = require('@pages/FO/product');
const ProductFaker = require('@data/faker/product');
const {DefaultFrTax} = require('@data/demo/tax');

let browser;
let page;
let productData;
let editedProductData;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.productsLink,
    );

    await this.pageObjects.productsPage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);
    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);
    page = await this.pageObjects.addProductPage.previewProduct();
    this.pageObjects = await init();
    const result = await this.pageObjects.foProductPage.getProductInformation(productData);
    page = await this.pageObjects.foProductPage.closePage(browser, 1);
    this.pageObjects = await init();
    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(productData.name),
      expect(result.price).to.equal(productData.price),
      expect(result.description).to.contains(productData.description),
    ]);
  });

  it('should edit Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);
    const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(editedProductData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);
    page = await this.pageObjects.addProductPage.previewProduct();
    this.pageObjects = await init();
    const result = await this.pageObjects.foProductPage.getProductInformation(editedProductData);
    page = await this.pageObjects.foProductPage.closePage(browser, 1);
    this.pageObjects = await init();
    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(editedProductData.name),
      expect(result.price).to.equal(editedProductData.price),
      expect(result.description).to.be.equal(editedProductData.description),
    ]);
  });

  it('should go to Products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToCheckPrices', baseContext);

    await this.pageObjects.addProductPage.goToSubMenu(
      this.pageObjects.addProductPage.catalogParentLink,
      this.pageObjects.addProductPage.productsLink,
    );

    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should filter list by reference, check prices and go to edit page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsPrices', baseContext);
    await this.pageObjects.productsPage.filterProducts('reference', editedProductData.reference);
    const productPrice = await this.pageObjects.productsPage.getProductPriceFromList(1);
    const productPriceTTC = await this.pageObjects.productsPage.getProductPriceFromList(1, true);
    const conversionRate = (100 + parseInt(DefaultFrTax.rate, 10)) / 100;
    await expect(parseFloat(productPrice)).to.equal(parseFloat((editedProductData.price / conversionRate).toFixed(2)));
    await expect(parseFloat(productPriceTTC)).to.equal(parseFloat(editedProductData.price));
    await this.pageObjects.productsPage.goToEditProductPage(1);
  });

  it('should delete Product and be on product list page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);
    const testResult = await this.pageObjects.addProductPage.deleteProduct();
    await expect(testResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });
});
