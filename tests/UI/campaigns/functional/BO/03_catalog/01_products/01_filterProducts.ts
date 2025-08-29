import {expect} from 'chai';

// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  dataProducts,
  dataCategories,
  type Page,
  type ProductFilterMinMax,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_filterProducts';

describe('BO - Catalog - Products : Filter in Products Page', async () => {
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

  describe('Filter products table : Go to BO', async () => {
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

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should check that no filter is applied by default', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNoFilter', baseContext);

      const isVisible = await boProductsPage.isResetButtonVisible(page);
      expect(isVisible, 'Reset button is visible!').to.eq(false);
    });

    it('should get number of products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfProduct', baseContext);

      numberOfProducts = await boProductsPage.getNumberOfProductsFromHeader(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });

  describe('Filter products table by : ID, Name, Reference, Category, Price, Quantity and Status', async () => {
    [
      {
        args: {
          identifier: 'filterIDMinUpperMax',
          filterBy: 'id_product',
          filterValue: {min: 10, max: 5},
          filterType: 'input',
          alertDanger: boProductsPage.alertDangerIDFilterValue,
        },
      },
      {
        args: {
          identifier: 'filterPriceMinUpperMax',
          filterBy: 'price',
          filterValue: {min: 15, max: 10},
          filterType: 'input',
          alertDanger: boProductsPage.alertDangerPriceFilterValue,
        },
      },
      {
        args: {
          identifier: 'filterQuantityMinUpperMax',
          filterBy: 'quantity',
          filterValue: {min: 500, max: 100},
          filterType: 'input',
          alertDanger: boProductsPage.alertDangerQuantityFilterValue,
        },
      },
    ].forEach((test) => {
      it(`should filter list by '${test.args.filterBy}' min upper than max and check error message`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

        await boProductsPage.filterProducts(page, test.args.filterBy, test.args.filterValue, test.args.filterType);

        const textMessage = await boProductsPage.getAlertDangerBlockContent(page);
        expect(textMessage).to.equal(test.args.alertDanger);
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
          filterValue: dataProducts.demo_14.name,
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterReference',
          filterBy: 'reference',
          filterValue: dataProducts.demo_1.reference,
          filterType: 'input',
        },
      },
      {
        args: {
          identifier: 'filterCategory',
          filterBy: 'category',
          filterValue: dataCategories.women.name,
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

        await boProductsPage.filterProducts(page, test.args.filterBy, test.args.filterValue, test.args.filterType);

        const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);

        if (test.args.filterBy === 'active') {
          expect(numberOfProductsAfterFilter).to.be.above(0);
        } else {
          expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
        }

        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await boProductsPage.getTextColumn(page, test.args.filterBy, i);

          switch (test.args.comparisonType) {
            case 'toWithinMinMax':
              expect(typeof test.args.filterValue).to.be.eq('object');
              if (typeof test.args.filterValue !== 'string') {
                expect(textColumn).to.within(test.args.filterValue.min, test.args.filterValue.max);
              }
              break;

            case 'toBeTrue':
              expect(textColumn).to.eq(true);
              break;

            default:
              expect(textColumn).to.contain(test.args.filterValue);
          }
        }
      });

      it('should reset filter', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetAfter${test.args.identifier}`, baseContext);

        const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });

    it('should filter list by \'Status\' No and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByStatusNo', baseContext);

      await boProductsPage.filterProducts(page, 'active', 'No', 'select');

      const textColumn = await boProductsPage.getTextForEmptyTable(page);
      expect(textColumn).to.equal('warning No records found');
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterByStatus', baseContext);

      const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });

  describe('Filter products table by : Category and Position', async () => {
    it('should filter by category \'Home\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCategories', baseContext);

      await boProductsPage.filterProductsByCategory(page, 'Home');

      const numberOfProductsAfterFilter = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numberOfProductsAfterFilter).to.equal(numberOfProducts);
    });

    it('should check the filter by category button name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFilterButtonName', baseContext);

      const filterButtonName = await boProductsPage.getFilterByCategoryButtonName(page);
      expect(filterButtonName).to.equal('Filter by categories (Home)');
    });

    it('should check that the \'Clear filter\' link is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkClearFilterLink', baseContext);

      const isVisible = await boProductsPage.isClearFilterLinkVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should check that the new column \'Position\' is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPositionColumn', baseContext);

      const isVisible = await boProductsPage.isPositionColumnVisible(page);
      expect(isVisible).to.eq(true);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterByPosition', baseContext);

      const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should click on \'Clear filter\' button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnClearFilterButton', baseContext);

      await boProductsPage.clickOnClearFilterLink(page);

      const numberOfProductsAfterReset = await boProductsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });

    it('should check that the \'Clear filter\' link is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkClearFilterLinkNotVisible', baseContext);

      const isVisible = await boProductsPage.isClearFilterLinkVisible(page);
      expect(isVisible).to.eq(false);
    });

    it('should check that the new column \'Position\' is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPositionColumnNotVisible', baseContext);

      const isVisible = await boProductsPage.isPositionColumnVisible(page);
      expect(isVisible).to.eq(false);
    });
  });
});
