require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CategoriesPage = require('@pages/BO/catalog/categories');
const AddCategoryPage = require('@pages/BO/catalog/categories/add');
const CategoryFaker = require('@data/faker/category');

let browser;
let page;
let numberOfCategories = 0;
const firstCategoryData = new CategoryFaker({name: 'todelete'});
const secondCategoryData = new CategoryFaker({name: 'todeletetwo'});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
    addCategoryPage: new AddCategoryPage(page),
  };
};

// Create Categories, Then disable / Enable and Delete with Bulk actions
describe('Create Categories, Then disable / Enable and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    /* Delete the generated images */
    await Promise.all([
      files.deleteFile(`${firstCategoryData.name}.jpg`),
      files.deleteFile(`${secondCategoryData.name}.jpg`),
    ]);
  });
  // Login into BO and go to Categories page
  loginCommon.loginBO();

  it('should go to "Catalog>Categories" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.categoriesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should reset all filters and get Number of Categories in BO', async function () {
    numberOfCategories = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
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
        await this.pageObjects.categoriesPage.clickAndWaitForNavigation(
          this.pageObjects.categoriesPage.addNewCategoryLink,
        );
        const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
      });

      it('should create category and check result', async function () {
        const textResult = await this.pageObjects.addCategoryPage.createEditCategory(test.args.categoryToCreate);
        await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);
        const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + index + 1);
      });
    });
  });
  // 2 : Enable/Disable categories created with bulk actions
  describe('Enable and Disable categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        'todelete',
      );
      const textResult = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(1, 'name');
      await expect(textResult).to.contains('todelete');
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}, expected: 'clear'},
      {args: {action: 'enable', enabledValue: true}, expected: 'check'},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} with bulk actions and check Result`, async function () {
        const textResult = await this.pageObjects.categoriesPage.changeCategoriesEnabledColumnBulkActions(
          test.args.enabledValue,
        );
        await expect(textResult).to.be.equal(this.pageObjects.categoriesPage.successfulUpdateStatusMessage);
        const numberOfCategoriesInGrid = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
        await expect(numberOfCategoriesInGrid).to.be.at.most(numberOfCategories);
        for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
          const textColumn = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(1, 'active');
          await expect(textColumn).to.contains(test.expected);
        }
      });
    });
  });
  // 3 : Delete Categories created with bulk actions
  describe('Delete categories with Bulk Actions', async () => {
    it('should filter list by Name', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        'todelete',
      );
      const textResult = await this.pageObjects.categoriesPage.getTextColumnFromTableCategories(1, 'name');
      await expect(textResult).to.contains('todelete');
    });

    it('should delete categories with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.categoriesPage.deleteCategoriesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.categoriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
