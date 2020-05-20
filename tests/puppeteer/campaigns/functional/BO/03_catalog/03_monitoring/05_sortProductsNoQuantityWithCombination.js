require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const MonitoringPage = require('@pages/BO/catalog/monitoring');
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_monitoring_sortProductsNoQuantityWithCombination';

let browser;
let page;
let numberOfProducts = 0;
let numberOfProductsIngrid = 0;
const firstProduct = new ProductFaker({type: 'Standard product', quantity: 0, status: false});
const secondProduct = new ProductFaker({type: 'Standard product', quantity: 0});
const thirdProduct = new ProductFaker({type: 'Standard product', quantity: 0});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    monitoringPage: new MonitoringPage(page),
  };
};

/*
Create 3 new products with combinations but without available quantities
Sort list of products with combinations but without available quantities in monitoring page
 */
describe('Sort list of products with combinations but without available quantities', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  // 1 : Create 3 products with combinations but without available quantities
  describe('Create 3 products with combinations but without available quantities', async () => {
    const tests = [
      {args: {productToCreate: firstProduct}},
      {args: {productToCreate: secondProduct}},
      {args: {productToCreate: thirdProduct}},
    ];

    tests.forEach((test, index) => {
      it('should go to \'catalog > products\' page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `goToProductsPage${index}`,
          baseContext,
        );

        await this.pageObjects.dashboardPage.goToSubMenu(
          this.pageObjects.dashboardPage.catalogParentLink,
          this.pageObjects.dashboardPage.productsLink,
        );

        await this.pageObjects.dashboardPage.closeSfToolBar();

        const pageTitle = await this.pageObjects.productsPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
      });

      it('should reset all filters and get number of products in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFirst${index}`, baseContext);

        numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should create product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        await this.pageObjects.productsPage.goToAddProductPage();

        await this.pageObjects.addProductPage.createEditBasicProduct(test.args.productToCreate);
        const createProductMessage = await this.pageObjects.addProductPage.setCombinationsInProduct(
          test.args.productToCreate,
        );

        await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
      });
    });
  });

  // 2 : Sort products without image table
  describe('sort List of products without image in monitoring page', async () => {
    it('should go to \'catalog > monitoring\' page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToMonitoringPage',
        baseContext,
      );

      await this.pageObjects.addProductPage.goToSubMenu(
        this.pageObjects.dashboardPage.catalogParentLink,
        this.pageObjects.dashboardPage.monitoringLink,
      );

      const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);

      numberOfProductsIngrid = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines(
        'no_qty_product_with_combination',
      );

      await expect(numberOfProductsIngrid).to.be.at.least(1);
    });

    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_product', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByReferenceDesc', sortBy: 'reference', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByReferenceAsc', sortBy: 'reference', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_product', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((testSort) => {
      it(
        `should sort by '${testSort.args.sortBy}' '${testSort.args.sortDirection}' and check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', testSort.args.testIdentifier, baseContext);

          let nonSortedTable = await this.pageObjects.monitoringPage.getAllRowsColumnContent(
            'no_qty_product_with_combination',
            testSort.args.sortBy,
          );

          await this.pageObjects.monitoringPage.sortTable(
            'no_qty_product_with_combination',
            testSort.args.sortBy,
            testSort.args.sortDirection,
          );

          let sortedTable = await this.pageObjects.monitoringPage.getAllRowsColumnContent(
            'no_qty_product_with_combination',
            testSort.args.sortBy,
          );

          if (testSort.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await this.pageObjects.monitoringPage.sortArray(nonSortedTable, testSort.args.isFloat);

          if (testSort.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 3 : Delete the 3 created products with combinations but without available quantities
  describe('Delete created products', async () => {
    it('should filter products grid', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'filterToDelete',
        baseContext,
      );

      await this.pageObjects.monitoringPage.filterTable(
        'no_qty_product_with_combination',
        'input',
        'name',
        firstProduct.name,
      );

      const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable(
        'no_qty_product_with_combination',
        1,
        'name',
      );

      await expect(textColumn).to.contains(firstProduct.name);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'deleteProduct',
        baseContext,
      );

      const textResult = await this.pageObjects.monitoringPage.deleteProductInGrid(
        'no_qty_product_with_combination',
        1,
      );

      await expect(textResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);

      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    const tests = [
      {args: {productToCreate: secondProduct}},
      {args: {productToCreate: thirdProduct}},
    ];

    tests.forEach((test, index) => {
      it('should delete the created product from products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulkDelete${index}`, baseContext);

        const deleteTextResult = await this.pageObjects.productsPage.deleteProduct(test.args.productToCreate);
        await expect(deleteTextResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset filter and check number of products', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `resetInProductsPage${index}`,
          baseContext,
        );

        const numberOfProductsAfterDelete = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
        await expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts - index - 1);
      });
    });
  });
});
