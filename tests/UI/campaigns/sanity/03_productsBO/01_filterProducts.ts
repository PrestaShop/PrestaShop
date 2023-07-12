// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/productsV2';

// Import data
import Products from '@data/demo/products';
import Categories from '@data/demo/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'productV2_sanity_filterProducts';

describe('BO - Catalog - Products : Filter in Products Page', async () => {
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

  describe('Filter products table by : ID, Name, Reference, Category, Price, Quantity and Status', async () => {
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

      const pageTitle: string = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should check that no filter is applied by default', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoFilter', baseContext);

      const isVisible: boolean = await productsPage.isResetButtonVisible(page);
      await expect(isVisible, 'Reset button is visible!').to.be.false;
    });

    it('should get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await productsPage.getNumberOfProductsFromHeader(page);
      await expect(numberOfProducts).to.be.above(0);
    });

    [
      {
        args: {
          identifier: 'filterIDMinMax',
          filterBy: 'id_product',
          filterValue: {min: 5, max: 10},
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterName',
          filterBy: 'product_name',
          filterValue: Products.demo_14.name,
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterReference',
          filterBy: 'reference',
          filterValue: Products.demo_1.reference,
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterCategory',
          filterBy: 'category',
          filterValue: Categories.women.name,
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterPriceMinMax',
          filterBy: 'price',
          filterValue: {min: 5, max: 10},
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterQuantityMinMax',
          filterBy: 'quantity',
          filterValue: {min: 100, max: 1000},
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterStatus',
          filterBy: 'active',
          filterValue: 'Yes',
          filterType: 'select',
        },
      },
    ].forEach((test) => {
      it(`should filter list by '${test.args.filterBy}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.identifier}`, baseContext);

        await productsPage.filterProducts(page, test.args.filterBy, test.args.filterValue, test.args.filterType);

        const numberOfProductsAfterFilter: number = await productsPage.getNumberOfProductsFromList(page);

        if (test.args.filterBy === 'active') {
          await expect(numberOfProductsAfterFilter).to.be.above(0);
        } else {
          await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
        }

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await productsPage.getTextColumn(page, test.args.filterBy, i);

          if (typeof test.args.filterValue !== 'string') {
            await expect(textColumn).to.within(test.args.filterValue.min, test.args.filterValue.max);
          } else if (test.args.filterBy === 'active') {
            await expect(textColumn).to.be.true;
          } else {
            await expect(textColumn).to.be.contain(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetAfter${test.args.identifier}`, baseContext);

        const numberOfProductsAfterReset: number = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });
});
