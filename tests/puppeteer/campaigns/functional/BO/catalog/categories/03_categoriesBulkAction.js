require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CategoriesPage = require('@pages/BO/categories');
const AddCategoryPage = require('@pages/BO/addCategory');
const CategoryFaker = require('@data/faker/category');

let browser;
let page;
let numberOfCategories = 0;
let firstCategoryData;
let secondCategoryData;

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
    firstCategoryData = await (new CategoryFaker({name: 'todelete'}));
    secondCategoryData = await (new CategoryFaker({name: 'todelete'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
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
    it('should go to add new category page', async function () {
      await this.pageObjects.categoriesPage.clickAndWaitForNavigation(
        this.pageObjects.categoriesPage.addNewCategoryLink,
      );
      const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
    });

    it('should create first category and check result', async function () {
      const textResult = await this.pageObjects.addCategoryPage.createEditCategory(firstCategoryData);
      await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);
      const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle,
      );
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });

    it('should go to add new category page', async function () {
      await this.pageObjects.categoriesPage.clickAndWaitForNavigation(
        this.pageObjects.categoriesPage.addNewCategoryLink,
      );
      const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
    });

    it('should create second category and check result', async function () {
      const textResult = await this.pageObjects.addCategoryPage.createEditCategory(secondCategoryData);
      await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);
      const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 2);
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
      const textResult = await this.pageObjects.categoriesPage.getTextContent(
        this.pageObjects.categoriesPage.categoriesListTableColumn
          .replace('%ROW', '1')
          .replace('%COLUMN', 'name')
        ,
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should disable categories with Bulk Actions and check Result', async function () {
      const disableTextResult = await this.pageObjects.categoriesPage.changeCategoriesEnabledColumnBulkActions(false);
      await expect(disableTextResult).to.be.equal(this.pageObjects.categoriesPage.successfulUpdateStatusMessage);
      const numberOfCategoriesInGrid = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesInGrid).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn
            .replace('%ROW', i)
            .replace('%COLUMN', 'active')
          ,
        );
        await expect(textColumn).to.contains('clear');
      }
      /* eslint-enable no-await-in-loop */
    });

    it('should enable categories with Bulk Actions and check Result', async function () {
      const enableTextResult = await this.pageObjects.categoriesPage.changeCategoriesEnabledColumnBulkActions(true);
      await expect(enableTextResult).to.be.equal(this.pageObjects.categoriesPage.successfulUpdateStatusMessage);
      const numberOfCategoriesInGrid = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle,
      );
      await expect(numberOfCategoriesInGrid).to.be.at.most(numberOfCategories);
      /* eslint-disable no-await-in-loop */
      for (let i = 1; i <= numberOfCategoriesInGrid; i++) {
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn
            .replace('%ROW', i)
            .replace('%COLUMN', 'active')
          ,
        );
        await expect(textColumn).to.contains('check');
      }
      /* eslint-enable no-await-in-loop */
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
      const textResult = await this.pageObjects.categoriesPage.getTextContent(
        this.pageObjects.categoriesPage.categoriesListTableColumn
          .replace('%ROW', '1')
          .replace('%COLUMN', 'name')
        ,
      );
      await expect(textResult).to.contains('todelete');
    });

    it('should delete categories with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.categoriesPage.deleteCategoriesBulkActions();
      await expect(deleteTextResult).to.be.equal(this.pageObjects.categoriesPage.successfulMultiDeleteMessage);
      /* Delete the generated images */
      await this.pageObjects.categoriesPage.deleteFile(`${firstCategoryData.name}.jpg`);
    });

    it('should reset all filters', async function () {
      const numberOfCategoriesAfterReset = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
