require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_CRUDStandardProductWithCombinationsInBO';

// importing pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const foProductPage = require('@pages/FO/product');
const ProductFaker = require('@data/faker/product');

const productToCreate = {
  type: 'Standard product',
  productHasCombinations: true,
};
const productWithCombinations = new ProductFaker(productToCreate);
const editedProductWithCombinations = new ProductFaker(productToCreate);


let browserContext;
let page;


// Create, read, update and delete Standard product with combinations in BO
describe('Create, read, update and delete Standard product with combinations in BO', async () => {
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

  it('should create Product with Combinations', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

    await productsPage.goToAddProductPage(page);
    await addProductPage.createEditBasicProduct(page, productWithCombinations);
    const createProductMessage = await addProductPage.setCombinationsInProduct(
      page,
      productWithCombinations,
    );
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

    page = await addProductPage.previewProduct(page);
    const result = await foProductPage.getProductInformation(page);

    page = await foProductPage.closePage(browserContext, page, 0);

    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(productWithCombinations.name),
      expect(result.price).to.equal(productWithCombinations.price),
      expect(result.description).to.contains(productWithCombinations.description),
    ]);
  });

  it('should edit Product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

    await addProductPage.createEditBasicProduct(page, editedProductWithCombinations);
    const createProductMessage = await addProductPage.setCombinationsInProduct(
      page,
      editedProductWithCombinations,
    );
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview and check product in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

    page = await addProductPage.previewProduct(page);
    const result = await foProductPage.getProductInformation(page);

    page = await foProductPage.closePage(browserContext, page, 0);

    // Check that all Product attribute are correct
    await Promise.all([
      expect(result.name).to.equal(editedProductWithCombinations.name),
      expect(result.price).to.equal(editedProductWithCombinations.price),
      expect(result.description).to.contains(editedProductWithCombinations.description),
    ]);
  });

  it('should delete Product and be on product list page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const testResult = await addProductPage.deleteProduct(page);
    await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
});
