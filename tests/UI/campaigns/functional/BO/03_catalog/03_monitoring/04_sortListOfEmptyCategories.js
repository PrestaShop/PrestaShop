require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CategoriesPage = require('@pages/BO/catalog/categories');
const AddCategoryPage = require('@pages/BO/catalog/categories/add');
const MonitoringPage = require('@pages/BO/catalog/monitoring');
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

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
    addCategoryPage: new AddCategoryPage(page),
    monitoringPage: new MonitoringPage(page),
  };
};

/*
Create new category
Sort list of empty categories
 */
describe('Sort list of empty categories', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated image */
    await files.deleteFile(`${firstCreateCategoryData.name}.jpg`);
    await files.deleteFile(`${secondCreateCategoryData.name}.jpg`);
    await files.deleteFile(`${thirdCreateCategoryData.name}.jpg`);
  });

  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to \'catalog > categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.categoriesLink,
    );

    await this.pageObjects.dashboardPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
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

        await this.pageObjects.categoriesPage.goToAddNewCategoryPage();
        const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
      });

      it('should create category and check the  number of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index}`, baseContext);

        const textResult = await this.pageObjects.addCategoryPage.createEditCategory(test.args.categoryToCreate);
        await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
      });
    });
  });

  // 2 : Sort empty categories list
  describe('Sort list of empty categories', async () => {
    it('should go to \'catalog > monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPageToSort', baseContext);

      await this.pageObjects.categoriesPage.goToSubMenu(
        this.pageObjects.dashboardPage.catalogParentLink,
        this.pageObjects.dashboardPage.monitoringLink,
      );

      const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);
      numberOfEmptyCategories = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines('empty_category');
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

          let nonSortedTable = await this.pageObjects.monitoringPage.getAllRowsColumnContent(
            'empty_category',
            test.args.sortBy,
          );

          await this.pageObjects.monitoringPage.sortTable('empty_category', test.args.sortBy, test.args.sortDirection);

          let sortedTable = await this.pageObjects.monitoringPage.getAllRowsColumnContent(
            'empty_category',
            test.args.sortBy,
          );

          if (test.args.isFloat) {
            nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
            sortedTable = await sortedTable.map(text => parseFloat(text));
          }

          const expectedResult = await this.pageObjects.monitoringPage.sortArray(nonSortedTable, test.args.isFloat);

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

        await this.pageObjects.monitoringPage.filterTable(
          'empty_category',
          'input',
          'name',
          test.args.categoryToCreate.name,
        );

        const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable('empty_category', 1, 'name');
        await expect(textColumn).to.contains(test.args.categoryToCreate.name);
      });

      it('should delete category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteCategory${index}`, baseContext);

        const textResult = await this.pageObjects.monitoringPage.deleteCategoryInGrid('empty_category', 1, 1);
        await expect(textResult).to.equal(this.pageObjects.monitoringPage.successfulDeleteMessage);

        const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
      });

      it('should reset filter and check number of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete${index}`, baseContext);

        const numberOfCategoriesAfterDelete = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
        await expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories + 3 - index - 1);
      });

      it('should go to \'catalog > monitoring\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPageToDelete', baseContext);

        await this.pageObjects.categoriesPage.goToSubMenu(
          this.pageObjects.dashboardPage.catalogParentLink,
          this.pageObjects.dashboardPage.monitoringLink,
        );

        const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);
      });
    });
  });
});
