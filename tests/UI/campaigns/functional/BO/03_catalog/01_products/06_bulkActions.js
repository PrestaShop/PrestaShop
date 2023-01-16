// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import data
const ProductFaker = require('@data/faker/product');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');

const baseContext = 'functional_BO_catalog_products_bulkActions';

const firstProductData = new ProductFaker({name: 'TO DELETE 1', type: 'Standard product'});
const secondProductData = new ProductFaker({name: 'TO DELETE 2', type: 'Standard product'});

let browserContext;
let page;

let numberOfProducts = 0;
let numberOfFilteredProductsAfterDuplicate = 0;

/*
Go to products page
Create 2 products
Enable/Disable/Duplicate/Delete products by bulk actions
*/

describe('BO - Catalog - Products : Bulk actions products', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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

  it('should reset all filters and get number of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  [firstProductData, secondProductData].forEach((productData, index) => {
    it(`should create product n°${index + 1}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createProduct${index + 1}`, baseContext);

      await productsPage.goToAddProductPage(page);
      const createProductMessage = await addProductPage.createEditBasicProduct(page, productData);
      await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
    });

    it('should go to catalog page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToCatalogPage${index + 1}`, baseContext);

      await addProductPage.goToCatalogPage(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });
  });

  describe('Bulk set product status', async () => {
    it('should filter products by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkSetStatus', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      await expect(textColumn).to.contains('TO DELETE');
    });

    [
      {action: 'enable', status: true},
      {action: 'disable', status: false},
    ].forEach((test) => {
      it(`should ${test.action} products`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Products`, baseContext);

        const textResult = await productsPage.bulkSetStatus(page, test.status);
        await expect(textResult)
          .to.equal(
            test.status
              ? productsPage.productMultiActivatedSuccessfulMessage
              : productsPage.productMultiDeactivatedSuccessfulMessage,
          );

        for (let row = 1; row <= 2; row++) {
          const productStatus = await productsPage.getProductStatusFromList(page, row);
          await expect(productStatus).to.equal(test.status);
        }
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkSetStatus', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts + 2);
    });
  });

  describe('Bulk duplicate products', async () => {
    it('should filter products by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDuplicate', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      await expect(textColumn).to.contains('TO DELETE');
    });

    it('should duplicate products by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDuplicate', baseContext);

      const duplicateTextResult = await productsPage.duplicateAllProductsWithBulkActions(page);
      await expect(duplicateTextResult).to.equal(productsPage.productMultiDuplicatedSuccessfulMessage);

      numberOfFilteredProductsAfterDuplicate = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfFilteredProductsAfterDuplicate).to.be.below(numberOfProducts);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDuplicate', baseContext);

      const numberOfProductsAfterDuplicate = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterDuplicate)
        .to
        .be
        .equal(numberOfProducts + numberOfFilteredProductsAfterDuplicate);
    });
  });

  describe('Bulk delete products', async () => {
    it('should filter products by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      await expect(textColumn).to.contains('TO DELETE');
    });

    it('should delete products by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await productsPage.deleteAllProductsWithBulkActions(page);
      await expect(deleteTextResult).to.equal(productsPage.productMultiDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts);
    });
  });
});
