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
const productsPage = require('@pages/BO/catalog/products/index');
const addProductPage = require('@pages/BO/catalog/products/add');

// Import data
const ProductFaker = require('@data/faker/product');

const baseContext = 'functional_BO_catalog_products_paginationAndSortProducts';

let browserContext;
let page;
let numberOfProducts = 0;

/*
Create 3 products
Sort products table
Paginate between pages
Delete products with bulk actions
 */
describe('BO - Catalog - Products : Pagination and sort Products table', async () => {
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

  it('should go to \'Catalog > Products\' page', async function () {
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

  // 1 : Sort products list
  describe('Sort products table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'sortByIdAsc', sortBy: 'id_product', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc', isFloat: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc', isFloat: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByReferenceAsc', sortBy: 'reference', sortDirection: 'asc', isFloat: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByReferenceDesc', sortBy: 'reference', sortDirection: 'desc', isFloat: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByCategoryAsc', sortBy: 'name_category', sortDirection: 'asc', isFloat: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByCategoryDesc', sortBy: 'name_category', sortDirection: 'desc', isFloat: false,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByPriceAsc', sortBy: 'price', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByPriceDesc', sortBy: 'price', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByQuantityAsc', sortBy: 'sav_quantity', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByQuantityDesc', sortBy: 'sav_quantity', sortDirection: 'desc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortByIdDesc', sortBy: 'id_product', sortDirection: 'desc', isFloat: true,
          },
      },
    ];

    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await productsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await productsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await productsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await productsPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 2 : Create 3 new products
  const creationTests = new Array(3).fill(0, 0, 10);
  describe('Create 3 products in BO', async () => {
    creationTests.forEach((test, index) => {
      const createProductData = new ProductFaker({
        name: `todelete${index}`,
        type: 'Standard product',
        productHasCombinations: false,
      });

      it('should go to add product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

        await productsPage.goToAddProductPage(page);

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it(`should create product n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, createProductData);
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });

      it('should go to catalog page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCatalogPage${index}`, baseContext);

        await addProductPage.goToCatalogPage(page);
        const pageTitle = await productsPage.getPageTitle(page);
        await expect(pageTitle).to.contains(productsPage.pageTitle);
      });
    });
  });

  // 3 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo20', baseContext);

      const paginationNumber = await productsPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await productsPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await productsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await productsPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 4 : Delete the created products
  describe('Delete the created products', async () => {
    it('should filter by product name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByName', baseContext);

      // Filter by name
      await productsPage.filterProducts(page, 'name', 'todelete');

      const numberOfProductsAfterFilter = await productsPage.getNumberOfProductsFromList(page);
      await expect(numberOfProductsAfterFilter).to.equal(3);
    });

    it('should delete products by bulk actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await productsPage.deleteAllProductsWithBulkActions(page);
      await expect(deleteTextResult).to.equal(productsPage.productMultiDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersLast', baseContext);

      await productsPage.resetFilterCategory(page);
      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfProducts).to.be.above(0);
    });
  });
});
