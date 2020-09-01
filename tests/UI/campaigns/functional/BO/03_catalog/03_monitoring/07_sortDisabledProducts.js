require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const monitoringPage = require('@pages/BO/catalog/monitoring');

const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_monitoring_sortDisabledProducts';


let browserContext;
let page;
let numberOfProducts = 0;
let numberOfProductsIngrid = 0;
const firstProduct = new ProductFaker({type: 'Standard product', status: false});
const secondProduct = new ProductFaker({type: 'Standard product', status: false});
const thirdProduct = new ProductFaker({type: 'Standard product', status: false});

/*
Create 3 new disabled products
Sort list of disabled products in monitoring page
 */
describe('Sort list of disabled products', async () => {
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

  // 1 : Create 3 disabled products
  describe('Create 3 disabled products', async () => {
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

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.productsLink,
        );

        await dashboardPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset all filters and get number of products in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFirst${index}`, baseContext);
        numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProducts).to.be.above(0);
      });

      it('should create product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        await productsPage.goToAddProductPage(page);

        const createProductMessage = await addProductPage.createEditBasicProduct(
          page,
          test.args.productToCreate,
        );

        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });
  });

  // 2 : Sort disabled products
  describe('sort List of disabled products', async () => {
    it('should go to \'catalog > monitoring\' page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'goToMonitoringPage',
        baseContext,
      );

      await addProductPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.monitoringLink,
      );

      const pageTitle = await monitoringPage.getPageTitle(page);
      await expect(pageTitle).to.contains(monitoringPage.pageTitle);

      numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(
        page,
        'disabled_product',
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

          let nonSortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            'disabled_product',
            testSort.args.sortBy,
          );

          await monitoringPage.sortTable(
            page,
            'disabled_product',
            testSort.args.sortBy,
            testSort.args.sortDirection,
          );

          let sortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            'disabled_product',
            testSort.args.sortBy,
          );

          if (testSort.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await monitoringPage.sortArray(nonSortedTable, testSort.args.isFloat);

          if (testSort.args.sortDirection === 'asc') {
            await expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            await expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        },
      );
    });
  });

  // 3 : Delete the 3 created products
  describe('Delete created products', async () => {
    it('should filter products grid', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        'filterToDelete',
        baseContext,
      );

      await monitoringPage.filterTable(
        page,
        'disabled_product',
        'input',
        'name',
        firstProduct.name,
      );

      const textColumn = await monitoringPage.getTextColumnFromTable(
        page,
        'disabled_product',
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

      const textResult = await monitoringPage.deleteProductInGrid(page, 'disabled_product', 1);
      await expect(textResult).to.equal(productsPage.productDeletedSuccessfulMessage);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    const tests = [
      {args: {productToCreate: secondProduct}},
      {args: {productToCreate: thirdProduct}},
    ];

    tests.forEach((test, index) => {
      it('should delete the created product from products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `bulkDelete${index}`, baseContext);

        const deleteTextResult = await productsPage.deleteProduct(page, test.args.productToCreate);
        await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset filter and check number of products', async function () {
        await testContext.addContextItem(
          this,
          `testIdentifier${index}`,
          `resetInProductsPage${index}`,
          baseContext,
        );

        const numberOfProductsAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterDelete).to.be.equal(numberOfProducts - index - 1);
      });
    });
  });
});
