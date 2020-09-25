require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_deleteProductsWithBulkActions';

// importing pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');

const ProductFaker = require('@data/faker/product');

const productToCreate = {
  name: 'product To Delete 1',
  type: 'Standard product',
};
const firstProductData = new ProductFaker(productToCreate);
productToCreate.name = 'product To Delete 2';
const secondProductData = new ProductFaker(productToCreate);

let browserContext;
let page;

// Create 2 Standard products in BO and Delete it with Bulk Actions
describe('Create Standard product in BO and Delete it with Bulk Actions', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage1', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

    await productsPage.resetFilterCategory(page);
    const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  [firstProductData, secondProductData].forEach((productData, index) => {
    it('should create new product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createProduct${index + 1}`, baseContext);

      await productsPage.goToAddProductPage(page);
      const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfterCreate${index + 1}`, baseContext);

      await addProductPage.goToSubMenu(
        page,
        addProductPage.catalogParentLink,
        addProductPage.productsLink,
      );

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });
  });

  it('should delete products with bulk Actions', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

    // Filter By reference first
    await productsPage.filterProducts(page, 'name', 'product To Delete ');
    const deleteTextResult = await productsPage.deleteAllProductsWithBulkActions(page);
    await expect(deleteTextResult).to.equal(productsPage.productMultiDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersLast', baseContext);

    await productsPage.resetFilterCategory(page);
    const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });
});
