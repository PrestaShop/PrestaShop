// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import categoriesPage from '@pages/BO/catalog/categories';
import addCategoryPage from '@pages/BO/catalog/categories/add';
import monitoringPage from '@pages/BO/catalog/monitoring';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import CategoryData from '@data/faker/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_monitoring_deleteEmptyCategory';

/*
Create new category
Check existence of new category in monitoring page
Delete category and check deletion in categories page
 */
describe('BO - Catalog - Monitoring : Create empty category and delete it from monitoring page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;
  let numberOfEmptyCategories: number = 0;

  const createCategoryData: CategoryData = new CategoryData();

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
    expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  describe('Create empty category in BO', async () => {
    it('should go to add new category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCategoryPage', baseContext);

      await categoriesPage.goToAddNewCategoryPage(page);

      const pageTitle = await addCategoryPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
    });

    it('should create category and check the categories number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

      const textResult = await addCategoryPage.createEditCategory(page, createCategoryData);
      expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

      const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
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
      expect(pageTitle).to.contains(monitoringPage.pageTitle);

      numberOfEmptyCategories = await monitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      expect(numberOfEmptyCategories).to.be.at.least(1);
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
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should reset filter in empty categories grid', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetInMonitoringPage', baseContext);

      numberOfEmptyCategories = await monitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      expect(numberOfEmptyCategories).to.be.at.least(1);
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
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await monitoringPage.deleteCategoryInGrid(page, 'empty_category', 1, 1);
      expect(textResult).to.equal(monitoringPage.successfulDeleteMessage);

      const pageTitle = await categoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should reset filter check number of categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterDelete = await categoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories);
    });
  });
});
