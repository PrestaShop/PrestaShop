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
const monitoringPage = require('@pages/BO/catalog/monitoring');

// Import data
const CategoryFaker = require('@data/faker/category');

const baseContext = 'functional_BO_catalog_monitoring_sortAndPagination_emptyCategories';

let browserContext;
let page;
let numberOfCategories = 0;
let numberOfEmptyCategories = 0;
const tableName = 'empty_category';

/*
Create 11 new categories
Sort list of empty categories
Pagination next and previous
 */
describe('BO - Catalog - Monitoring : Sort and pagination list of empty categories', async () => {
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

  it('should go to \'catalog > categories\' page', async function () {
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

  // 1 : Create 11 categories
  const creationTests = new Array(11).fill(0, 0, 11);
  describe('Create 11 categories in BO', async () => {
    creationTests.forEach((test, index) => {
      const createCategoryData = new CategoryFaker({name: `todelete${index + 1}`});
      before(() => files.generateImage(`todelete${index + 1}.jpg`));

      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategoryPage${index}`, baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it(`should create category n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index}`, baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, createCategoryData);
        await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
      });

      after(() => files.deleteFile(`todelete${index + 1}.jpg`));
    });
  });

  // 2 : Sort empty categories list
  describe('Sort list of empty categories', async () => {
    it('should go to \'catalog > monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPageToSort', baseContext);

      await categoriesPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.monitoringLink,
      );

      const pageTitle = await monitoringPage.getPageTitle(page);
      await expect(pageTitle).to.contains(monitoringPage.pageTitle);

      numberOfEmptyCategories = await monitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      await expect(numberOfEmptyCategories).to.be.at.least(1);
    });

    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_category', sortDirection: 'desc', isFloat: true,
        },
      },
      {args: {testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByDescriptionDesc', sortBy: 'description', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortByDescriptionAsc', sortBy: 'description', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledAsc', sortBy: 'active', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortByEnabledDesc', sortBy: 'active', sortDirection: 'desc'}},
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_category', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(
        `should sort empty categories table by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          let nonSortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            tableName,
            test.args.sortBy,
          );

          await monitoringPage.sortTable(page, 'empty_category', test.args.sortBy, test.args.sortDirection);

          let sortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            tableName,
            test.args.sortBy,
          );

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await monitoringPage.sortArray(nonSortedTable, test.args.isFloat);

          if (test.args.sortDirection === 'asc') {
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

  // 4 : Delete the created categories
  describe('Delete the created categories from monitoring page', async () => {
    const deletionTests = new Array(11).fill(0, 0, 11);
    deletionTests.forEach((test, index) => {
      it('should filter list of empty categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

        await monitoringPage.filterTable(page, tableName, 'input', 'name', `todelete${index + 1}`);

        const textColumn = await monitoringPage.getTextColumnFromTable(page, tableName, 1, 'name');
        await expect(textColumn).to.contains(`todelete${index + 1}`);
      });

      it(`should delete category n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCategory${index + 1}`, baseContext);

        const textResult = await monitoringPage.deleteCategoryInGrid(page, tableName, 1, 1);
        await expect(textResult).to.equal(monitoringPage.successfulDeleteMessage);

        const pageTitle = await categoriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(categoriesPage.pageTitle);
      });

      it('should reset filter and check number of empty categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete${index}`, baseContext);

        const numberOfCategoriesAfterDelete = await categoriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories + 11 - index - 1);
      });

      it('should go to \'catalog > monitoring\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToMonitoringPageToDelete${index}`, baseContext);

        await categoriesPage.goToSubMenu(
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
