// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import categoriesPage from '@pages/BO/catalog/categories';
import addCategoryPage from '@pages/BO/catalog/categories/add';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import categoryPage from '@pages/FO/category';
import {homePage as foHomePage} from '@pages/FO/home';
import {siteMapPage} from '@pages/FO/siteMap';

// Import data
import CategoryData from '@data/faker/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_categories_CRUDCategoryInBO';

// Create, Read, Update and Delete Category
describe('BO - Catalog - Categories : CRUD Category in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCategories: number = 0;
  let categoryID: number = 0;
  let subcategoryID: number = 0;

  const createCategoryData: CategoryData = new CategoryData();
  const createSubCategoryData: CategoryData = new CategoryData({name: 'subCategoryToCreate'});
  const editCategoryData: CategoryData = new CategoryData({displayed: false, name: `update${createCategoryData.name}`});

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create categories images
    await Promise.all([
      files.generateImage(`${createCategoryData.name}.jpg`),
      files.generateImage(`${createSubCategoryData.name}.jpg`),
      files.generateImage(`${editCategoryData.name}.jpg`),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    /* Delete the generated images */
    await Promise.all([
      files.deleteFile(`${createCategoryData.name}.jpg`),
      files.deleteFile(`${createSubCategoryData.name}.jpg`),
      files.deleteFile(`${editCategoryData.name}.jpg`),
    ]);
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

  it('should reset all filters and get number of categories in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

    numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfCategories).to.be.above(0);
  });

  // 1 : Create category and subcategory then go to FO to check the existence of the new categories
  describe('Create Category and subcategory in BO then check it in FO', async () => {
    describe('Create Category and check it in FO', async () => {
      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewCategoryPage', baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create category and check the categories number', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, createCategoryData);
        await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await categoriesPage.getNumberOfElementInGrid(page);
        await expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
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
        await expect(numberOfCategoriesAfterFilter).to.be.at.most(numberOfCategories);

        for (let i: number = 1; i <= numberOfCategoriesAfterFilter; i++) {
          const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, i, 'name');
          await expect(textColumn).to.contains(createCategoryData.name);
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
        await expect(isHomePage, 'Fail to open FO home page').to.be.true;
      });

      it('should check the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkCreatedCategoryFO', baseContext);

        // Go to sitemap page
        await foHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        await expect(pageTitle).to.equal(siteMapPage.pageTitle);

        // Check category name
        const categoryName = await siteMapPage.getCategoryName(page, categoryID);
        await expect(categoryName).to.contains(createCategoryData.name);
      });

      it('should view the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedCategoryFO', baseContext);

        await siteMapPage.viewCreatedCategory(page, categoryID);

        // Check category name
        const pageTitle = await categoryPage.getHeaderPageName(page);
        await expect(pageTitle).to.contains(createCategoryData.name.toUpperCase());

        // Check category description
        const categoryDescription = await categoryPage.getCategoryDescription(page);
        await expect(categoryDescription).to.equal(createCategoryData.description);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo1', baseContext);

        // Close tab and init other page objects with new current tab
        page = await categoryPage.closePage(browserContext, page, 0);

        const pageTitle = await categoriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(categoriesPage.pageTitle);
      });
    });

    describe('Create Subcategory and check it in FO', async () => {
      it('should display the subcategories table related to the created category', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'displaySubcategoriesForCreatedCategory', baseContext);

        await categoriesPage.goToViewSubCategoriesPage(page, 1);

        const pageTitle = await categoriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(createCategoryData.name);
      });

      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewSubcategoryPage', baseContext);

        await categoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await addCategoryPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
      });

      it('should create a subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createSubcategory', baseContext);

        const textResult = await addCategoryPage.createEditCategory(page, createSubCategoryData);
        await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);
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
        await expect(textColumn).to.contains(createSubCategoryData.name);
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
        await expect(pageTitle).to.equal(siteMapPage.pageTitle);

        // Check category
        const categoryName = await siteMapPage.getCategoryName(page, subcategoryID);
        await expect(categoryName).to.contains(createSubCategoryData.name);
      });

      it('should view the created subcategory', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedSubcategoryFO', baseContext);

        await siteMapPage.viewCreatedCategory(page, subcategoryID);

        // Check subcategory name
        const pageTitle = await categoryPage.getHeaderPageName(page);
        await expect(pageTitle).to.contains(createSubCategoryData.name.toUpperCase());

        // Check subcategory description
        const subcategoryDescription = await categoryPage.getCategoryDescription(page);
        await expect(subcategoryDescription).to.equal(createSubCategoryData.description);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo2', baseContext);

        // Close tab and init other page objects with new current tab
        page = await categoryPage.closePage(browserContext, page, 0);

        const pageTitle = await categoriesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(createCategoryData.name);
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
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
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
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should click on view category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToViewCreatedCategoryPage', baseContext);

      await categoriesPage.goToViewSubCategoriesPage(page, 1);

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createCategoryData.name);
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
      await expect(textColumn).to.contains(createSubCategoryData.name);
    });
  });

  // 3 : Update category and check that category isn't displayed in FO (displayed = false)
  describe('Update Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToUpdate', baseContext);

      await categoriesPage.goToSubMenu(
        page,
        categoriesPage.catalogParentLink,
        categoriesPage.categoriesLink,
      );

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it(`should filter list by Name '${createCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToUpdate', baseContext);

      await categoriesPage.resetFilter(page);
      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        createCategoryData.name,
      );

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      await expect(textColumn).to.contains(createCategoryData.name);
    });

    it('should go to edit category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCategoryPage', baseContext);

      await categoriesPage.goToEditCategoryPage(page, 1);

      const pageTitle = await addCategoryPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addCategoryPage.pageTitleEdit + createCategoryData.name);
    });

    it('should update the category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCategory', baseContext);

      const textResult = await addCategoryPage.createEditCategory(page, editCategoryData);
      await expect(textResult).to.equal(categoriesPage.successfulUpdateMessage);

      const numberOfCategoriesAfterUpdate = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterUpdate).to.be.equal(numberOfCategories + 1);
    });

    it('should search for the new category and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchForUpdatedCategory', baseContext);

      await categoriesPage.resetFilter(page);
      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        editCategoryData.name,
      );

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      await expect(textColumn).to.contains(editCategoryData.name);
    });

    it('should go to FO and check that the category does not exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkUpdatedCategoryFO', baseContext);

      // View shop
      page = await categoriesPage.viewMyShop(page);
      // Change FO language
      await foHomePage.changeLanguage(page, 'en');
      // Go to sitemap page
      await foHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);

      // Check category name
      const categoryName = await siteMapPage.isVisibleCategory(page, categoryID);
      await expect(categoryName).to.be.false;
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBo3', baseContext);

      // Close tab and init other page objects with new current tab
      page = await siteMapPage.closePage(browserContext, page, 0);

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });
  });

  // 4 : Delete Category from BO
  describe('Delete Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToDelete', baseContext);

      await categoriesPage.goToSubMenu(
        page,
        categoriesPage.catalogParentLink,
        categoriesPage.categoriesLink,
      );

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it(`should filter list by Name '${editCategoryData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await categoriesPage.resetFilter(page);
      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        editCategoryData.name,
      );
      categoryID = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      await expect(textColumn).to.contains(editCategoryData.name);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await categoriesPage.deleteCategory(page, 1);
      await expect(textResult).to.equal(categoriesPage.successfulDeleteMessage);

      const numberOfCategoriesAfterDeletion = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFO', baseContext);

      // View shop
      page = await categoriesPage.viewMyShop(page);
      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should check that the deleted category does not exist', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDeletedCategoryFO', baseContext);

      // Go to sitemap page
      await foHomePage.goToFooterLink(page, 'Sitemap');

      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);

      const categoryName = await siteMapPage.isVisibleCategory(page, categoryID);
      await expect(categoryName, 'Category is visible in FO!').to.be.false;
    });
  });
});
