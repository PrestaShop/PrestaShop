require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const monitoringPage = require('@pages/BO/catalog/monitoring');

// Import data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_catalog_monitoring_sortAndPagination_productsWithoutPrice';

let browserContext;
let page;

let numberOfProducts = 0;
let numberOfProductsIngrid = 0;
const tableName = 'product_without_price';

/*
Create 11 new products without price
Sort list of products without price in monitoring page
Pagination next and previous
 */
describe('BO - Catalog - Monitoring : Sort and pagination list of products without price', async () => {
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

  it('should go to \'catalog > products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.productsLink,
    );

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await productsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });

  it('should reset all filters and get number of products', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfProducts).to.be.above(0);
  });

  // 1 : Create 11 products products without price
  const creationTests = new Array(11).fill(0, 0, 11);
  describe('Create 11 products without price', async () => {
    creationTests.forEach((test, index) => {
      const createProductData = new ProductFaker({name: `todelete${index}`, type: 'Standard product', price: 0});
      it(`should create product n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        await productsPage.goToAddProductPage(page);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, createProductData);
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });

      it('should go to catalog page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCatalog${index}`, baseContext);

        await addProductPage.goToCatalogPage(page);

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });
    });
  });

  // 2 : Sort products without price
  describe('sort List of products without price', async () => {
    it('should go to \'catalog > monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

      await addProductPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.monitoringLink,
      );

      const pageTitle = await monitoringPage.getPageTitle(page);
      await expect(pageTitle).to.contains(monitoringPage.pageTitle);

      numberOfProductsIngrid = await monitoringPage.resetAndGetNumberOfLines(page, tableName);
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
            tableName,
            testSort.args.sortBy,
          );

          await monitoringPage.sortTable(
            page,
            tableName,
            testSort.args.sortBy,
            testSort.args.sortDirection,
          );

          let sortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            tableName,
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

  // 3 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo10', baseContext);

      const paginationNumber = await monitoringPage.selectPaginationLimit(page, tableName, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await monitoringPage.paginationNext(page, tableName);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await monitoringPage.paginationPrevious(page, tableName);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await monitoringPage.selectPaginationLimit(page, tableName, '20');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 4 : Delete the created products
  describe('Delete the created products without price', async () => {
    const deletionTests = new Array(11).fill(0, 0, 11);
    deletionTests.forEach((test, index) => {
      it('should filter list of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

        await monitoringPage.filterTable(page, tableName, 'input', 'name', 'toDelete');

        const textColumn = await monitoringPage.getTextColumnFromTable(page, tableName, 1, 'name');
        await expect(textColumn).to.contains('TODELETE');
      });

      it(`should delete product n°${index + 1} from monitoring page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        const textResult = await monitoringPage.deleteProductInGrid(page, tableName, 1);
        await expect(textResult).to.equal(productsPage.productDeletedSuccessfulMessage);

        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should reset filter and check number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete${index}`, baseContext);

        const numberOfCategoriesAfterDelete = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfProducts + 11 - index - 1);
      });

      it('should go to \'catalog > monitoring\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMonitoringPageToDelete${index}`, baseContext);

        await productsPage.goToSubMenu(
          page,
          dashboardPage.catalogParentLink,
          dashboardPage.monitoringLink,
        );

        const pageTitle = await monitoringPage.getPageTitle(page);
        await expect(pageTitle).to.contains(monitoringPage.pageTitle);
      });
    });
  });
});
