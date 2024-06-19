// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import categoriesPage from '@pages/BO/catalog/categories';
import addCategoryPage from '@pages/BO/catalog/categories/add';
// Import FO pages
import {categoryPage} from '@pages/FO/classic/category';
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {siteMapPage} from '@pages/FO/classic/siteMap';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  type CategoryRedirection,
  dataCategories,
  FakerCategory,
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
    await loginCommon.loginBO(this, page);
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

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create category and subcategory then go to FO to check the existence of the new categories
  describe('Create Category and subcategory in BO then check it in FO', async () => {
    describe('Create Category and check it in FO', async () => {
      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCategoryPage', baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create category and check the categories number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, createCategoryData);
        expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
      });

      it('should search for the new category and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchCreatedCategory', baseContext);

        await categoriesPage.resetFilter(page);
        await categoriesPage.filterCategories(
          page,
          'input',
          'name',
          createCategoryData.name,
        );

        const numberOfCategoriesAfterFilter = await categoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

        for (let i: number = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, i, 'name');
          expect(textColumn).to.contains(createCategoryData.name);
        }
      });

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCheckCreatedCategory', baseContext);

        categoryID = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
        // View Shop
        page = await categoriesPage.viewMyShop(page);
        // Change FO language
        await foHomePage.changeLanguage(page, 'en');

        const isHomePage = await foHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should check the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);

        // Go to sitemap page
        await foHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        expect(pageTitle).to.equal(siteMapPage.pageTitle);

        // Check category name
        const categoryName = await siteMapPage.getCategoryName(page, categoryID);
        expect(categoryName).to.contains(createCategoryData.name);
      });

      it('should view the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedCategoryFO', baseContext);

        await siteMapPage.viewCreatedCategory(page, categoryID);

        // Check category name
        const pageTitle = await categoryPage.getHeaderPageName(page);
        expect(pageTitle).to.contains(createCategoryData.name.toUpperCase());

        // Check category description
        const categoryDescription = await categoryPage.getCategoryDescription(page);
        expect(categoryDescription).to.equal(createCategoryData.description);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

        // Close tab and init other page objects with new current tab
        page = await categoryPage.closePage(browserContext, page, 0);

        const pageTitle = await categoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(categoriesPage.pageTitle);
      });
    });

    describe('Create Subcategory and check it in FO', async () => {
      it('should display the subcategories table related to the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'displaySubcategoriesForCreatedCategory', baseContext);

        await categoriesPage.goToViewSubCategoriesPage(page, 1);

        const pageTitle = await categoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(createCategoryData.name);
      });

      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewSubcategoryPage', baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create a subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createSubcategory', baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, createSubCategoryData);
        expect(textResult).to.equal(categoriesPage.successfulCreationMessage);
      });

      it('should search for the subcategory and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchForCreatedSubcategory', baseContext);

        await categoriesPage.resetFilter(page);
        await categoriesPage.filterCategories(
          page,
          'input',
          'name',
          createSubCategoryData.name,
        );

        const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
        expect(textColumn).to.contains(createSubCategoryData.name);
      });

      it('should go to FO and check the created Subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedSubcategoryFO', baseContext);

        subcategoryID = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
        // View shop
        page = await categoriesPage.viewMyShop(page);
        // Change language in FO
        await foHomePage.changeLanguage(page, 'en');
        // Go to sitemap page
        await foHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        expect(pageTitle).to.equal(siteMapPage.pageTitle);

        // Check category
        const categoryName = await siteMapPage.getCategoryName(page, subcategoryID);
        expect(categoryName).to.contains(createSubCategoryData.name);
      });

      it('should view the created subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedSubcategoryFO', baseContext);

        await siteMapPage.viewCreatedCategory(page, subcategoryID);

        // Check subcategory name
        const pageTitle = await categoryPage.getHeaderPageName(page);
        expect(pageTitle).to.contains(createSubCategoryData.name.toUpperCase());

        // Check subcategory description
        const subcategoryDescription = await categoryPage.getCategoryDescription(page);
        expect(subcategoryDescription).to.equal(createSubCategoryData.description);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await categoryPage.closePage(browserContext, page, 0);

        const pageTitle = await categoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(createCategoryData.name);
      });
    });
  });

  // 2 : View Category and check the subcategories related
  describe('View Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToViewCreatedCategory', baseContext);

      await categoriesPage.goToSubMenu(
        page,
        categoriesPage.catalogParentLink,
        categoriesPage.categoriesLink,
      );

      const pageTitle = await categoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it(`should filter list by Name '${createCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedCategory', baseContext);

      await categoriesPage.resetFilter(page);
      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCreatedCategoryPage', baseContext);

      await categoriesPage.goToViewSubCategoriesPage(page, 1);

      const pageTitle = await categoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(createCategoryData.name);
    });

    it('should check subcategories list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSubcategoriesForCreatedCategory', baseContext);

      await categoriesPage.resetFilter(page);
      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        createSubCategoryData.name,
      );

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
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

        await categoriesPage.goToSubMenu(
          page,
          categoriesPage.catalogParentLink,
          categoriesPage.categoriesLink,
        );

        const pageTitle = await categoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(categoriesPage.pageTitle);
      });

      it(`should filter list by Name '${arg.category.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToDisable${index}`, baseContext);

        await categoriesPage.resetFilter(page);
        await categoriesPage.filterCategories(
          page,
          'input',
          'name',
          arg.category.name,
        );

        const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
        expect(textColumn).to.contains(arg.category.name);
      });

      it(`should go to edit ${arg.type} page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditCategoryPage${index}`, baseContext);

        await categoriesPage.goToEditCategoryPage(page, 1);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCategoryPage.pageTitleEdit + arg.category.name);

        categoryFriendlyURL = await addCategoryPage.getValue(page, 'friendlyUrl');
      });

      it(`should disable the ${arg.type}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `disableCategory${index}`, baseContext);

        arg.category.setDisplayed(false);

        const textResult = await addCategoryPage.createEditCategory(page, arg.category);
        expect(textResult).to.equal(categoriesPage.successfulUpdateMessage);

        const numberOfCategoriesAfterUpdate = await categoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategoriesAfterUpdate).to.be.equal(arg.type === 'category' ? numberOfCategories + 1 : 1);
      });

      it(`should go to FO and check that the ${arg.type} does not exist`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkRedirectedCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // View shop
        page = await categoriesPage.viewMyShop(page);
        // Change FO language
        await foHomePage.changeLanguage(page, 'en');
        // Go to sitemap page
        await foHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        expect(pageTitle).to.equal(siteMapPage.pageTitle);

        // Check category name
        const categoryName = await siteMapPage.isVisibleCategory(page, idCategory);
        expect(categoryName).to.eq(false);
      });

      it('should check the HTTP code of the response', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `responseRedirectedCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // Check if it is an error page
        const response = await categoryPage.goTo(page, `${global.FO.URL}en/${idCategory}-${categoryFriendlyURL}`);
        expect(response).to.be.not.equal(null);
        const requestRedirectFrom = response!.request().redirectedFrom();
        expect(requestRedirectFrom).to.be.not.equal(null);
        const responseBeforeRedirection = await requestRedirectFrom!.response();
        expect(responseBeforeRedirection).to.be.not.equal(null);
        expect(responseBeforeRedirection!.status()).to.be.equal(parseInt(arg.category.redirectionWhenNotDisplayed, 10));

        // Check if it is redirected to the category
        const categoryName = await categoryPage.getHeaderPageName(page);
        expect(categoryName).to.contains(arg.category.redirectedCategory!.name.toUpperCase());
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBoDisabled${index}`, baseContext);

        const titlePage: string = arg.type === 'category'
          ? categoriesPage.pageTitle
          : categoriesPage.pageCategoryTitle(createCategoryData.name);

        // Close tab and init other page objects with new current tab
        page = await siteMapPage.closePage(browserContext, page, 0);

        const pageTitle = await categoriesPage.getPageTitle(page);
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

        await categoriesPage.goToSubMenu(
          page,
          categoriesPage.catalogParentLink,
          categoriesPage.categoriesLink,
        );

        const pageTitle = await categoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(categoriesPage.pageTitle);
      });

      it(`should filter list by Name '${arg.category.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterToUpdate${index}`, baseContext);

        await categoriesPage.resetFilter(page);
        await categoriesPage.filterCategories(
          page,
          'input',
          'name',
          arg.category.name,
        );

        const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
        expect(textColumn).to.contains(arg.category.name);
      });

      it(`should go to edit ${arg.type} page`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToEditCategoryPage1${index}`, baseContext);

        await categoriesPage.goToEditCategoryPage(page, 1);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        expect(pageTitle).to.contains(addCategoryPage.pageTitleEdit + arg.category.name);

        categoryFriendlyURL = await addCategoryPage.getValue(page, 'friendlyUrl');
      });

      it(`should set a new redirection type for ${arg.type}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `setNewRedirection${index}`, baseContext);

        arg.category.setRedirectionWhenNotDisplayed(arg.newRedirect);

        const textResult = await addCategoryPage.createEditCategory(page, arg.category);
        expect(textResult).to.equal(categoriesPage.successfulUpdateMessage);

        const numberOfCategoriesAfterUpdate = await categoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategoriesAfterUpdate).to.be.equal(arg.type === 'category' ? numberOfCategories + 1 : 1);
      });

      it(`should go to FO and check that the ${arg.type} does not exist`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkErroredCategoryFO${index}`, baseContext);

        const idCategory: number = arg.type === 'category' ? categoryID : subcategoryID;

        // View shop
        page = await categoriesPage.viewMyShop(page);
        // Change FO language
        await foHomePage.changeLanguage(page, 'en');
        // Go to sitemap page
        await foHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        expect(pageTitle).to.equal(siteMapPage.pageTitle);

        // Check category name
        const categoryName = await siteMapPage.isVisibleCategory(page, idCategory);
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
          ? categoriesPage.pageTitle
          : categoriesPage.pageCategoryTitle(createCategoryData.name);

        // Close tab and init other page objects with new current tab
        page = await siteMapPage.closePage(browserContext, page, 0);

        const pageTitle = await categoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(titlePage);
      });
    });
  });

  // 5 : Delete Category from BO
  describe('Delete Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToDelete', baseContext);

      await categoriesPage.goToSubMenu(
        page,
        categoriesPage.catalogParentLink,
        categoriesPage.categoriesLink,
      );

      const pageTitle = await categoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it(`should filter list by Name '${createCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await categoriesPage.resetFilter(page);
      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        createCategoryData.name,
      );
      categoryID = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await categoriesPage.deleteCategory(page, 1);
      expect(textResult).to.equal(categoriesPage.successfulDeleteMessage);

      const numberOfCategoriesAfterDeletion = await categoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // View shop
      page = await categoriesPage.viewMyShop(page);
      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should check that the deleted category does not exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedCategoryFO', baseContext);

      // Go to sitemap page
      await foHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await siteMapPage.getPageTitle(page);
      expect(pageTitle).to.equal(siteMapPage.pageTitle);

      const categoryName = await siteMapPage.isVisibleCategory(page, categoryID);
      expect(categoryName, 'Category is visible in FO!').to.eq(false);
    });
  });
});
