import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  dataProducts,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_duplicateProduct';

describe('BO - Catalog - Products : Duplicate product', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Duplicate product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await boProductsPage.getNumberOfProductsFromHeader(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it(`should filter by reference '${dataProducts.demo_14.reference}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByReference', baseContext);

      await boProductsPage.filterProducts(page, 'reference', dataProducts.demo_14.reference);

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(1);
    });

    it('should click on duplicate button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDuplicateProduct', baseContext);

      const isModalVisible = await boProductsPage.clickOnDuplicateProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should duplicate product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'duplicateProduct', baseContext);

      const textMessage = await boProductsPage.clickOnConfirmDialogButton(page);
      expect(textMessage).to.equal(boProductsCreatePage.successfulDuplicateMessage);
    });

    it('should click on duplicate button from the footer of create product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickDuplicateFromFooterPage', baseContext);

      const textMessage = await boProductsCreatePage.duplicateProduct(page);
      expect(textMessage).to.equal(boProductsCreatePage.successfulDuplicateMessage);
    });

    it('should click on \'Go to catalog\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await boProductsCreatePage.goToCatalogPage(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should check the number of duplicated products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProducts', baseContext);

      const numberOfProductsAfterDuplicate = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterDuplicate).to.equal(3);
    });

    it('should check the name of the duplicated product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNameDuplicatedProduct', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', 'copy of');

      let textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.contain(`copy of copy of ${dataProducts.demo_14.name}`);

      textColumn = await boProductsPage.getTextColumn(page, 'product_name', 2);
      expect(textColumn).to.contain(`copy of ${dataProducts.demo_14.name}`);
    });
  });

  describe('Bulk delete duplicated products', async () => {
    it('should select the 2 products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
      expect(isBulkDeleteButtonEnabled).to.eq(true);
    });

    it('should click on bulk actions button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBulkDeleteButton', baseContext);

      const textMessage = await boProductsPage.clickOnBulkActionsProducts(page, 'delete');
      expect(textMessage).to.equal('Deleting 2 products');
    });

    it('should bulk delete products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProduct', baseContext);

      const textMessage = await boProductsPage.bulkActionsProduct(page, 'delete');
      expect(textMessage).to.equal('Deleting 2 / 2 products');
    });

    it('should close progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProgressModal', baseContext);

      const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, 'delete');
      expect(isModalVisible).to.eq(true);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
