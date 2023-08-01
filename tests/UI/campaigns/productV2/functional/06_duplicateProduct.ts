// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/productsV2';
import createProductsPage from '@pages/BO/catalog/productsV2/add';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'productV2_functional_duplicateProduct';

describe('BO - Catalog - Products : Duplicate product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Duplicate product', async () => {
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

    it('should get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await productsPage.getNumberOfProductsFromHeader(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it(`should filter by reference '${Products.demo_14.reference}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByReference', baseContext);

      await productsPage.filterProducts(page, 'reference', Products.demo_14.reference);

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.equal(1);
    });

    it('should click on duplicate button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDuplicateProduct', baseContext);

      const isModalVisible = await productsPage.clickOnDuplicateProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should duplicate product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

      const textMessage = await productsPage.clickOnConfirmDialogButton(page);
      await expect(textMessage).to.equal(createProductsPage.successfulDuplicateMessage);
    });

    it('should click on duplicate button from the footer of create product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDuplicateFromFooterPage', baseContext);

      const textMessage = await createProductsPage.duplicateProduct(page);
      await expect(textMessage).to.equal(createProductsPage.successfulDuplicateMessage);
    });

    it('should click on \'Go to catalog\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await createProductsPage.goToCatalogPage(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should check the number of duplicated products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const numberOfProductsAfterDuplicate = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterDuplicate).to.equal(3);
    });

    it('should check the name of the duplicated product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNameDuplicatedProduct', baseContext);

      await productsPage.filterProducts(page, 'product_name', 'copy of');

      let textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
      await expect(textColumn).to.contain(`copy of copy of ${Products.demo_14.name}`);

      textColumn = await productsPage.getTextColumn(page, 'product_name', 2);
      await expect(textColumn).to.contain(`copy of ${Products.demo_14.name}`);
    });
  });

  describe('Bulk delete duplicated products', async () => {
    it('should select the 2 products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isBulkDeleteButtonEnabled = await productsPage.bulkSelectProducts(page);
      await expect(isBulkDeleteButtonEnabled).to.be.true;
    });

    it('should click on bulk actions button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBulkDeleteButton', baseContext);

      const textMessage = await productsPage.clickOnBulkActionsProducts(page, 'delete');
      await expect(textMessage).to.equal('Deleting 2 products');
    });

    it('should bulk delete products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProduct', baseContext);

      const textMessage = await productsPage.bulkActionsProduct(page, 'delete');
      await expect(textMessage).to.equal('Deleting 2 / 2 products');
    });

    it('should close progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProgressModal', baseContext);

      const isModalVisible = await productsPage.closeBulkActionsProgressModal(page, 'delete');
      await expect(isModalVisible).to.be.true;
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
