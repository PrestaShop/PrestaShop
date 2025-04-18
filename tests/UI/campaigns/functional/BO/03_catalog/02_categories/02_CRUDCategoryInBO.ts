import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  type APIRequestContext,
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type CategoryRedirection,
  dataCategories,
  FakerCategory,
  foClassicHomePage,
  foClassicCategoryPage,
  foClassicSitemapPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_categories_CRUDCategoryInBO';

// Create, Read, Update and Delete Category
describe('BO - Catalog - Categories : CRUD Category in BO', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;
  let categoryID: number = 0;
  let subcategoryID: number = 0;
  let categoryFriendlyURL: string = '';

  const createCategoryData: FakerCategory = new FakerCategory({
    displayed: true,
    redirectionWhenNotDisplayed: '301',
    redirectedCategory: dataCategories.art,
  });
  const createSubCategoryData: FakerCategory = new FakerCategory({
    name: 'subCategoryToCreate',
    displayed: true,
    redirectionWhenNotDisplayed: '302',
    redirectedCategory: dataCategories.clothes,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
    page = await utilsPlaywright.newTab(browserContext);

    // Create categories images
    await Promise.all([
      utilsFile.generateImage(`${createCategoryData.name}.jpg`),
      utilsFile.generateImage(`${createSubCategoryData.name}.jpg`),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      utilsFile.deleteFile(`${createCategoryData.name}.jpg`),
      utilsFile.deleteFile(`${createSubCategoryData.name}.jpg`),
    ]);
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

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create category and subcategory then go to FO to check the existence of the new categories
  describe('Create Category and subcategory in BO then check it in FO', async () => {
    describe('Create Category and check it in FO', async () => {
      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCategoryPage', baseContext);

        await boCategoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
      });

      it('should create category and check the categories number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

        const textResult = await boCategoriesCreatePage.createEditCategory(page, createCategoryData);
        expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
      });

      it('should search for the new category and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory', baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(
          page,
          'input',
          'name',
          createCategoryData.name,
        );

        const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

        for (let i: number = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, i, 'name');
          expect(textColumn).to.contains(createCategoryData.name);
        }
      });

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCheckCreatedCategory', baseContext);

        categoryID = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
        // View Shop
        page = await boCategoriesPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should check the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);

        // Go to sitemap page
        await foClassicHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await foClassicSitemapPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

        // Check category name
        const categoryName = await foClassicSitemapPage.getCategoryName(page, categoryID);
        expect(categoryName).to.contains(createCategoryData.name);
      });

      it('should view the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedCategoryFO', baseContext);

        await foClassicSitemapPage.viewCreatedCategory(page, categoryID);

        // Check category name
        const pageTitle = await foClassicCategoryPage.getHeaderPageName(page);
        expect(pageTitle).to.contains(createCategoryData.name.toUpperCase());

        // Check category description
        const categoryDescription = await foClassicCategoryPage.getCategoryDescription(page);
        expect(categoryDescription).to.equal(createCategoryData.description);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foClassicCategoryPage.closePage(browserContext, page, 0);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
      });
    });

    describe('Create Subcategory and check it in FO', async () => {
      it('should display the subcategories table related to the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'displaySubcategoriesForCreatedCategory', baseContext);

        await boCategoriesPage.goToViewSubCategoriesPage(page, 1);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(createCategoryData.name);
      });

      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewSubcategoryPage', baseContext);

        await boCategoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
      });

      it('should create a subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createSubcategory', baseContext);

        const textResult = await boCategoriesCreatePage.createEditCategory(page, createSubCategoryData);
        expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);
      });

      it('should search for the subcategory and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchForCreatedSubcategory', baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(
          page,
          'input',
          'name',
          createSubCategoryData.name,
        );

        const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
        expect(textColumn).to.contains(createSubCategoryData.name);
      });

      it('should go to FO and check the created Subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedSubcategoryFO', baseContext);

        subcategoryID = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
        // View shop
        page = await boCategoriesPage.viewMyShop(page);
        // Change language in FO
        await foClassicHomePage.changeLanguage(page, 'en');
        // Go to sitemap page
        await foClassicHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await foClassicSitemapPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

        // Check category
        const categoryName = await foClassicSitemapPage.getCategoryName(page, subcategoryID);
        expect(categoryName).to.contains(createSubCategoryData.name);
      });

      it('should view the created subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedSubcategoryFO', baseContext);

        await foClassicSitemapPage.viewCreatedCategory(page, subcategoryID);

        // Check subcategory name
        const pageTitle = await foClassicCategoryPage.getHeaderPageName(page);
        expect(pageTitle).to.contains(createSubCategoryData.name.toUpperCase());

        // Check subcategory description
        const subcategoryDescription = await foClassicCategoryPage.getCategoryDescription(page);
        expect(subcategoryDescription).to.equal(createSubCategoryData.description);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await foClassicCategoryPage.closePage(browserContext, page, 0);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(createCategoryData.name);
      });
    });
  });

  // 2 : View Category and check the subcategories related
  describe('View Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToViewCreatedCategory', baseContext);

      await boCategoriesPage.goToSubMenu(
        page,
        boCategoriesPage.catalogParentLink,
        boCategoriesPage.categoriesLink,
      );

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
    });

    it(`should filter list by Name '${createCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCategory', baseContext);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(
        page,
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCreatedCategoryPage', baseContext);

      await boCategoriesPage.goToViewSubCategoriesPage(page, 1);

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(createCategoryData.name);
    });

    it('should check subcategories list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSubcategoriesForCreatedCategory', baseContext);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(
        page,
        'input',
        'name',
        createSubCategoryData.name,
      );

      const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textColumn).to.contains(createSubCategoryData.name);
    });
  });

  // 3 : Disable category (and subcategory) and check the redirection
  [
    {
      type: 'category',
      category: createCategoryData,
    },
    {
      type: 'subcategory',
      category: createSubCategoryData,
    },
  ].forEach((arg: {type: string, category: FakerCategory}, index: number) => {
    describe(`Disable ${arg.type} and check the redirection (${arg.category.redirectionWhenNotDisplayed})`, async () => {
      it('should go to \'Catalog > Categories\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCategoriesPageToDisable${index}`, baseContext);

        await boCategoriesPage.goToSubMenu(
          page,
          boCategoriesPage.catalogParentLink,
          boCategoriesPage.categoriesLink,
        );

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
      });

      it(`should filter list by Name '${arg.category.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDisable${index}`, baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(
          page,
          'input',
          'name',
          arg.category.name,
        );

        const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
        expect(textColumn).to.contains(arg.category.name);
      });

      it(`should go to edit ${arg.type} page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditCategoryPage${index}`, baseContext);

        await boCategoriesPage.goToEditCategoryPage(page, 1);

        const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleEdit + arg.category.name);

        categoryFriendlyURL = await boCategoriesCreatePage.getValue(page, 'friendlyUrl');
      });

      it(`should disable the ${arg.type}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `disableCategory${index}`, baseContext);

        arg.category.setDisplayed(false);

        const textResult = await boCategoriesCreatePage.createEditCategory(page, arg.category);
        expect(textResult).to.equal(boCategoriesPage.successfulUpdateMessage);

        const numberOfCategoriesAfterUpdate = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategoriesAfterUpdate).to.be.equal(arg.type === 'category' ? numberOfCategories + 1 : 1);
      });

      it(`should go to FO and check that the ${arg.type} does not exist`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkRedirectedCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // View shop
        page = await boCategoriesPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');
        // Go to sitemap page
        await foClassicHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await foClassicSitemapPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

        // Check category name
        const categoryName = await foClassicSitemapPage.isVisibleCategory(page, idCategory);
        expect(categoryName).to.eq(false);
      });

      it('should check the HTTP code of the response', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `responseRedirectedCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // Check if it is an error page
        const response = await foClassicCategoryPage.goTo(page, `${global.FO.URL}en/${idCategory}-${categoryFriendlyURL}`);
        expect(response).to.be.not.equal(null);
        const requestRedirectFrom = response!.request().redirectedFrom();
        expect(requestRedirectFrom).to.be.not.equal(null);
        const responseBeforeRedirection = await requestRedirectFrom!.response();
        expect(responseBeforeRedirection).to.be.not.equal(null);
        expect(responseBeforeRedirection!.status()).to.be.equal(parseInt(arg.category.redirectionWhenNotDisplayed, 10));

        // Check if it is redirected to the category
        const categoryName = await foClassicCategoryPage.getHeaderPageName(page);
        expect(categoryName).to.contains(arg.category.redirectedCategory!.name.toUpperCase());
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBoDisabled${index}`, baseContext);

        const titlePage: string = arg.type === 'category'
          ? boCategoriesPage.pageTitle
          : boCategoriesPage.pageCategoryTitle(createCategoryData.name);

        // Close tab and init other page objects with new current tab
        page = await foClassicSitemapPage.closePage(browserContext, page, 0);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(titlePage);
      });
    });
  });

  // 4: Update the category (and subcategory) redirection to 40x and check the error page
  [
    {
      type: 'category',
      category: createCategoryData,
      newRedirect: '404' as CategoryRedirection,
    },
    {
      type: 'subcategory',
      category: createSubCategoryData,
      newRedirect: '410' as CategoryRedirection,
    },
  ].forEach((arg: {type: string, category: FakerCategory, newRedirect: CategoryRedirection}, index: number) => {
    describe(`Change the redirection for ${arg.type} and check the error page (${arg.newRedirect})`, async () => {
      it('should go to \'Catalog > Categories\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToCategoriesPageToSetRedirection${index}`, baseContext);

        await boCategoriesPage.goToSubMenu(
          page,
          boCategoriesPage.catalogParentLink,
          boCategoriesPage.categoriesLink,
        );

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
      });

      it(`should filter list by Name '${arg.category.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToUpdate${index}`, baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(
          page,
          'input',
          'name',
          arg.category.name,
        );

        const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
        expect(textColumn).to.contains(arg.category.name);
      });

      it(`should go to edit ${arg.type} page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditCategoryPage1${index}`, baseContext);

        await boCategoriesPage.goToEditCategoryPage(page, 1);

        const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleEdit + arg.category.name);

        categoryFriendlyURL = await boCategoriesCreatePage.getValue(page, 'friendlyUrl');
      });

      it(`should set a new redirection type for ${arg.type}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setNewRedirection${index}`, baseContext);

        arg.category.setRedirectionWhenNotDisplayed(arg.newRedirect);

        const textResult = await boCategoriesCreatePage.createEditCategory(page, arg.category);
        expect(textResult).to.equal(boCategoriesPage.successfulUpdateMessage);

        const numberOfCategoriesAfterUpdate = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategoriesAfterUpdate).to.be.equal(arg.type === 'category' ? numberOfCategories + 1 : 1);
      });

      it(`should go to FO and check that the ${arg.type} does not exist`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkErroredCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // View shop
        page = await boCategoriesPage.viewMyShop(page);
        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');
        // Go to sitemap page
        await foClassicHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await foClassicSitemapPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

        // Check category name
        const categoryName = await foClassicSitemapPage.isVisibleCategory(page, idCategory);
        expect(categoryName).to.eq(false);
      });

      it('should check the HTTP code of the response', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `responseErroredCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // Check if it is a error page
        const response = await apiContext.get(`${global.FO.URL}en/${idCategory}-${categoryFriendlyURL}`);
        expect(response).to.be.not.equal(null);
        expect(response!.status()).to.be.equal(parseInt(arg.category.redirectionWhenNotDisplayed, 10));
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBoRedirect${index}`, baseContext);

        const titlePage: string = arg.type === 'category'
          ? boCategoriesPage.pageTitle
          : boCategoriesPage.pageCategoryTitle(createCategoryData.name);

        // Close tab and init other page objects with new current tab
        page = await foClassicSitemapPage.closePage(browserContext, page, 0);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(titlePage);
      });
    });
  });

  // 5 : Delete Category from BO
  describe('Delete Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToDelete', baseContext);

      await boCategoriesPage.goToSubMenu(
        page,
        boCategoriesPage.catalogParentLink,
        boCategoriesPage.categoriesLink,
      );

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
    });

    it(`should filter list by Name '${createCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(
        page,
        'input',
        'name',
        createCategoryData.name,
      );
      categoryID = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);

      const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await boCategoriesPage.deleteCategory(page, 1);
      expect(textResult).to.equal(boCategoriesPage.successfulDeleteMessage);

      const numberOfCategoriesAfterDeletion = await boCategoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // View shop
      page = await boCategoriesPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check that the deleted category does not exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedCategoryFO', baseContext);

      // Go to sitemap page
      await foClassicHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await foClassicSitemapPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

      const categoryName = await foClassicSitemapPage.isVisibleCategory(page, categoryID);
      expect(categoryName, 'Category is visible in FO!').to.eq(false);
    });
  });
});
