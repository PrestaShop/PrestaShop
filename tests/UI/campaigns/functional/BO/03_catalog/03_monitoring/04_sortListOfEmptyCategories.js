require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');
const addCategoryPage = require('@pages/BO/catalog/categories/add');
const monitoringPage = require('@pages/BO/catalog/monitoring');

const CategoryFaker = require('@data/faker/category');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_monitoring_sortListOfEmptyCategories';

let browserContext;
let page;
let numberOfCategories = 0;
let numberOfEmptyCategories = 0;
const firstCreateCategoryData = new CategoryFaker({displayed: false});
const secondCreateCategoryData = new CategoryFaker();
const thirdCreateCategoryData = new CategoryFaker();

/*
Create new category
Sort list of empty categories
 */
describe('Sort list of empty categories', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create categories images
    await Promise.all([
      files.generateImage(`${firstCreateCategoryData.name}.jpg`),
      files.generateImage(`${secondCreateCategoryData.name}.jpg`),
      files.generateImage(`${thirdCreateCategoryData.name}.jpg`),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated image */
    await files.deleteFile(`${firstCreateCategoryData.name}.jpg`);
    await files.deleteFile(`${secondCreateCategoryData.name}.jpg`);
    await files.deleteFile(`${thirdCreateCategoryData.name}.jpg`);
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

  // 1 : Create 3 empty categories
  describe('Create 3 empty categories in BO', async () => {
    const tests = [
      {args: {categoryToCreate: firstCreateCategoryData}},
      {args: {categoryToCreate: secondCreateCategoryData}},
      {args: {categoryToCreate: thirdCreateCategoryData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategoryPage${index}`, baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);
        const pageTitle = await addCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create category and check the  number of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index}`, baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, test.args.categoryToCreate);
        await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
      });
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
        `should sort empty categories by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          let nonSortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            'empty_category',
            test.args.sortBy,
          );

          await monitoringPage.sortTable(page, 'empty_category', test.args.sortBy, test.args.sortDirection);

          let sortedTable = await monitoringPage.getAllRowsColumnContent(
            page,
            'empty_category',
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

  // 3 : Delete the 3 created categories
  describe('Delete the created 3 categories from monitoring page', async () => {
    const tests = [
      {args: {categoryToCreate: firstCreateCategoryData}},
      {args: {categoryToCreate: secondCreateCategoryData}},
      {args: {categoryToCreate: thirdCreateCategoryData}},
    ];

    tests.forEach((test, index) => {
      it('should filter categories grid', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDelete${index}`, baseContext);

        await monitoringPage.filterTable(
          page,
          'empty_category',
          'input',
          'name',
          test.args.categoryToCreate.name,
        );

        const textColumn = await monitoringPage.getTextColumnFromTable(page, 'empty_category', 1, 'name');
        await expect(textColumn).to.contains(test.args.categoryToCreate.name);
      });

      it('should delete category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCategory${index}`, baseContext);

        const textResult = await monitoringPage.deleteCategoryInGrid(page, 'empty_category', 1, 1);
        await expect(textResult).to.equal(monitoringPage.successfulDeleteMessage);

        const pageTitle = await categoriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(categoriesPage.pageTitle);
      });

      it('should reset filter and check number of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete${index}`, baseContext);

        const numberOfCategoriesAfterDelete = await categoriesPage.resetAndGetNumberOfLines(page);
        await expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories + 3 - index - 1);
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
