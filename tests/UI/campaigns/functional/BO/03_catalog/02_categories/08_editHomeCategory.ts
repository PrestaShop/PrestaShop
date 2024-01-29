// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import categoriesPage from '@pages/BO/catalog/categories';
import editCategoryPage from '@pages/BO/catalog/categories/add';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import categoryPage from '@pages/FO/classic/category';
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {siteMapPage} from '@pages/FO/classic/siteMap';

// Import data
import Categories from '@data/demo/categories';
import CategoryData from '@data/faker/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_categories_editHomeCategory';

// Edit home category
describe('BO - Catalog - Categories : Edit home category', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let categoryID: number;

  const editCategoryData: CategoryData = new CategoryData({name: 'Home'});

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
    expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage1', baseContext);

    await categoriesPage.goToEditHomeCategoryPage(page);

    const pageTitle = await editCategoryPage.getPageTitle(page);
    expect(pageTitle).to.contains(editCategoryPage.pageTitleEdit);
  });

  it('should update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCategory', baseContext);

    const textResult = await editCategoryPage.editHomeCategory(page, editCategoryData);
    expect(textResult).to.equal(categoriesPage.pageRootTitle);
  });

  it('should go to FO and check the updated category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);

    categoryID = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
    // View Shop
    page = await categoriesPage.viewMyShop(page);
    // Change FO language
    await foHomePage.changeLanguage(page, 'en');

    const isHomePage = await foHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);

    // Go to sitemap page
    await foHomePage.goToFooterLink(page, 'Sitemap');

    const pageTitle = await siteMapPage.getPageTitle(page);
    expect(pageTitle).to.equal(siteMapPage.pageTitle);

    // Check category name
    const categoryName = await siteMapPage.getCategoryName(page, categoryID);
    expect(categoryName).to.contains(editCategoryData.name);
  });

  it('should view the created category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedCategoryFO', baseContext);

    await siteMapPage.viewCreatedCategory(page, categoryID);

    // Check category name
    const pageTitle = await categoryPage.getHeaderPageName(page);
    expect(pageTitle).to.contains(editCategoryData.name.toUpperCase());

    // Check category description
    const categoryDescription = await categoryPage.getCategoryDescription(page);
    expect(categoryDescription).to.equal(editCategoryData.description);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await categoryPage.closePage(browserContext, page, 0);

    const pageTitle = await categoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(categoriesPage.pageRootTitle);
  });

  it('should click on view category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToViewCreatedCategoryPage', baseContext);

    await categoriesPage.goToViewSubCategoriesPage(page, 1);

    const pageTitle = await categoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(categoriesPage.pageTitle);
  });

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage2', baseContext);

    await categoriesPage.goToEditHomeCategoryPage(page);

    const pageTitle = await editCategoryPage.getPageTitle(page);
    expect(pageTitle).to.contains(editCategoryData.name);
  });

  it('should reset update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetUpdateCategory', baseContext);

    const textResult = await editCategoryPage.editHomeCategory(page, Categories.home);
    expect(textResult).to.equal(categoriesPage.pageRootTitle);
  });
});
