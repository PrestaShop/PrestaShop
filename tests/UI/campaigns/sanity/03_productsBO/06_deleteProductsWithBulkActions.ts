import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';

// Import data
import ProductData from '@data/faker/product';

const baseContext: string = 'sanity_productsBO_deleteProductsWithBulkActions';

describe('BO - Catalog - Products : Delete products with bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  // Data to create first product
  const firstProductData: ProductData = new ProductData({
    name: 'toDelete1'.toUpperCase(),
    type: 'standard',
    quantity: 50,
    minimumQuantity: 1,
    status: true,
  });

  // Data to create second product
  const secondProductData: ProductData = new ProductData({
    name: 'toDelete2'.toUpperCase(),
    type: 'standard',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Create first product', async () => {
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, firstProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, firstProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('Create second product', async () => {
    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton2', baseContext);

      const isModalVisible = await createProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct2', baseContext);

      await createProductsPage.chooseProductType(page, secondProductData.type);

      const isIframeVisible = await createProductsPage.isChooseProductIframeVisible(page);
      expect(isIframeVisible).to.eq(false);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, secondProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('Bulk delete created products', async () => {
    it('should click on \'Go to catalog\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await createProductsPage.goToCatalogPage(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should filter list by \'Name\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterListByReference', baseContext);

      await productsPage.filterProducts(page, 'product_name', 'toDelete', 'input');

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(2);

      const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.contains('TODELETE');
    });

    it('should select the 2 products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDeleteProduct', baseContext);

      const isBulkDeleteButtonEnabled: boolean = await productsPage.bulkSelectProducts(page);
      expect(isBulkDeleteButtonEnabled).to.eq(true);
    });

    it('should click on bulk actions button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnBulkDeleteButton', baseContext);

      const textMessage = await productsPage.clickOnBulkActionsProducts(page, 'delete');
      expect(textMessage).to.equal('Deleting 2 products');
    });

    it('should bulk delete products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteProduct', baseContext);

      const textMessage = await productsPage.bulkActionsProduct(page, 'delete');
      expect(textMessage).to.equal('Deleting 2 / 2 products');
    });

    it('should close progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeProgressModal', baseContext);

      const isModalNotVisible = await productsPage.closeBulkActionsProgressModal(page, 'delete');
      expect(isModalNotVisible).to.eq(true);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
