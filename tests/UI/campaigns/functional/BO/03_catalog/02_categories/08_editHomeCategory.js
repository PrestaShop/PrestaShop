// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const files = require('@utils/files');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const categoriesPage = require('@pages/BO/catalog/categories');
const editCategoryPage = require('@pages/BO/catalog/categories/add');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const siteMapPage = require('@pages/FO/siteMap');
const categoryPage = require('@pages/FO/category');

// Import data
const CategoryFaker = require('@data/faker/category');
const {Categories} = require('@data/demo/categories');

const baseContext = 'functional_BO_catalog_categories_editHomeCategory';

let browserContext;
let page;

let categoryID;
const editCategoryData = new CategoryFaker({name: 'Home'});

// Edit home category
describe('BO - Catalog - Categories : Edit home category', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create category image
    await files.generateImage(`${editCategoryData.name}.jpg`);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(`${editCategoryData.name}.jpg`);
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

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage1', baseContext);

    await categoriesPage.goToEditHomeCategoryPage(page);
    const pageTitle = await editCategoryPage.getPageTitle(page);
    await expect(pageTitle).to.contains(editCategoryPage.pageTitleEdit);
  });

  it('should update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCategory', baseContext);

    const textResult = await editCategoryPage.editHomeCategory(page, editCategoryData);
    await expect(textResult).to.equal(categoriesPage.pageRootTitle);
  });

  it('should go to FO and check the updated category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);

    categoryID = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category');

    // View Shop
    page = await categoriesPage.viewMyShop(page);

    // Change FO language
    await foHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage, 'Fail to open FO home page').to.be.true;

    // Go to sitemap page
    await foHomePage.goToFooterLink(page, 'Sitemap');
    const pageTitle = await siteMapPage.getPageTitle(page);
    await expect(pageTitle).to.equal(siteMapPage.pageTitle);

    // Check category name
    const categoryName = await siteMapPage.getCategoryName(page, categoryID);
    await expect(categoryName).to.contains(editCategoryData.name);
  });

  it('should view the created category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedCategoryFO', baseContext);

    await siteMapPage.viewCreatedCategory(page, categoryID);

    // Check category name
    const pageTitle = await categoryPage.getHeaderPageName(page);
    await expect(pageTitle).to.contains(editCategoryData.name.toUpperCase());

    // Check category description
    const categoryDescription = await categoryPage.getCategoryDescription(page);
    await expect(categoryDescription).to.equal(editCategoryData.description);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await categoryPage.closePage(browserContext, page, 0);

    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageRootTitle);
  });

  it('should click on view category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToViewCreatedCategoryPage', baseContext);

    await categoriesPage.goToViewSubCategoriesPage(page, 1);
    const pageTitle = await categoriesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage2', baseContext);

    await categoriesPage.goToEditHomeCategoryPage(page);
    const pageTitle = await editCategoryPage.getPageTitle(page);
    await expect(pageTitle).to.contains(editCategoryData.name);
  });

  it('should reset update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetUpdateCategory', baseContext);

    const textResult = await editCategoryPage.editHomeCategory(page, Categories.home);
    await expect(textResult).to.equal(categoriesPage.pageRootTitle);
  });
});
