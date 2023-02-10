// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {
  disableNewProductPageTest,
  resetNewProductPageAsDefault,
} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/products';

// Import data
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_products_filterAndQuickEditProducts';

// Filter and quick edit Products
describe('BO - Catalog - Products : Filter and quick edit Products table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfProducts: number = 0;
  let filterValue: string = '';

  // Pre-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('Get the number of products', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > products\' page', async function () {
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
  });

  // 1 : Filter products with all inputs and selects in grid table
  describe('Filter products table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_product',
            filterValue: {min: Products.demo_1.id, max: Products.demo_6.id},
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: Products.demo_14.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterReference',
            filterType: 'input',
            filterBy: 'reference',
            filterValue: Products.demo_3.reference,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCategoryName',
            filterType: 'input',
            filterBy: 'name_category',
            filterValue: Products.demo_5.category,
          },
      },
      {
        args: {
          testIdentifier: 'filterPrice',
          filterType: 'input',
          filterBy: 'price',
          filterValue: {min: Products.demo_1.price, max: Products.demo_3.price},
        },
      },
      {
        args: {
          testIdentifier: 'filterQuantity',
          filterType: 'input',
          filterBy: 'sav_quantity',
          filterValue: {min: Products.demo_6.quantity, max: Products.demo_1.quantity},
        },
      },

      {
        args:
          {
            testIdentifier: 'filterStatus',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Products.demo_1.status ? '1' : '0',
          },
      },
    ].forEach((test) => {
      filterValue = typeof test.args.filterValue !== 'object' || test.args.filterValue.min === undefined
        ? `'${test.args.filterValue}'`
        : `'${test.args.filterValue.min}-${test.args.filterValue.max}'`;

      it(`should filter by ${test.args.filterBy} ${filterValue}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await productsPage.filterProducts(
          page,
          test.args.filterBy,
          test.args.filterValue,
          test.args.filterType,
        );

        const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
        await expect(numberOfProductsAfterFilter).to.within(0, numberOfProducts);

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const productStatus = await productsPage.getProductStatusFromList(page, i);
            await expect(productStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await productsPage.getTextColumn(page, test.args.filterBy, i);

            if (typeof test.args.filterValue === 'object') {
              await expect(parseFloat(textColumn)).to.within(test.args.filterValue.min, test.args.filterValue.max);
            } else {
              await expect(textColumn).to.contains(test.args.filterValue);
            }
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });

  // 2 : Editing products from table
  describe('Quick edit products table', async () => {
    it('should filter by Name \'Hummingbird printed sweater\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await productsPage.filterProducts(page, 'name', Products.demo_3.name);

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);

      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await productsPage.getProductNameFromList(page, i);
        await expect(textColumn).to.contains(Products.demo_3.name);
      }
    });

    [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ].forEach((productStatus) => {
      it(`should ${productStatus.args.status} the product`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${productStatus.args.status}Product`, baseContext);

        const isActionPerformed = await productsPage.setProductStatus(
          page,
          1,
          productStatus.args.enable,
        );

        if (isActionPerformed) {
          const resultMessage = await productsPage.getAlertSuccessBlockParagraphContent(page);

          if (productStatus.args.enable) {
            await expect(resultMessage).to.contains(productsPage.productActivatedSuccessfulMessage);
          } else {
            await expect(resultMessage).to.contains(productsPage.productDeactivatedSuccessfulMessage);
          }
        }

        const currentStatus = await productsPage.getProductStatusFromList(page, 1);
        await expect(currentStatus).to.be.equal(productStatus.args.enable);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });

  // Post-condition: Reset initial state
  resetNewProductPageAsDefault(`${baseContext}_resetNewProduct`);
});
