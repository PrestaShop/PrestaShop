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
const EditCategoryPage = require('@pages/BO/catalog/categories/add');

// Import data
const CategoryFaker = require('@data/faker/category');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_categories_editHomeCategory';

let browser;
let page;
const editCategoryData = new CategoryFaker({name: 'Home'});

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    categoriesPage: new CategoriesPage(page),
    editCategoryPage: new EditCategoryPage(page),
  };
};

// Edit home category
describe('Edit home category', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
    await files.deleteFile(`${editCategoryData.name}.jpg`);
  });

  // Login into BO and go to categories page
  loginCommon.loginBO();

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.categoriesLink,
    );

    await this.pageObjects.categoriesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.categoriesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.categoriesPage.pageTitle);
  });

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage', baseContext);

    await this.pageObjects.categoriesPage.goToEditHomeCategoryPage();
    const pageTitle = await this.pageObjects.editCategoryPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.editCategoryPage.pageTitleEdit);
  });

  it('should update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCategory', baseContext);

    const textResult = await this.pageObjects.editCategoryPage.editHomeCategory(editCategoryData);
    await expect(textResult).to.equal(this.pageObjects.categoriesPage.successfulUpdateMessage);
  });
});
