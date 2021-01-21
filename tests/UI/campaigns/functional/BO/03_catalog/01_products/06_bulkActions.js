require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const ProductFaker = require('@data/faker/product');

const firstProductData = new ProductFaker({name: 'TO DELETE 1', type: 'Standard product'});
const secondProductData = new ProductFaker({name: 'TO DELETE 2', type: 'Standard product'});

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_products_bulkActions';

let browserContext;
let page;

let numberOfProducts = 0;

/*
Go to products page
Create 2 products
Enable by bulk actions
Disable by bulk actions
Delete by bulk actions
*/

describe('Bulk actions products', async () => {
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

  it('should go to products page', async function () {
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

  describe('Bulk set product status', async () => {
    it('should filter product by name', async function () {
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

  describe('Bulk delete products', async () => {
    it('should filter product by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await productsPage.filterProducts(page, 'name', 'TO DELETE');

      const textColumn = await productsPage.getProductNameFromList(page, 1);
      await expect(textColumn).to.contains('TO DELETE');
    });

    it('should delete products with bulk Actions', async function () {
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
