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
const MonitoringPage = require('@pages/BO/catalog/monitoring');
const CategoryFaker = require('@data/faker/category');

let browser;
let page;
let numberOfCategories = 0;
let numberOfEmptyCategories = 0;
const createCategoryData = new CategoryFaker();

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
    addCategoryPage: new AddCategoryPage(page),
    monitoringPage: new MonitoringPage(page),
  };
};

/*
Create new category
Check existence of new category in monitoring page
Delete category and check deletion in category page
 */
describe('Create empty category and delete it from monitoring page', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
    /* Delete the generated image */
    await files.deleteFile(`${createCategoryData.name}.jpg`);
  });
  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to catalog > categories page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.categoriesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    numberOfCategories = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
    await expect(numberOfCategories).to.be.above(0);
  });

  describe('Create Category and subcategory in BO and check it in FO', async () => {
    it('should go to add new category page', async function () {
      await this.pageObjects.categoriesPage.goToAddNewCategoryPage();
      const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
    });

    it('should create category and check the categories number', async function () {
      const textResult = await this.pageObjects.addCategoryPage.createEditCategory(createCategoryData);
      await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);
      const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberOfElementInGrid();
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });
  });

  describe('Check created category in monitoring page', async () => {
    it('should go to catalog > monitoring page', async function () {
      await this.pageObjects.categoriesPage.goToSubMenu(
        this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.monitoringLink,
      );
      const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);
      numberOfEmptyCategories = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines('empty_category');
      await expect(numberOfEmptyCategories).to.be.at.least(1);
    });

    it('should filter categories grid and existence of new category', async function () {
      await this.pageObjects.monitoringPage.filterTable(
        'empty_category',
        'input',
        'name',
        createCategoryData.name,
      );
      const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable('empty_category', 1, 'name');
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should reset filter in empty categories grid', async function () {
      numberOfEmptyCategories = await this.pageObjects.monitoringPage.resetAndGetNumberOfLines('empty_category');
      await expect(numberOfEmptyCategories).to.be.at.least(1);
    });
  });

  describe('Delete category from monitoring page', async () => {
    it('should filter categories grid', async function () {
      await this.pageObjects.monitoringPage.filterTable(
        'empty_category',
        'input',
        'name',
        createCategoryData.name,
      );
      const textColumn = await this.pageObjects.monitoringPage.getTextColumnFromTable('empty_category', 1, 'name');
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should delete category', async function () {
      const textResult = await this.pageObjects.monitoringPage.deleteCategoryInGrid('empty_category', 1, 1);
      await expect(textResult).to.equal(this.pageObjects.monitoringPage.successfulDeleteMessage);
      const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
    });

    it('should reset filter check number of categories', async function () {
      const numberOfCategoriesAfterDelete = await this.pageObjects.categoriesPage.resetAndGetNumberOfLines();
      await expect(numberOfCategoriesAfterDelete).to.be.equal(numberOfCategories);
    });
  });
});
