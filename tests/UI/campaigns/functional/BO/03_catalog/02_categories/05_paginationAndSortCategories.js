require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');
const addCategoryPage = require('@pages/BO/catalog/categories/add');

// Import data
const CategoryFaker = require('@data/faker/category');

const baseContext = 'functional_BO_catalog_categories_paginationAndSortCategories';

let browserContext;
let page;
let numberOfCategories = 0;

/*
Create 11 categories
Paginate between pages
Sort categories table
Delete categories with bulk actions
 */
describe('BO - Catalog - Categories : Pagination and sort categories table', async () => {
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

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.categoriesLink,
    );

    await dashboardPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 11 new categories
  describe('Create 11 categories in BO', async () => {
    const creationTests = new Array(10).fill(0, 0, 10);
    creationTests.forEach((test, index) => {
      before(() => files.generateImage(`${createCategoryData.name}.jpg`));

      const createCategoryData = new CategoryFaker({name: `todelete${index}`});

      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewCategoryPage${index}`, baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);
        const pageTitle = await addCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it(`should create category n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index}`, baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, createCategoryData);
        await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
      });

      after(() => files.deleteFile(`${createCategoryData.name}.jpg`));
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo10', baseContext);

      const paginationNumber = await categoriesPage.selectPaginationLimit(page, '10');
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await categoriesPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await categoriesPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemNumberTo50', baseContext);

      const paginationNumber = await categoriesPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  const tests = [
    {
      args:
        {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_category', sortDirection: 'desc', isFloat: true,
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
          testIdentifier: 'sortByPositionDesc', sortBy: 'position', sortDirection: 'desc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByPositionAsc', sortBy: 'position', sortDirection: 'asc', isFloat: true,
        },
    },
    {
      args:
        {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_category', sortDirection: 'asc', isFloat: true,
        },
    },
  ];

  // 3 : Sort categories
  describe('Sort categories table', async () => {
    tests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await categoriesPage.getAllRowsColumnContent(page, test.args.sortBy);
        await categoriesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await categoriesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await categoriesPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'asc') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete categories created by bulk actions
  describe('Delete categories by Bulk Actions', async () => {
    it('should filter list by Name \'todelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDelete', baseContext);

      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        'todelete',
      );

      const textResult = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete categories and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await categoriesPage.deleteCategoriesBulkActions(page);
      await expect(deleteTextResult).to.be.equal(categoriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
