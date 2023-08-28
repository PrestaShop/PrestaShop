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
import ProductFaker from '@data/faker/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'productV2_functional_productBulkActions';

describe('BO - Catalog - Products : Enable, disable, duplicate and Delete products with bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;

  // Data to create first product
  const firstProductData: ProductFaker = new ProductFaker({
    name: 'myFavoriteProduct1',
    type: 'standard',
    status: true,
  });

  // Data to create second product
  const secondProductData: ProductFaker = new ProductFaker({
    name: 'myFavoriteProduct2',
    type: 'standard',
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
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should reset filter and get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, firstProductData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, firstProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('Create second product', async () => {
    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton2', baseContext);

      const isModalVisible = await createProductsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct2', baseContext);

      await createProductsPage.chooseProductType(page, secondProductData.type);

      const isIframeVisible = await createProductsPage.isChooseProductIframeVisible(page);
      await expect(isIframeVisible).to.be.false;
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createSecondProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, secondProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });
  });

  describe('Go to catalog page and filter by the name of created products', async () => {
    it('should click on \'Go to catalog\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCatalogPage', baseContext);

      await createProductsPage.goToCatalogPage(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should filter list by \'Name\' and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterListByReference', baseContext);

      await productsPage.filterProducts(page, 'product_name', 'myFavoriteProduct', 'input');

      const numberOfProductsAfterFilter: number = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.equal(2);

      const textColumn = await productsPage.getTextColumn(page, 'product_name', 1);
      await expect(textColumn).to.contains('myFavoriteProduct');
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

        const isBulkDeleteButtonEnabled = await productsPage.bulkSelectProducts(page);
        await expect(isBulkDeleteButtonEnabled).to.be.true;
      });

      it('should click on bulk actions button', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `clickOnBulkActionsButton${index}`, baseContext);

        const textMessage = await productsPage.clickOnBulkActionsProducts(page, test.args.action);
        await expect(textMessage).to.equal(`${test.args.message} ${test.args.productsNumber} products`);
      });

      it(`should bulk ${test.args.action} products`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulk${test.args.action}Product`, baseContext);

        const textMessage = await productsPage.bulkActionsProduct(page, test.args.action);
        await expect(textMessage).to.equal(
          `${test.args.message} ${test.args.productsNumber} / ${test.args.productsNumber} products`);
      });

      it('should close progress modal', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `close${test.args.action}ProgressModal`, baseContext);

        const isModalVisible = await productsPage.closeBulkActionsProgressModal(page, test.args.action);
        await expect(isModalVisible).to.be.true;
      });

      if (index === 3) {
        it('should reset filter and get number of products', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfProduct', baseContext);

          const numberOfProductAfterBulkActions = await productsPage.resetAndGetNumberOfLines(page);
          await expect(numberOfProductAfterBulkActions).to.be.equal(numberOfProducts);
        });
      }
    });
  });
});
