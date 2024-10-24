// Import utils
import testContext from '@utils/testContext';

// Import pages
import categoriesPage from '@pages/BO/catalog/categories';
import addCategoryPage from '@pages/BO/catalog/categories/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCategory,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext = 'functional_BO_catalog_categories_categoriesBulkActions';

// Create Categories, Then disable / Enable and Delete by Bulk actions
describe('BO - Catalog - Categories : Enable/Disable/Delete categories by Bulk Actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;

  const firstCategoryData: FakerCategory = new FakerCategory({name: 'todelete'});
  const secondCategoryData: FakerCategory = new FakerCategory({name: 'todeletetwo'});

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create categories images
    await Promise.all([
      utilsFile.generateImage(`${firstCategoryData.name}.jpg`),
      utilsFile.generateImage(`${secondCategoryData.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      utilsFile.deleteFile(`${firstCategoryData.name}.jpg`),
      utilsFile.deleteFile(`${secondCategoryData.name}.jpg`),
    ]);
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
    await categoriesPage.closeSfToolBar(page);

    const pageTitle = await categoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 2 categories In BO
  describe('Create 2 categories in BO', async () => {
    [
      {args: {categoryToCreate: firstCategoryData}},
      {args: {categoryToCreate: secondCategoryData}},
    ].forEach((test, index) => {
      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategoryPage${index + 1}`, baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create category and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index + 1}`, baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, test.args.categoryToCreate);
        expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + index + 1);
      });
    });
  });

  // 2 : Enable/Disable categories created by bulk actions
  describe('Enable and Disable categories by Bulk Actions', async () => {
    it('should filter list by Name \'todelete\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEditStatus', baseContext);

      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        'todelete',
      );

      const textResult = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textResult).to.contains('todelete');
    });

    [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ].forEach((test) => {
      it(`should ${test.args.action} categories`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategoryPage${test.args.action}`, baseContext);

        const textResult = await categoriesPage.bulkSetStatus(
          page,
          test.args.enabledValue,
        );
        expect(textResult).to.be.equal(categoriesPage.successfulUpdateStatusMessage);

        const numberOfCategoriesInGrid = await categoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesInGrid).to.be.at.most(numberOfCategories);

        for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
          const categoryStatus = await categoriesPage.getStatus(page, i);
          expect(categoryStatus).to.equal(test.args.enabledValue);
        }
      });
    });
  });

  // 3 : Delete Categories created with bulk actions
  describe('Delete categories by Bulk Actions', async () => {
    it('should delete categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await categoriesPage.deleteCategoriesBulkActions(page);
      expect(deleteTextResult).to.be.equal(categoriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
