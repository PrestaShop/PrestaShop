require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

const CategoryPageFaker = require('@data/faker/CMScategory');
const PageFaker = require('@data/faker/CMSpage');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard/index');
const pagesPage = require('@pages/BO/design/pages/index');
const addPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');
const addPagePage = require('@pages/BO/design/pages/add');
const foHomePage = require('@pages/FO/home');
const siteMapPage = require('@pages/FO/siteMap');
const cmsPage = require('@pages/FO/cms');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_pages_CRUDPageCategory';


let browserContext;
let page;
let numberOfCategories = 0;
let numberOfPages = 0;

const createCategoryData = new CategoryPageFaker();
const editCategoryData = new CategoryPageFaker({name: `update${createCategoryData.name}`});
const createPageData = new PageFaker();
const editPageData = new PageFaker({
  displayed: false,
  title: `update${createPageData.title}`,
});

// Create, Read, Update and Delete Page Category and Page
describe('Create, Read, Update and Delete Page Category and Page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // Go to Design>Pages page
  it('should go to "Design>Pages" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.pagesLink,
    );

    await pagesPage.closeSfToolBar(page);

    const pageTitle = await pagesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(pagesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page_category');
    if (numberOfCategories !== 0) await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create category then go to FO to check it
  describe('Create Page Category in BO and check it in FO', async () => {
    it('should go to add new page category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewPageCategoryPage', baseContext);

      await pagesPage.goToAddNewPageCategory(page);
      const pageTitle = await addPageCategoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
    });

    it('should create page category ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CreatePageCategory', baseContext);

      const textResult = await addPageCategoryPage.createEditPageCategory(page, createCategoryData);
      await expect(textResult).to.equal(pagesPage.successfulCreationMessage);
    });

    it('should go back to categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCategoriesAfterCreation', baseContext);

      await pagesPage.backToList(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should check the categories number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategoriesNumberAfterCreation', baseContext);

      const numberOfCategoriesAfterCreation = await pagesPage.getNumberOfElementInGrid(
        page,
        'cms_page_category',
      );
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });

    it('should search for the new category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory1', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page_category',
        'input',
        'name',
        createCategoryData.name,
      );
      const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'name',
      );
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should go to FO and check the created category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);

      const pageCategoryID = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'id_cms_category',
      );

      page = await pagesPage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');

      await foHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);

      const pageCategoryName = await siteMapPage.getPageCategoryName(page, pageCategoryID);
      await expect(pageCategoryName).to.contains(createCategoryData.name);

      page = await siteMapPage.closePage(browserContext, page, 0);
    });
  });
  // 2 : Create Page then go to FO to check it
  describe('Create Page in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCategoryToCreateNewPage', baseContext);

      await pagesPage.viewCategory(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should get the pages number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfPages', baseContext);
      numberOfPages = await pagesPage.getNumberOfElementInGrid(page, 'cms_page');
      await expect(numberOfPages).to.equal(0);
    });

    it('should go to add new page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewPage', baseContext);

      await pagesPage.goToAddNewPage(page);
      const pageTitle = await addPageCategoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
    });

    it('should create page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPage', baseContext);

      const textResult = await addPagePage.createEditPage(page, createPageData);
      await expect(textResult).to.equal(pagesPage.successfulCreationMessage);
    });

    it('should search for the created page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedPage', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page',
        'input',
        'meta_title',
        createPageData.title,
      );
      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(
        page,
        1,
        'meta_title',
      );
      await expect(textColumn).to.contains(createPageData.title);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedPageForPreview', baseContext);

      await pagesPage.goToEditPage(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should preview the page in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewPage', baseContext);

      page = await addPagePage.previewPage(page);

      const pageTitle = await cmsPage.getTextContent(page, cmsPage.pageTitle);
      await expect(pageTitle).to.contains(createPageData.title);

      const metaTitle = await cmsPage.getPageTitle(page);
      await expect(metaTitle).to.equal(createPageData.metaTitle);

      const pageContent = await cmsPage.getTextContent(page, cmsPage.pageContent);
      await expect(pageContent).to.include(createPageData.content);

      page = await cmsPage.closePage(browserContext, page, 0);
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelCreatedPageEdition', baseContext);

      await addPagePage.cancelPage(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });
  });
  // 3 : Update category then go to FO to check it
  describe('Update Page Category created in BO and check it in FO', async () => {
    it('should search for the created category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory2', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page_category',
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'name',
      );
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should go to edit category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCategory', baseContext);

      await pagesPage.goToEditCategoryPage(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should update the created page category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'UpdateCategory', baseContext);

      const textResult = await addPageCategoryPage.createEditPageCategory(page, editCategoryData);
      await expect(textResult).to.equal(addPageCategoryPage.successfulUpdateMessage);
    });

    it('should go back to categories list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCategoriesAfterUpdate', baseContext);

      await pagesPage.backToList(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should search for the updated category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchUpdatedCategory', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page_category',
        'input',
        'name',
        editCategoryData.name,
      );

      const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'name',
      );

      await expect(textColumn).to.contains(editCategoryData.name);
    });

    it('should go to FO and check the updated category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCategoryFO', baseContext);

      const pageCategoryID = await pagesPage.getTextColumnFromTableCmsPageCategory(
        page,
        1,
        'id_cms_category',
      );

      page = await pagesPage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');

      await foHomePage.goToFooterLink(page, 'Sitemap');
      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);

      const pageCategoryName = await siteMapPage.getPageCategoryName(page, pageCategoryID);
      await expect(pageCategoryName).to.contains(editCategoryData.name);

      page = await siteMapPage.closePage(browserContext, page, 0);
    });
  });

  // 4 : Update page then go to FO to check it
  describe('Update Page created in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedCategory', baseContext);

      await pagesPage.viewCategory(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should search for the created page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedPage', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page',
        'input',
        'meta_title',
        createPageData.title,
      );

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(
        page,
        1,
        'meta_title',
      );
      await expect(textColumn).to.contains(createPageData.title);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedPageForUpdate', baseContext);

      await pagesPage.goToEditPage(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should update the created page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePage', baseContext);

      const textResult = await addPagePage.createEditPage(page, editPageData);
      await expect(textResult).to.equal(pagesPage.successfulUpdateMessage);
    });

    it('should search for the updated Page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchUpdatedPageForPreview', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page',
        'input',
        'meta_title',
        editPageData.title,
      );

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(
        page,
        1,
        'meta_title',
      );

      await expect(textColumn).to.contains(editPageData.title);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToUpdatedPageForPreview', baseContext);

      await pagesPage.goToEditPage(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should click on preview button and check that the page does not exist in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewUpdatedPage', baseContext);

      page = await addPagePage.previewPage(page);

      const pageTitle = await cmsPage.getTextContent(page, cmsPage.pageTitle);
      await expect(pageTitle).to.include(cmsPage.pageNotFound);

      page = await cmsPage.closePage(browserContext, page, 0);
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelUpdatedPageEdition', baseContext);

      await addPagePage.cancelPage(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });
  });

  // 5 : Delete Page and Category from BO
  describe('Delete Page and Category', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCategoryForDelete', baseContext);

      await pagesPage.viewCategory(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should search for the updated page to delete', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchUpdatedPageForDelete', baseContext);

      await pagesPage.filterTable(
        page,
        'cms_page',
        'input',
        'meta_title',
        editPageData.title,
      );

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(
        page,
        1,
        'meta_title',
      );

      await expect(textColumn).to.contains(editPageData.title);
    });

    it('should delete page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePage', baseContext);

      const textResult = await pagesPage.deleteRowInTable(page, 'cms_page', 1);
      await expect(textResult).to.equal(pagesPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterPages', baseContext);

      const numberOfPagesAfterDeletion = await pagesPage.resetAndGetNumberOfLines(page, 'cms_page');
      await expect(numberOfPagesAfterDeletion).to.be.equal(numberOfPages);
    });

    it('should click on back to list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCategoriesAfterDelete', baseContext);

      await pagesPage.backToList(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await pagesPage.deleteRowInTable(page, 'cms_page_category', 1);
      await expect(textResult).to.equal(pagesPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

      const numberOfCategoriesAfterDeletion = await pagesPage.resetAndGetNumberOfLines(
        page,
        'cms_page_category',
      );
      await expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });
  });
});
