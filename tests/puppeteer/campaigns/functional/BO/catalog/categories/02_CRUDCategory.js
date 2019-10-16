// Using chai
const {expect} = require('chai');
const helper = require('../../../../utils/helpers');
const loginCommon = require('../../../../commonTests/loginBO');
// Importing pages
const BOBasePage = require('../../../../../pages/BO/BObasePage');
const LoginPage = require('../../../../../pages/BO/login');
const DashboardPage = require('../../../../../pages/BO/dashboard');
const CategoriesPage = require('../../../../../pages/BO/categories');
const AddCategoryPage = require('../../../../../pages/BO/addCategory');
const FOBasePage = require('../../../../../pages/FO/FObasePage');
const SiteMapPage = require('../../../../../pages/FO/siteMap');
const CategoryFaker = require('../../../../data/faker/category');

let browser;
let page;
let numberOfCategories = 0;
let createCategoryData;
let createSubCategoryData;
let editCategoryData;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
    addCategoryPage: new AddCategoryPage(page),
    foBasePage: new FOBasePage(page),
    siteMapPage: new SiteMapPage(page),
  };
};

// Create, Read, Update and Delete Category
describe('Create, Read, Update and Delete Category', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    createCategoryData = await (new CategoryFaker());
    createSubCategoryData = await (new CategoryFaker());
    editCategoryData = await (new CategoryFaker({displayed: 'No'}));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to Categories page
  loginCommon.loginBO();
  it('should go to "Catalog>Categories" page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.productsParentLink,
      this.pageObjects.boBasePage.categoriesLink);
    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });
  it('should reset all filters and get Number of Categories in BO', async function () {
    if (await this.pageObjects.categoriesPage.elementVisible(
      this.pageObjects.categoriesPage.filterResetButton,
      2000)) {
      await this.pageObjects.categoriesPage.resetFilter();
    }
    numberOfCategories = await this.pageObjects.categoriesPage.getNumberFromText(
      this.pageObjects.categoriesPage.categoryGridTitle);
    await expect(numberOfCategories).to.be.above(0);
  });

  describe('Create Category and subcategory in BO and check it in FO', async () => {
    describe('Create Category and check it in FO', async () => {
      it('should go to add new category page', async function () {
        await this.pageObjects.categoriesPage.goToAddNewCategoryPage();
        const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
      });
      it('should create category and check the new categories number', async function () {
        const textResult = await this.pageObjects.addCategoryPage.createEditCategory(createCategoryData);
        await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);
        const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberFromText(
          this.pageObjects.categoriesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
      });
      it('should search for the new category and check result', async function () {
        await this.pageObjects.categoriesPage.filterCategories(
          'input',
          'name',
          createCategoryData.name,
        );
        const numberOfCategoriesAfterFilter = await this.pageObjects.categoriesPage.getNumberFromText(
          this.pageObjects.categoriesPage.categoryGridTitle);
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);
        /* eslint-disable no-await-in-loop */
        for (let i = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await this.pageObjects.categoriesPage.getTextContent(
            this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', i).replace('%COLUMN', 'name'),
          );
          await expect(textColumn).to.contains(createCategoryData.name);
        }
        /* eslint-enable no-await-in-loop */
      });
      it('should go to FO and check the created category', async function () {
        const categoryID = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace(
            '%COLUMN',
            'id_category'),
        );
        page = await this.pageObjects.boBasePage.viewMyShop();
        this.pageObjects = await init();
        await this.pageObjects.foBasePage.changeLanguage('en');
        await this.pageObjects.foBasePage.goToPage(this.pageObjects.foBasePage.siteMapLink);
        const pageTitle = await this.pageObjects.siteMapPage.getPageTitle();
        await expect(pageTitle).to.equal(this.pageObjects.siteMapPage.pageTitle);
        const categoryName = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.siteMapPage.categoryNameSelect.replace('%ID', categoryID));
        await expect(categoryName).to.contains(createCategoryData.name);
        page = await this.pageObjects.foBasePage.closePage(browser, 1);
        this.pageObjects = await init();
      });
    });
    // test related to the bug described in this issue https://github.com/PrestaShop/PrestaShop/issues/15588
    describe('Create SubCategory and check it in FO', async () => {
      it('should display the subcategories table', async function () {
        await this.pageObjects.categoriesPage.goToSubCategoryPage('1');
        const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
        await expect(pageTitle).to.contains(createCategoryData.name);
      });
      it('should go to add new category page', async function () {
        await this.pageObjects.categoriesPage.goToAddNewCategoryPage();
        const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleCreate);
      });
      it('should create a subcategory', async function () {
        const textResult = await this.pageObjects.addCategoryPage.createEditCategory(createSubCategoryData);
        await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulCreationMessage);
      });
      it.skip('should search for the subcategory and check result #15588', async function () {
        await this.pageObjects.categoriesPage.filterCategories(
          'input',
          'name',
          createSubCategoryData.name,
        );
        const textColumn = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace('%COLUMN', 'name'),
        );
        await expect(textColumn).to.contains(createSubCategoryData.name);
      });
      it.skip('should go to FO and check the created SubCategory', async function () {
        const categoryID = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace(
            '%COLUMN',
            'id_category'),
        );
        page = await this.pageObjects.boBasePage.viewMyShop();
        this.pageObjects = await init();
        await this.pageObjects.foBasePage.changeLanguage('en');
        await this.pageObjects.foBasePage.goToPage(this.pageObjects.foBasePage.siteMapLink);
        const pageTitle = await this.pageObjects.siteMapPage.getPageTitle();
        await expect(pageTitle).to.equal(this.pageObjects.siteMapPage.pageTitle);
        const categoryName = await this.pageObjects.categoriesPage.getTextContent(
          this.pageObjects.siteMapPage.categoryNameSelect.replace('%ID', categoryID));
        await expect(categoryName).to.contains(createSubCategoryData.name);
        page = await this.pageObjects.foBasePage.closePage(browser, 1);
        this.pageObjects = await init();
      });
    });
  });

  describe('Update Category created', async () => {
    it('should go to Catalog>Categories page and check for the created category', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.productsParentLink,
        this.pageObjects.boBasePage.categoriesLink);
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        createCategoryData.name,
      );
      const textColumn = await this.pageObjects.categoriesPage.getTextContent(
        this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(createCategoryData.name);
    });
    it('should go to edit category page', async function () {
      await this.pageObjects.categoriesPage.goToEditCategoryPage('1');
      const pageTitle = await this.pageObjects.addCategoryPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addCategoryPage.pageTitleEdit + createCategoryData.name);
    });
    it('should disable the category', async function () {
      const textResult = await this.pageObjects.addCategoryPage.createEditCategory(editCategoryData);
      await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulUpdateMessage);
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterUpdate = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterUpdate).to.be.equal(numberOfCategories + 1);
    });
    it('should search for the new category and check result', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        editCategoryData.name,
      );
      const textColumn = await this.pageObjects.categoriesPage.getTextContent(
        this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(editCategoryData.name);
    });
    it('should go to FO and check that the category does not exist', async function () {
      const categoryID = await this.pageObjects.categoriesPage.getTextContent(
        this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace(
          '%COLUMN',
          'id_category'),
      );
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.goToPage(this.pageObjects.foBasePage.siteMapLink);
      const pageTitle = await this.pageObjects.siteMapPage.getPageTitle();
      await expect(pageTitle).to.equal(this.pageObjects.siteMapPage.pageTitle);
      const categoryName = await this.pageObjects.categoriesPage.elementVisible(
        this.pageObjects.siteMapPage.categoryNameSelect.replace('%ID', categoryID));
      await expect(categoryName).to.be.false;
      page = await this.pageObjects.foBasePage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });

  describe('Delete Category', async () => {
    it('should go to "Catalog>Categories" page', async function () {
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.productsParentLink,
        this.pageObjects.boBasePage.categoriesLink);
      const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
    });
    it('should filter list by name', async function () {
      await this.pageObjects.categoriesPage.filterCategories(
        'input',
        'name',
        editCategoryData.name,
      );
      const textColumn = await this.pageObjects.categoriesPage.getTextContent(
        this.pageObjects.categoriesPage.categoriesListTableColumn.replace('%ROW', 1).replace('%COLUMN', 'name'),
      );
      await expect(textColumn).to.contains(editCategoryData.name);
    });
    it('should delete category', async function () {
      const textResult = await this.pageObjects.categoriesPage.deleteCategory('1');
      await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulDeleteMessage);
      await this.pageObjects.categoriesPage.resetFilter();
      const numberOfCategoriesAfterCreation = await this.pageObjects.categoriesPage.getNumberFromText(
        this.pageObjects.categoriesPage.categoryGridTitle);
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories);
    });
  });
});
