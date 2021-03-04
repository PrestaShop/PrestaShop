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

// Import data
const CategoryFaker = require('@data/faker/category');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_categoriesBulkActions';


let browserContext;
let page;
let numberOfCategories = 0;

const firstCategoryData = new CategoryFaker({name: 'todelete'});
const secondCategoryData = new CategoryFaker({name: 'todeletetwo'});

// Create Categories, Then disable / Enable and Delete with Bulk actions
describe('Create Categories, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create categories images
    await Promise.all([
      files.generateImage(`${firstCategoryData.name}.jpg`),
      files.generateImage(`${secondCategoryData.name}.jpg`),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      files.deleteFile(`${firstCategoryData.name}.jpg`),
      files.deleteFile(`${secondCategoryData.name}.jpg`),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to "Catalog>Categories" page', async function () {
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

  it('should reset all filters and get Number of Categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create 2 categories In BO
  describe('Create 2 categories in BO', async () => {
    const tests = [
      {args: {categoryToCreate: firstCategoryData}},
      {args: {categoryToCreate: secondCategoryData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategoryPage${index + 1}`, baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create category and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCategory${index + 1}`, baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, test.args.categoryToCreate);
        await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + index + 1);
      });
    });
  });

  // 2 : Enable/Disable categories created with bulk actions
  describe('Enable and Disable categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkEditStatus', baseContext);

      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        'todelete',
      );

      const textResult = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      await expect(textResult).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCategoryPage${test.args.action}`, baseContext);

        const textResult = await categoriesPage.bulkSetStatus(
          page,
          test.args.enabledValue,
        );

        await expect(textResult).to.be.equal(categoriesPage.successfulUpdateStatusMessage);

        const numberOfCategoriesInGrid = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesInGrid).to.be.at.most(numberOfCategories);

        for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
          const categoryStatus = await categoriesPage.getStatus(page, i);
          await expect(categoryStatus).to.equal(test.args.enabledValue);
        }
      });
    });
  });

  // 3 : Delete Categories created with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
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

    it('should delete categories with Bulk Actions and check Result', async function () {
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
