// Import utils
import testContext from '@utils/testContext';

// Import pages
// Import BO pages
import categoriesPage from '@pages/BO/catalog/categories';
import editCategoryPage from '@pages/BO/catalog/categories/add';
// Import FO pages
import {siteMapPage} from '@pages/FO/classic/siteMap';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCategories,
  FakerCategory,
  foClassicHomePage,
  foClassicCategoryPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_categories_editHomeCategory';

// Edit home category
describe('BO - Catalog - Categories : Edit home category', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let categoryID: number;

  const editCategoryData: FakerCategory = new FakerCategory({name: 'Home'});

  // before and after functions
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
    await foClassicHomePage.changeLanguage(page, 'en');

    const isHomePage = await foClassicHomePage.isHomePage(page);
    expect(isHomePage, 'Fail to open FO home page').to.eq(true);

    // Go to sitemap page
    await foClassicHomePage.goToFooterLink(page, 'Sitemap');

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

    const textResult = await editCategoryPage.editHomeCategory(page, dataCategories.home);
    expect(textResult).to.equal(categoriesPage.pageRootTitle);
  });
});
