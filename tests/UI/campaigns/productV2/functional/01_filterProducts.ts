import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
import {ProductFilterMinMax} from '@data/types/product';

const baseContext: string = 'productV2_functional_filterProducts';

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
          identifier: 'filterIDMinUpperMax',
          filterBy: 'id_product',
          filterValue: {min: 10, max: 5},
          filterType: 'input',
          alertDanger: productsPage.alertDangerIDFilterValue,
        },
      },
      {
        args: {
          identifier: 'filterPriceMinUpperMax',
          filterBy: 'price',
          filterValue: {min: 15, max: 10},
          filterType: 'input',
          alertDanger: productsPage.alertDangerPriceFilterValue,
        },
      },
      {
        args: {
          identifier: 'filterQuantityMinUpperMax',
          filterBy: 'quantity',
          filterValue: {min: 500, max: 100},
          filterType: 'input',
          alertDanger: productsPage.alertDangerQuantityFilterValue,
        },
      },
    ].forEach((test) => {
      it(`should filter list by '${test.args.filterBy}' min upper than max and check error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

        await productsPage.filterProducts(page, test.args.filterBy, test.args.filterValue, test.args.filterType);

        const textMessage = await productsPage.getAlertDangerBlockContent(page);
        await expect(textMessage).to.equal(test.args.alertDanger);
      });
    });

    [
      {
        args: {
          identifier: 'filterIDMinMax',
          filterBy: 'id_product',
          filterValue: {min: 5, max: 10} as ProductFilterMinMax,
          filterType: 'input',
          comparisonType: 'toWithinMinMax',
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
          filterValue: {min: 5, max: 10} as ProductFilterMinMax,
          filterType: 'input',
          comparisonType: 'toWithinMinMax',
        },
      },
      {
        args: {
          identifier: 'filterQuantityMinMax',
          filterBy: 'quantity',
          filterValue: {min: 100, max: 1000} as ProductFilterMinMax,
          filterType: 'input',
          comparisonType: 'toWithinMinMax',
        },
      },
      {
        args: {
          identifier: 'filterStatusYes',
          filterBy: 'active',
          filterValue: 'Yes',
          filterType: 'select',
          comparisonType: 'toBeTrue',
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

          switch (test.args.comparisonType) {
            case 'toWithinMinMax':
              await expect(typeof test.args.filterValue).to.be.eq('object');
              if (typeof test.args.filterValue !== 'string') {
                await expect(textColumn).to.within(test.args.filterValue.min, test.args.filterValue.max);
              }
              break;

            case 'toBeTrue':
              await expect(textColumn).to.be.true;
              break;

            default:
              await expect(textColumn).to.contain(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetAfter${test.args.identifier}`, baseContext);

        const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });

    it('should filter list by \'Status\' No and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByStatusNo', baseContext);

      await productsPage.filterProducts(page, 'active', 'No', 'select');

      const textColumn = await productsPage.getTextForEmptyTable(page);
      await expect(textColumn).to.equal('warning No records found');
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterByStatus', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });

  describe('Filter products table by : Category and Position', async () => {
    it('should filter by category \'Home\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCategories', baseContext);

      await productsPage.filterProductsByCategory(page, 'Home');

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
    });

    it('should check the filter by category button name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFilterButtonName', baseContext);

      const filterButtonName = await productsPage.getFilterByCategoryButtonName(page);
      await expect(filterButtonName).to.equal('Filter by categories (Home)');
    });

    it('should check that the \'Clear filter\' link is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkClearFilterLink', baseContext);

      const isVisible = await productsPage.isClearFilterLinkVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should check that the new column \'Position\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPositionColumn', baseContext);

      const isVisible = await productsPage.isPositionColumnVisible(page);
      await expect(isVisible).to.be.true;
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterByPosition', baseContext);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should click on \'Clear filter\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnClearFilterButton', baseContext);

      await productsPage.clickOnClearFilterLink(page);

      const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should check that the \'Clear filter\' link is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkClearFilterLinkNotVisible', baseContext);

      const isVisible = await productsPage.isClearFilterLinkVisible(page);
      await expect(isVisible).to.be.false;
    });

    it('should check that the new column \'Position\' is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPositionColumnNotVisible', baseContext);

      const isVisible = await productsPage.isPositionColumnVisible(page);
      await expect(isVisible).to.be.false;
    });
  });
});
