import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boLoginPage,
  boMonitoringPage,
  type BrowserContext,
  FakerCategory,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const createCategoryData: FakerCategory = new FakerCategory();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create category image
    await utilsFile.generateImage(`${createCategoryData.name}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated image */
    await utilsFile.deleteFile(`${createCategoryData.name}.jpg`);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.categoriesLink,
    );
    await boCategoriesPage.closeSfToolBar(page);

    const pageTitle = await boCategoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  describe('Create empty category in BO', async () => {
    it('should go to add new category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddCategoryPage', baseContext);

      await boCategoriesPage.goToAddNewCategoryPage(page);

      const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
    });

    it('should create category and check the categories number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

      const textResult = await boCategoriesCreatePage.createEditCategory(page, createCategoryData);
      expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);

      const numberOfCategoriesAfterCreation = await boCategoriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });
  });

  describe('Check created category in monitoring page', async () => {
    it('should go to \'Catalog > Monitoring\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

      await boCategoriesPage.goToSubMenu(
        page,
        boCategoriesPage.catalogParentLink,
        boCategoriesPage.monitoringLink,
      );

      const pageTitle = await boMonitoringPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMonitoringPage.pageTitle);

      numberOfEmptyCategories = await boMonitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      expect(numberOfEmptyCategories).to.be.at.least(1);
    });

    it(`should filter categories by Name ${createCategoryData.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategory', baseContext);

      await boMonitoringPage.filterTable(
        page,
        'empty_category',
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await boMonitoringPage.getTextColumnFromTable(page, 'empty_category', 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should reset filter in empty categories grid', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetInMonitoringPage', baseContext);

      numberOfEmptyCategories = await boMonitoringPage.resetAndGetNumberOfLines(page, 'empty_category');
      expect(numberOfEmptyCategories).to.be.at.least(1);
    });
  });

  describe('Delete category from monitoring page', async () => {
    it(`should filter categories by Name ${createCategoryData.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterEmptyCategoriesGridToDelete', baseContext);

      await boMonitoringPage.filterTable(
        page,
        'empty_category',
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await boMonitoringPage.getTextColumnFromTable(page, 'empty_category', 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await boMonitoringPage.deleteCategoryInGrid(page, 'empty_category', 1, 1);
      expect(textResult).to.equal(boMonitoringPage.successfulDeleteMessage);

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
    });

    it('should reset filter check number of categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCategoriesAfterDelete = await boCategoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories);
    });
  });
});
