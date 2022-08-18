require('module-alias/register');
// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');
const {enableNewProductPageTest, disableNewProductPageTest} = require('@commonTests/BO/advancedParameters/newFeatures');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/productsV2');

// Import data
const {Products} = require('@data/demo/products');
const {Categories} = require('@data/demo/categories');

const baseContext = 'productV2_sanity_filterProducts';

let browserContext;
let page;
let numberOfProducts = 0;

describe('BO - Catalog - Products : Filter in Products Page', async () => {
  // Pre-condition: Enable new product page
  enableNewProductPageTest(`${baseContext}_enableNewProduct`);

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

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should check that no filter is applied by default', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoFilter', baseContext);

      const isVisible = await productsPage.isResetButtonVisible(page);
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

        const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
        if (test.args.filterBy === 'active') {
          await expect(numberOfProductsAfterFilter).to.be.above(0);
        } else {
          await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
        }

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await productsPage.getTextColumn(page, test.args.filterBy, i);

          if (test.args.filterBy === 'id_product' || test.args.filterBy === 'price'
            || test.args.filterBy === 'quantity') {
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

        const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });

  // Post-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);
});
