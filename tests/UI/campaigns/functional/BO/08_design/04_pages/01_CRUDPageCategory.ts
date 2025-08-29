import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCMSPageCategoriesCreatePage,
  boCMSPagesPage,
  boCMSPagesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCMSCategory,
  FakerCMSPage,
  foClassicCmsPage,
  foClassicHomePage,
  foClassicSitemapPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_pages_CRUDPageCategory';

/*
Create category and check it in FO
Create page and check it in FO
Update category and check it in FO
Update page and check it in FO
Delete page and category from BO
 */
describe('BO - Design - Pages : CRUD category and page', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;
  let numberOfPages: number = 0;
  let categoryID: number = 0;

  const createCategoryData: FakerCMSCategory = new FakerCMSCategory();
  const editCategoryData: FakerCMSCategory = new FakerCMSCategory({name: `update${createCategoryData.name}`});
  const createPageData: FakerCMSPage = new FakerCMSPage();
  const editPageData: FakerCMSPage = new FakerCMSPage({
    displayed: false,
    title: `update${createPageData.title}`,
  });
  const categoriesTableName: string = 'cms_page_category';
  const pagesTableName: string = 'cms_page';

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Design > Pages\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCmsPagesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.pagesLink,
    );
    await boCMSPagesPage.closeSfToolBar(page);

    const pageTitle = await boCMSPagesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
  });

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCategories = await boCMSPagesPage.resetAndGetNumberOfLines(page, categoriesTableName);
    if (numberOfCategories !== 0) expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create category then go to FO to check it
  describe('Create category in BO and check it in FO', async () => {
    it('should go to add new page category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewPageCategoryPage', baseContext);

      await boCMSPagesPage.goToAddNewPageCategory(page);

      const pageTitle = await boCMSPageCategoriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPageCategoriesCreatePage.pageTitleCreate);
    });

    it('should create category ', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CreatePageCategory', baseContext);

      const textResult = await boCMSPageCategoriesCreatePage.createEditPageCategory(page, createCategoryData);
      expect(textResult).to.equal(boCMSPagesPage.successfulCreationMessage);
    });

    it('should go back to categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCategoriesAfterCreation', baseContext);

      await boCMSPagesPage.backToList(page);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });

    it('should check the categories number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategoriesNumberAfterCreation', baseContext);

      const numberOfCategoriesAfterCreation = await boCMSPagesPage.getNumberOfElementInGrid(
        page,
        categoriesTableName,
      );
      expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
    });

    it('should search for the new category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory1', baseContext);

      await boCMSPagesPage.filterTable(page, categoriesTableName, 'input', 'name', createCategoryData.name);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);

      // Get category ID
      categoryID = parseInt(
        await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'id_cms_category'),
        10,
      );
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop1', baseContext);

      page = await boCMSPagesPage.viewMyShop(page);

      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
    });

    it('should go to \'Sitemap\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSiteMapPage1', baseContext);

      await foClassicHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await foClassicSitemapPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);
    });

    it('should check the created category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO1', baseContext);

      const pageCategoryName = await foClassicSitemapPage.getPageCategoryName(page, categoryID);
      expect(pageCategoryName).to.contains(createCategoryData.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      page = await foClassicSitemapPage.closePage(browserContext, page, 0);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });
  });

  // 2 : Create Page then go to FO to check it
  describe('Create page in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCategoryToCreateNewPage', baseContext);

      await boCMSPagesPage.viewCategory(page, 1);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });

    it('should get the pages number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNumberOfPages', baseContext);

      numberOfPages = await boCMSPagesPage.getNumberOfElementInGrid(page, pagesTableName);
      expect(numberOfPages).to.equal(0);
    });

    it('should go to add new page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewPage', baseContext);

      await boCMSPagesPage.goToAddNewPage(page);

      const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesCreatePage.pageTitleCreate);
    });

    it('should create page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createPage', baseContext);

      const textResult = await boCMSPagesCreatePage.createEditPage(page, createPageData);
      expect(textResult).to.equal(boCMSPagesPage.successfulCreationMessage);
    });

    it('should search for the created page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedPage', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTableName, 'input', 'meta_title', createPageData.title);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textColumn).to.contains(createPageData.title);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedPageForPreview', baseContext);

      await boCMSPagesPage.goToEditPage(page, 1);

      const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesCreatePage.editPageTitle(createPageData.title));
    });

    it('should preview the page in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewPage', baseContext);

      page = await boCMSPagesCreatePage.previewPage(page);

      const pageTitle = await foClassicCmsPage.getTextContent(page, foClassicCmsPage.pageTitle);
      expect(pageTitle).to.contains(createPageData.title);

      const metaTitle = await foClassicCmsPage.getPageTitle(page);
      expect(metaTitle).to.equal(createPageData.metaTitle);

      const pageContent = await foClassicCmsPage.getTextContent(page, foClassicCmsPage.pageContent);
      expect(pageContent).to.include(createPageData.content);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      page = await foClassicCmsPage.closePage(browserContext, page, 0);

      const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesCreatePage.editPageTitle(createPageData.title));
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelCreatedPageEdition', baseContext);

      await boCMSPagesCreatePage.cancelPage(page);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });
  });

  // 3 : Update category then go to FO to check it
  describe('Update category in BO and check it in FO', async () => {
    it('should search for the created category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory2', baseContext);

      await boCMSPagesPage.filterTable(page, categoriesTableName, 'input', 'name', createCategoryData.name);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should go to edit category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCategory', baseContext);

      await boCMSPagesPage.goToEditCategoryPage(page, 1);

      const pageTitle = await boCMSPageCategoriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPageCategoriesCreatePage.pageTitleEdit);
    });

    it('should update the created page category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'UpdateCategory', baseContext);

      const textResult = await boCMSPageCategoriesCreatePage.createEditPageCategory(page, editCategoryData);
      expect(textResult).to.equal(boCMSPageCategoriesCreatePage.successfulUpdateMessage);
    });

    it('should go back to categories list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCategoriesAfterUpdate', baseContext);

      await boCMSPagesPage.backToList(page);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });

    it('should search for the updated category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchUpdatedCategory', baseContext);

      await boCMSPagesPage.filterTable(page, categoriesTableName, 'input', 'name', editCategoryData.name);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'name');
      expect(textColumn).to.contains(editCategoryData.name);

      categoryID = parseInt(
        await boCMSPagesPage.getTextColumnFromTableCmsPageCategory(page, 1, 'id_cms_category'),
        10,
      );
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop2', baseContext);

      page = await boCMSPagesPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const pageTitle = await foClassicHomePage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicHomePage.pageTitle);
    });

    it('should go to \'Sitemap\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSiteMapPage2', baseContext);

      await foClassicHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await foClassicSitemapPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);
    });

    it('should check the updated category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCategoryFO2', baseContext);

      const pageCategoryName = await foClassicSitemapPage.getPageCategoryName(page, categoryID);
      expect(pageCategoryName).to.contains(editCategoryData.name);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      page = await foClassicSitemapPage.closePage(browserContext, page, 0);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });
  });

  // 4 : Update page then go to FO to check it
  describe('Update page created in BO and preview it in FO', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedCategory', baseContext);

      await boCMSPagesPage.viewCategory(page, 1);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });

    it('should search for the created page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedPage', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTableName, 'input', 'meta_title', createPageData.title);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textColumn).to.contains(createPageData.title);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedPageForUpdate', baseContext);

      await boCMSPagesPage.goToEditPage(page, 1);

      const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesCreatePage.editPageTitle(createPageData.title));
    });

    it('should update the created page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updatePage', baseContext);

      const textResult = await boCMSPagesCreatePage.createEditPage(page, editPageData);
      expect(textResult).to.equal(boCMSPagesPage.successfulUpdateMessage);
    });

    it('should search for the updated Page and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchUpdatedPageForPreview', baseContext);

      await boCMSPagesPage.filterTable(page, pagesTableName, 'input', 'meta_title', editPageData.title);

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textColumn).to.contains(editPageData.title);
    });

    it('should go to edit page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToUpdatedPageForPreview', baseContext);

      await boCMSPagesPage.goToEditPage(page, 1);

      const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesCreatePage.editPageTitle(editPageData.title));
    });

    it('should click on preview button and check that the page does not exist in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewUpdatedPage', baseContext);

      page = await boCMSPagesCreatePage.previewPage(page);

      const pageTitle = await foClassicCmsPage.getTextContent(page, foClassicCmsPage.pageTitle);
      expect(pageTitle).to.include(foClassicCmsPage.pageNotFound);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      page = await foClassicCmsPage.closePage(browserContext, page, 0);

      const pageTitle = await boCMSPagesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesCreatePage.editPageTitle(editPageData.title));
    });

    it('should click on cancel button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'cancelUpdatedPageEdition', baseContext);

      await boCMSPagesCreatePage.cancelPage(page);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });
  });

  // 5 : Delete page and category from BO
  describe('Delete page and category', async () => {
    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCategoryForDelete', baseContext);

      await boCMSPagesPage.viewCategory(page, 1);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });

    it('should search for the updated page to delete', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchUpdatedPageForDelete', baseContext);

      await boCMSPagesPage.filterTable(
        page,
        pagesTableName,
        'input',
        'meta_title',
        editPageData.title,
      );

      const textColumn = await boCMSPagesPage.getTextColumnFromTableCmsPage(page, 1, 'meta_title');
      expect(textColumn).to.contains(editPageData.title);
    });

    it('should delete page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deletePage', baseContext);

      const textResult = await boCMSPagesPage.deleteRowInTable(page, pagesTableName, 1);
      expect(textResult).to.equal(boCMSPagesPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterPages', baseContext);

      const numberOfPagesAfterDeletion = await boCMSPagesPage.resetAndGetNumberOfLines(page, pagesTableName);
      expect(numberOfPagesAfterDeletion).to.be.equal(numberOfPages);
    });

    it('should click on back to list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToCategoriesAfterDelete', baseContext);

      await boCMSPagesPage.backToList(page);

      const pageTitle = await boCMSPagesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCMSPagesPage.pageTitle);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await boCMSPagesPage.deleteRowInTable(page, categoriesTableName, 1);
      expect(textResult).to.equal(boCMSPagesPage.successfulDeleteMessage);
    });

    it('should reset filter', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterCategories', baseContext);

      const numberOfCategoriesAfterDeletion = await boCMSPagesPage.resetAndGetNumberOfLines(
        page,
        categoriesTableName,
      );
      expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });
  });
});
