import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCategories,
  FakerCategory,
  foClassicHomePage,
  foClassicCategoryPage,
  foClassicSitemapPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_categories_editHomeCategory';

describe('BO - Catalog - Categories : Edit home category', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let categoryID: number;

  const editCategoryData: FakerCategory = new FakerCategory({name: 'Home 123'});

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create category image
    await utilsFile.generateImage(`${editCategoryData.name}.jpg`);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(`${editCategoryData.name}.jpg`);
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

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage1', baseContext);

    await boCategoriesPage.goToEditHomeCategoryPage(page);

    const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleEdit);
  });

  it('should update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'updateCategory', baseContext);

    categoryID = await boCategoriesCreatePage.getIDCategory(page);

    const textResult = await boCategoriesCreatePage.editHomeCategory(page, editCategoryData);
    expect(textResult).to.equal(boCategoriesPage.pageTitle);
  });

  it('should go to FO and check the updated category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);
    // View Shop
    page = await boCategoriesPage.viewMyShop(page);
    // Change FO language
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);

    // Go to sitemap page
    await foClassicHomePage.goToFooterLink(page, 'Sitemap');

    const pageTitle = await foClassicSitemapPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

    // Check category name
    const categoryName = await foClassicSitemapPage.getCategoryName(page, categoryID);
    expect(categoryName).to.contains(editCategoryData.name);
  });

  it('should view the created category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedCategoryFO', baseContext);

    await foClassicSitemapPage.viewCreatedCategory(page, categoryID);

    // Check category name
    const pageTitle = await foClassicCategoryPage.getHeaderPageName(page);
    expect(pageTitle).to.contains(editCategoryData.name.toUpperCase());

    // Check category description
    const categoryDescription = await foClassicCategoryPage.getCategoryDescription(page);
    expect(categoryDescription).to.equal(editCategoryData.description);
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo', baseContext);

    // Close tab and init other page objects with new current tab
    page = await foClassicCategoryPage.closePage(browserContext, page, 0);

    const pageTitle = await boCategoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
  });

  it('should go to Edit Home category page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEditHomePage2', baseContext);

    await boCategoriesPage.goToEditHomeCategoryPage(page);

    const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
    expect(pageTitle).to.contains(editCategoryData.name);
  });

  it('should reset update the category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetUpdateCategory', baseContext);

    const textResult = await boCategoriesCreatePage.editHomeCategory(page, dataCategories.home);
    expect(textResult).to.equal(boCategoriesPage.pageTitle);
  });
});
