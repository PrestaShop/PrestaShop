require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_CRUDStandardProductInBO';

// importing pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const foProductPage = require('@pages/FO/product');

// Import data
const ProductFaker = require('@data/faker/product');
const {DefaultFrTax} = require('@data/demo/tax');

const productToCreate = {
  type: 'Standard product',
  productHasCombinations: false,
};
const productData = new ProductFaker(productToCreate);
const editedProductData = new ProductFaker(productToCreate);


let browserContext;
let page;

// Create, read, update and delete Standard product in BO
describe('Create, read, update and delete Standard product in BO', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Steps
  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to Products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    await productsPage.closeSfToolBar(page);
    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

    await productsPage.resetFilterCategory(page);
    const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);
    const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

    // Preview product in FO and get product information
    page = await addProductPage.previewProduct(page);
    const result = await foProductPage.getProductInformation(page);

    // Go back to BO
    page = await foProductPage.closePage(browserContext, page, 0);

    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(productData.name),
      expect(result.price).to.equal(productData.price),
      expect(result.description).to.contains(productData.description),
    ]);
  });

  it('should edit Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

    const createProductMessage = await addProductPage.createEditBasicProduct(page, editedProductData);
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

    page = await addProductPage.previewProduct(page);
    const result = await foProductPage.getProductInformation(page);

    page = await foProductPage.closePage(browserContext, page, 0);

    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(editedProductData.name),
      expect(result.price).to.equal(editedProductData.price),
      expect(result.description).to.be.equal(editedProductData.description),
    ]);
  });

  it('should go to Products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToCheckPrices', baseContext);

    await addProductPage.goToSubMenu(
      page,
      addProductPage.catalogParentLink,
      addProductPage.productsLink,
    );

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should filter list by reference, check prices and go to edit page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsPrices', baseContext);

    await productsPage.filterProducts(page, 'reference', editedProductData.reference);
    const productPrice = await productsPage.getProductPriceFromList(page, 1);
    const productPriceATI = await productsPage.getProductPriceFromList(page, 1, true);

    const conversionRate = (100 + parseInt(DefaultFrTax.rate, 10)) / 100;
    await expect(parseFloat(productPrice)).to.equal(parseFloat((editedProductData.price / conversionRate).toFixed(2)));
    await expect(parseFloat(productPriceATI)).to.equal(parseFloat(editedProductData.price));

    await productsPage.goToEditProductPage(page, 1);
  });

  it('should delete Product and be on product list page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const testResult = await addProductPage.deleteProduct(page);
    await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
});
