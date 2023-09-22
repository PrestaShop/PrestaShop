// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  resetNewProductPageAsDefault,
  setFeatureFlag,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';
import addProductPage from '@pages/BO/catalog/products/add';
import monitoringPage from '@pages/BO/catalog/monitoring';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_monitoring_monitoringProducts';

/*
Create new product
Check existence of new product in monitoring page
Delete product and check deletion in products page
 */
describe('BO - Catalog - Monitoring : Create different products and delete them from monitoring page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let numberOfProductsIngrid: number = 0;

  const productWithoutImage: ProductData = new ProductData({type: 'Standard product'});
  const disabledProduct: ProductData = new ProductData({type: 'Standard product', status: false});
  const productWithoutCombinationsWithoutQuantity: ProductData = new ProductData({type: 'Standard product', quantity: 0});
  const productWithCombinationsWithoutQuantity: ProductData = new ProductData({type: 'Standard product', quantity: 0});
  const productWithoutPrice: ProductData = new ProductData({type: 'Standard product', price: 0});
  const productWithoutDescription: ProductData = new ProductData({type: 'Standard product', description: '', summary: ''});

  // Pre-condition: Disable new product page
  setFeatureFlag(featureFlagPage.featureFlagProductPageV2, false, `${baseContext}_disableNewProduct`);

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

  const tests = [
    {
      args:
        {
          testIdentifier: 'productWithoutImage',
          productType: 'without image',
          productToCreate: productWithoutImage,
          gridName: 'product_without_image',
        },
    },
    {
      args:
        {
          testIdentifier: 'disabledProduct',
          productType: 'disabled',
          productToCreate: disabledProduct,
          gridName: 'disabled_product',
        },
    },
    {
      args:
        {
          testIdentifier: 'productWithoutCombinationsWithoutQuantity',
          productType: 'without combinations and without available quantities',
          productToCreate: productWithoutCombinationsWithoutQuantity,
          gridName: 'no_qty_product_without_combination',
        },
    },
    {
      args: {
        testIdentifier: 'productWithCombinationsWithQuantity',
        productType: 'with combinations and without available quantities',
        productToCreate: productWithCombinationsWithoutQuantity,
        gridName: 'no_qty_product_with_combination',
        hasCombinations: true,
      },
    },
    {
      args: {
        testIdentifier: 'productWithoutPrice',
        productType: 'without price',
        productToCreate: productWithoutPrice,
        gridName: 'product_without_price',
      },
    },
    {
      args: {
        testIdentifier: 'productWithoutDescription',
        productType: 'without description',
        productToCreate: productWithoutDescription,
        gridName: 'product_without_description',
      },
    },
  ];

  tests.forEach((test, index: number) => {
    describe(`Create product ${test.args.productType} in BO`, async () => {
      if (index === 0) {
        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `${test.args.testIdentifier}_goToProductsPage`,
            baseContext,
          );

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.catalogParentLink,
            dashboardPage.productsLink,
          );
          await productsPage.closeSfToolBar(page);

          const pageTitle = await productsPage.getPageTitle(page);
          expect(pageTitle).to.contains(productsPage.pageTitle);
        });
      }

      it('should reset all filters and get number of products in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}_resetFirst`, baseContext);

        numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });

      it('should create product and check the products number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}_create`, baseContext);

        await productsPage.goToAddProductPage(page);
        let createProductMessage = await addProductPage.createEditBasicProduct(
          page,
          test.args.productToCreate,
        );

        if (test.args.hasCombinations) {
          createProductMessage = await addProductPage.setAttributesInProduct(
            page,
            test.args.productToCreate,
          );
        }

        expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });

    describe('Check created product in monitoring page', async () => {
      it('should go to \'Catalog > Monitoring\' page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_goToMonitoringPage`,
          baseContext,
        );

        await addProductPage.goToSubMenu(
          page,
          addProductPage.catalogParentLink,
          addProductPage.monitoringLink,
        );

        const pageTitle = await monitoringPage.getPageTitle(page);
        expect(pageTitle).to.contains(monitoringPage.pageTitle);

        numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(
          page,
          test.args.gridName,
        );
        expect(numberOfProductsIngrid).to.be.at.least(1);
      });

      it(`should filter products ${test.args.productType} grid and check existence of new product`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_checkProduct`,
          baseContext,
        );

        await monitoringPage.filterTable(
          page,
          test.args.gridName,
          'input',
          'name',
          test.args.productToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(
          page,
          test.args.gridName,
          1,
          'name',
        );
        expect(textColumn).to.contains(test.args.productToCreate.name);
      });

      it(`should reset filter in products ${test.args.productType} grid`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_resetInMonitoringPage`,
          baseContext,
        );

        numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(page, test.args.gridName);
        expect(numberOfProductsIngrid).to.be.at.least(1);
      });
    });

    describe('Delete product from monitoring page', async () => {
      it(`should filter products ${test.args.productType} grid`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_filterToDelete`,
          baseContext,
        );

        await monitoringPage.filterTable(
          page,
          test.args.gridName,
          'input',
          'name',
          test.args.productToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(
          page,
          test.args.gridName,
          1,
          'name',
        );
        expect(textColumn).to.contains(test.args.productToCreate.name);
      });

      it('should delete product', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_deleteProduct`,
          baseContext,
        );

        const textResult = await monitoringPage.deleteProductInGrid(page, test.args.gridName, 1);
        expect(textResult).to.equal(productsPage.productDeletedSuccessfulMessage);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset filter check number of products', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `${test.args.testIdentifier}_resetInProductsPage`,
          baseContext,
        );

        const numberOfProductsAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts);
      });
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
