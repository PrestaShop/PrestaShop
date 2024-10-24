// Import utils
import testContext from '@utils/testContext';

// Import pages
import createProductsPage from '@pages/BO/catalog/products/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_productBulkActions';

describe('BO - Catalog - Products : Enable, disable, duplicate and Delete products with bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  // Data to create first product
  const firstProductData: FakerProduct = new FakerProduct({
    name: 'myFavoriteProduct1',
    type: 'standard',
    status: true,
  });

  // Data to create second product
  const secondProductData: FakerProduct = new FakerProduct({
    name: 'myFavoriteProduct2',
    type: 'standard',
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('Create first product', async () => {
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

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, firstProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

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

  describe('Go to catalog page and filter by the name of created products', async () => {
    it('should click on \'Go to catalog\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await createProductsPage.goToCatalogPage(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should filter list by \'Name\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterListByReference', baseContext);

      await boProductsPage.filterProducts(page, 'product_name', 'myFavoriteProduct', 'input');

      const numberOfProductsAfterFilter: number = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(2);

      const textColumn = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(textColumn).to.contains('myFavoriteProduct');
    });
  });

  [
    {
      args: {
        action: 'enable',
        message: 'Activating',
        productsNumber: 2,
      },
    },
    {
      args: {
        action: 'disable',
        message: 'Deactivating',
        productsNumber: 2,
      },
    },
    {
      args: {
        action: 'duplicate',
        message: 'Duplicating',
        productsNumber: 2,
      },
    },
    {
      args: {
        action: 'delete',
        message: 'Deleting',
        productsNumber: 4,
      },
    },
  ].forEach((test, index: number) => {
    describe(`Bulk ${test.args.action} created products`, async () => {
      it('should select the 2 products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `selectProducts${index}`, baseContext);

        const isBulkDeleteButtonEnabled = await boProductsPage.bulkSelectProducts(page);
        expect(isBulkDeleteButtonEnabled).to.eq(true);
      });

      it('should click on bulk actions button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnBulkActionsButton${index}`, baseContext);

        const textMessage = await boProductsPage.clickOnBulkActionsProducts(page, test.args.action);
        expect(textMessage).to.equal(`${test.args.message} ${test.args.productsNumber} products`);
      });

      it(`should bulk ${test.args.action} products`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}Product`, baseContext);

        const textMessage = await boProductsPage.bulkActionsProduct(page, test.args.action);
        expect(textMessage).to.equal(
          `${test.args.message} ${test.args.productsNumber} / ${test.args.productsNumber} products`);
      });

      it('should close progress modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `close${test.args.action}ProgressModal`, baseContext);

        const isModalVisible = await boProductsPage.closeBulkActionsProgressModal(page, test.args.action);
        expect(isModalVisible).to.eq(true);
      });

      if (index === 3) {
        it('should reset filter and get number of products', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

          const numberOfProductAfterBulkActions = await boProductsPage.resetAndGetNumberOfLines(page);
          expect(numberOfProductAfterBulkActions).to.be.equal(numberOfProducts);
        });
      }
    });
  });
});
