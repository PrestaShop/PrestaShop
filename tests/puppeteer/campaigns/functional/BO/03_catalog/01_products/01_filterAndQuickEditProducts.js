require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {Products} = require('@data/demo/products');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products/index');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_products_filterAndQuickEitProducts';

let browser;
let page;
let numberOfProducts = 0;
let filterValue = '';

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
  };
};

// Filter Products
describe('Filter Products', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to products page
  loginCommon.loginBO();

  it('should go to "Catalog>products" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters and get number of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);
    numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });
  // 1 : Filter products with all inputs and selects in grid table
  describe('Filter products', async () => {
    const tests = [
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
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: Products.demo_1.status,
          },
        expected: 'check',
      },
    ];
    tests.forEach((test) => {
      filterValue = test.args.filterValue.min === undefined ? `'${test.args.filterValue}`
        : `'${test.args.filterValue.min}-${test.args.filterValue.max}'`;
      it(`should filter by ${test.args.filterBy} ${filterValue}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);
        await this.pageObjects.productsPage.filterProducts(
          test.args.filterBy,
          test.args.filterValue,
          test.args.filterType,
        );
        const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
        await expect(numberOfProductsAfterFilter).to.within(0, numberOfProducts);
        for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
          const textColumn = await this.pageObjects.productsPage.getTextColumn(test.args.filterBy, i);
          if (test.expected !== undefined) {
            await expect(textColumn).to.equal(test.expected);
          } else if (test.args.filterValue.min !== undefined) {
            await expect(textColumn).to.within(test.args.filterValue.min, test.args.filterValue.max);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);
        const numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
      });
    });
  });

  // 2 : Editing products from table
  describe('Quick Edit products', async () => {
    it('should filter by Name \'Hummingbird printed sweater\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);
      await this.pageObjects.productsPage.filterProducts('name', Products.demo_3.name);
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
      for (let i = 1; i <= numberOfProductsAfterFilter; i++) {
        const textColumn = await this.pageObjects.productsPage.getProductNameFromList(i);
        await expect(textColumn).to.contains(Products.demo_3.name);
      }
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];
    statuses.forEach((productStatus) => {
      it(`should ${productStatus.args.status} the product`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${productStatus.args.status}Product`, baseContext);
        const isActionPerformed = await this.pageObjects.productsPage.updateToggleColumnValue(
          1,
          productStatus.args.enable,
        );
        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.productsPage.getTextContent(
            this.pageObjects.productsPage.alertSuccessBlockParagraph,
          );
          if (productStatus.args.enable) {
            await expect(resultMessage).to.contains(this.pageObjects.productsPage.productActivatedSuccessfulMessage);
          } else {
            await expect(resultMessage).to.contains(this.pageObjects.productsPage.productDeactivatedSuccessfulMessage);
          }
        }
        const isStatusChanged = await this.pageObjects.productsPage.getToggleColumnValue(1);
        await expect(isStatusChanged).to.be.equal(productStatus.args.enable);
      });
    });
  });
});
