require('module-alias/register');

// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const CategoryPageFaker = require('@data/faker/CMScategory');
const PageFaker = require('@data/faker/CMSpage');

// Importing BO pages
const dashboardPage = require('@pages/BO/dashboard/index');
const pagesPage = require('@pages/BO/design/pages/index');
const addPageCategoryPage = require('@pages/BO/design/pages/pageCategory/add');
const addPagePage = require('@pages/BO/design/pages/add');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const siteMapPage = require('@pages/FO/siteMap');
const cmsPage = require('@pages/FO/cms');

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
let categoryID = 0;
const categoriesTableName = 'cms_page_category';
const pagesTableName = 'cms_page';

/*
Create category and check it in FO
Create page and check it in FO
Update category and check it in FO
Update page and check it in FO
Delete page and category from BO
 */
describe('BO - Design - Pages : CRUD category and page', async () => {
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

  it('should go to \'Design > Pages\' page', async function () {
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

    numberOfCategories = await pagesPage.resetAndGetNumberOfLines(page, categoriesTableName);
    if (numberOfCategories !== 0) await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create category then go to FO to check it
  describe('Create category in BO and check it in FO', async () => {
    it('should go to add new page category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewPageCategoryPage', baseContext);

      await pagesPage.goToAddNewPageCategory(page);
      const pageTitle = await addPageCategoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addPageCategoryPage.pageTitleCreate);
    });

    it('should create category ', async function () {
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
        categoriesTableName,
      );
      await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });

    it('should search for the new category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory1', baseContext);

      await pagesPage.filterTable(page, categoriesTableName, 'input', 'name', createCategoryData.name);

      const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      await expect(textColumn).to.contains(createCategoryData.name);

      // Get category ID
      categoryID = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'id_cms_category');
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await pagesPage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');

      const pageTitle = await foHomePage.getPageTitle(page);
      await expect(pageTitle).to.equal(foHomePage.pageTitle);
    });

    it('should go to \'Sitemap\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSiteMapPage1', baseContext);

      await foHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);
    });

    it('should check the created category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO1', baseContext);

      const pageCategoryName = await siteMapPage.getPageCategoryName(page, categoryID);
      await expect(pageCategoryName).to.contains(createCategoryData.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await siteMapPage.closePage(browserContext, page, 0);

      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });
  });

  // 2 : Create Page then go to FO to check it
  describe('Create page in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCategoryToCreateNewPage', baseContext);

      await pagesPage.viewCategory(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should get the pages number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfPages', baseContext);
      numberOfPages = await pagesPage.getNumberOfElementInGrid(page, pagesTableName);
      await expect(numberOfPages).to.equal(0);
    });

    it('should go to add new page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewPage', baseContext);

      await pagesPage.goToAddNewPage(page);
      const pageTitle = await addPagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addPagePage.pageTitleCreate);
    });

    it('should create page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPage', baseContext);

      const textResult = await addPagePage.createEditPage(page, createPageData);
      await expect(textResult).to.equal(pagesPage.successfulCreationMessage);
    });

    it('should search for the created page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedPage', baseContext);

      await pagesPage.filterTable(page, pagesTableName, 'input', 'meta_title', createPageData.title);

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
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
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await cmsPage.closePage(browserContext, page, 0);

      const pageTitle = await addPagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addPagePage.pageTitleCreate);
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelCreatedPageEdition', baseContext);

      await addPagePage.cancelPage(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });
  });

  // 3 : Update category then go to FO to check it
  describe('Update category in BO and check it in FO', async () => {
    it('should search for the created category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory2', baseContext);

      await pagesPage.filterTable(page, categoriesTableName, 'input', 'name', createCategoryData.name);

      const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
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

      await pagesPage.filterTable(page, categoriesTableName, 'input', 'name', editCategoryData.name);

      const textColumn = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      await expect(textColumn).to.contains(editCategoryData.name);

      categoryID = await pagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'id_cms_category');
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await pagesPage.viewMyShop(page);

      await foHomePage.changeLanguage(page, 'en');

      const pageTitle = await foHomePage.getPageTitle(page);
      await expect(pageTitle).to.equal(foHomePage.pageTitle);
    });

    it('should go to \'Sitemap\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSiteMapPage2', baseContext);

      await foHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);
    });

    it('should check the updated category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCategoryFO2', baseContext);

      const pageCategoryName = await siteMapPage.getPageCategoryName(page, categoryID);
      await expect(pageCategoryName).to.contains(editCategoryData.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await siteMapPage.closePage(browserContext, page, 0);

      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });
  });

  // 4 : Update page then go to FO to check it
  describe('Update page created in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedCategory', baseContext);

      await pagesPage.viewCategory(page, 1);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });

    it('should search for the created page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedPage', baseContext);

      await pagesPage.filterTable(page, pagesTableName, 'input', 'meta_title', createPageData.title);

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
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

      await pagesPage.filterTable(page, pagesTableName, 'input', 'meta_title', editPageData.title);

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
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
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await cmsPage.closePage(browserContext, page, 0);

      const pageTitle = await addPagePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addPagePage.pageTitleCreate);
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelUpdatedPageEdition', baseContext);

      await addPagePage.cancelPage(page);
      const pageTitle = await pagesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(pagesPage.pageTitle);
    });
  });

  // 5 : Delete page and category from BO
  describe('Delete page and category', async () => {
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
        pagesTableName,
        'input',
        'meta_title',
        editPageData.title,
      );

      const textColumn = await pagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      await expect(textColumn).to.contains(editPageData.title);
    });

    it('should delete page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePage', baseContext);

      const textResult = await pagesPage.deleteRowInTable(page, pagesTableName, 1);
      await expect(textResult).to.equal(pagesPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterPages', baseContext);

      const numberOfPagesAfterDeletion = await pagesPage.resetAndGetNumberOfLines(page, pagesTableName);
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

      const textResult = await pagesPage.deleteRowInTable(page, categoriesTableName, 1);
      await expect(textResult).to.equal(pagesPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

      const numberOfCategoriesAfterDeletion = await pagesPage.resetAndGetNumberOfLines(
        page,
        categoriesTableName,
      );
      await expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });
  });
});
