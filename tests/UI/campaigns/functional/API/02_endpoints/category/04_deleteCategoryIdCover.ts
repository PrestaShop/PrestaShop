// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boCategoriesCreatePage,
  boCategoriesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerCategory,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_category_deleteCategoryIdCover';

describe('API : DELETE /admin-api/categories/{categoryId}/cover', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let idCategory: number;
  let accessToken: string;
  let numberOfCategories: number = 0;

  const clientScope: string = 'category_write';
  const createCategory: FakerCategory = new FakerCategory({
    coverImage: 'coverFakerCategory.jpg',
    displayed: true,
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);

    // Create category image
    await Promise.all([
      utilsFile.generateImage('coverFakerCategory.jpg'),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    // Delete category image
    await Promise.all([
      utilsFile.deleteFile('coverFakerCategory.jpg'),
    ]);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Create a category', async () => {
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

    it('should go to add new category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCategoryPage', baseContext);

      await boCategoriesPage.goToAddNewCategoryPage(page);

      const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
    });

    it('should create category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

      const textResult = await boCategoriesCreatePage.createEditCategory(page, createCategory);
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
        createCategory.name,
      );

      const numRows = await boCategoriesPage.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(1);

      idCategory = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
      expect(idCategory).to.greaterThan(0);
    });

    it('should go to the Edit Category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditCategoryPage', baseContext);

      await boCategoriesPage.goToEditCategoryPage(page, 1);

      const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(createCategory.name);
    });

    it('should check the category cover is present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryCoverPresent', baseContext);

      const coverImagePath = await boCategoriesCreatePage.getValue(page, 'cover_image');
      expect(coverImagePath.length).to.be.gt(0);
    });
  });

  describe('API : Delete the Category Cover', async () => {
    it('should request the endpoint /categories/{categoryId}/cover', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`categories/${idCategory}/cover`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Category Cover is deleted', async () => {
    it('should check the category cover is deleted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCategoryCoverDeleted', baseContext);

      await boCategoriesCreatePage.reloadPage(page);

      const coverImagePath = await boCategoriesCreatePage.getValue(page, 'cover_image');
      expect(coverImagePath.length).to.be.equals(0);
    });
  });

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

    it(`should filter list by Name '${createCategory.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDelete', baseContext);

      await boCategoriesPage.resetFilter(page);
      await boCategoriesPage.filterCategories(
        page,
        'input',
        'name',
        createCategory.name,
      );

      const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      expect(textColumn).to.contains(createCategory.name);

      const numCategories = await boCategoriesPage.getNumberOfElementInGrid(page);
      expect(numCategories).to.equals(1);
    });

    it('should delete category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCategory', baseContext);

      const textResult = await boCategoriesPage.deleteCategory(page, 1);
      expect(textResult).to.equal(boCategoriesPage.successfulDeleteMessage);

      const numberOfCategoriesAfterDeletion = await boCategoriesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCategoriesAfterDeletion).to.be.equal(numberOfCategories);
    });
  });
});
