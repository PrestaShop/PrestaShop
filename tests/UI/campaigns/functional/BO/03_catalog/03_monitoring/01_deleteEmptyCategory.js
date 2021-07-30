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

const baseContext = 'functional_BO_catalog_monitoring_deleteEmptyCategory';

let browserContext;
let page;

let numberOfCategories = 0;
let numberOfEmptyCategories = 0;

const createCategoryData = new CategoryFaker();

/*
Create new category
Check existence of new category in monitoring page
Delete category and check deletion in categories page
 */
describe('BO - Catalog - Monitoring : Create empty category and delete it from monitoring page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create category image
    await files.generateImage(`${createCategoryData.name}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated image */
    await files.deleteFile(`${createCategoryData.name}.jpg`);
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

    await categoriesPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCategories).to.be.above(0);
  });

  describe('Create empty category in BO', async () => {
    it('should go to add new category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCategoryPage', baseContext);

      await categoriesPage.goToAddNewCategoryPage(page);
      const pageTitle = await addCategoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
    });

    it('should create category and check the categories number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

      const textResult = await addCategoryPage.createEditCategory(page, createCategoryData);
      await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

      const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });
  });

  describe('Check created category in monitoring page', async () => {
    it('should go to \'Catalog > Monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

      await categoriesPage.goToSubMenu(
        page,
        categoriesPage.catalogParentLink,
        categoriesPage.monitoringLink,
      );

      const pageTitle = await monitoringPage.getPageTitle(page);
      await expect(pageTitle).to.contains(monitoringPage.pageTitle);

      numberOfEmptyCategories = await monitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      await expect(numberOfEmptyCategories).to.be.at.least(1);
    });

    it(`should filter categories by Name ${createCategoryData.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategory', baseContext);

      await monitoringPage.filterTable(
        page,
        'empty_category',
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await monitoringPage.getTextColumnFromTable(page, 'empty_category', 1, 'name');
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should reset filter in empty categories grid', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetInMonitoringPage', baseContext);

      numberOfEmptyCategories = await monitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      await expect(numberOfEmptyCategories).to.be.at.least(1);
    });
  });

  describe('Delete category from monitoring page', async () => {
    it(`should filter categories by Name ${createCategoryData.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmptyCategoriesGridToDelete', baseContext);

      await monitoringPage.filterTable(
        page,
        'empty_category',
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await monitoringPage.getTextColumnFromTable(page, 'empty_category', 1, 'name');
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await monitoringPage.deleteCategoryInGrid(page, 'empty_category', 1, 1);
      await expect(textResult).to.equal(monitoringPage.successfulDeleteMessage);

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should reset filter check number of categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterDelete = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories);
    });
  });
});
