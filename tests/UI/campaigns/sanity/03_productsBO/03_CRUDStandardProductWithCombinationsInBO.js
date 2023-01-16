// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import foProductPage from '@pages/FO/product';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');

const ProductFaker = require('@data/faker/product');

const baseContext = 'sanity_productsBO_CRUDStandardProductWithCombinationsInBO';

const productToCreate = {
  type: 'Standard product',
  productHasCombinations: true,
};
const productWithCombinations = new ProductFaker(productToCreate);
const editedProductWithCombinations = new ProductFaker(productToCreate);

let browserContext;
let page;
let productInformation = {
  price: 0,
  name: '',
  description: '',
  shortDescription: '',
};

// Create, read, update and delete Standard product with combinations in BO
describe('BO - Catalog - Products : Create, read, update and delete Standard product '
  + 'with combinations in BO', async () => {
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

  it('should go to \'Catalog > Products\' page', async function () {
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
    const createProductMessage = await addProductPage.setAttributesInProduct(
      page,
      productWithCombinations,
    );
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct1', baseContext);

    page = await addProductPage.previewProduct(page);
    productInformation = await foProductPage.getProductInformation(page);

    const pageTitle = await foProductPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productWithCombinations.name);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

    page = await foProductPage.closePage(browserContext, page, 0);

    const pageTitle = await addProductPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addProductPage.pageTitle);
  });

  it('should check that all product attributes are correct', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductAttributes1', baseContext);

    // Check that all Product attribute are correct
    await Promise.all([
      expect(productInformation.name).to.equal(productWithCombinations.name),
      expect(productInformation.price).to.equal(productWithCombinations.price),
      expect(productInformation.description).to.contains(productWithCombinations.description),
      expect(productInformation.shortDescription).to.contains(productWithCombinations.summary),
    ]);
  });

  it('should edit product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

    await addProductPage.createEditBasicProduct(page, editedProductWithCombinations);
    const createProductMessage = await addProductPage.setAttributesInProduct(
      page,
      editedProductWithCombinations,
    );
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });

  it('should preview product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

    page = await addProductPage.previewProduct(page);
    productInformation = await foProductPage.getProductInformation(page);

    const pageTitle = await foProductPage.getPageTitle(page);
    await expect(pageTitle).to.contains(editedProductWithCombinations.name);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

    page = await foProductPage.closePage(browserContext, page, 0);

    const pageTitle = await addProductPage.getPageTitle(page);
    await expect(pageTitle).to.contains(addProductPage.pageTitle);
  });

  it('should check that all product attributes are correct', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductAttributes2', baseContext);

    // Check that all Product attribute are correct
    await Promise.all([
      expect(productInformation.name).to.equal(editedProductWithCombinations.name),
      expect(productInformation.price).to.equal(editedProductWithCombinations.price),
      expect(productInformation.shortDescription).to.contains(editedProductWithCombinations.summary),
      expect(productInformation.description).to.contains(editedProductWithCombinations.description),
    ]);
  });

  it('should delete product and be on product list page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

    const testResult = await addProductPage.deleteProduct(page);
    await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
});
