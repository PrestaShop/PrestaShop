// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {
  type APIRequestContext,
  boCategoriesCreatePage,
  boCategoriesPage,
  boDashboardPage,
  boLoginPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  boProductsPage,
  type BrowserContext,
  FakerCategory,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_category_deleteCategoryIdDeleteMode';

describe('API : DELETE /admin-api/categories/{categoryId}/{deleteMode}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let numberOfCategories: number = 0;

  const clientScope: string = 'category_write';
  const createCategory1: FakerCategory = new FakerCategory({
    displayed: true,
  });
  const createCategory2: FakerCategory = new FakerCategory({
    displayed: true,
  });
  const createCategory3: FakerCategory = new FakerCategory({
    displayed: true,
  });
  const createProduct1: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });
  const createProduct2: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });
  const createProduct3: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });
  const dataCreated: {category: FakerCategory, product: FakerProduct}[] = [
    {
      category: createCategory1,
      product: createProduct1,
    },
    {
      category: createCategory2,
      product: createProduct2,
    },
    {
      category: createCategory3,
      product: createProduct3,
    },
  ];
  const idCategories: number[] = [];

  createProductTest(createProduct1, `${baseContext}_preTest_0`);
  createProductTest(createProduct2, `${baseContext}_preTest_1`);
  createProductTest(createProduct3, `${baseContext}_preTest_2`);

  describe('API : DELETE /admin-api/categories/{categoryId}/{deleteMode}', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);

      apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    describe('API : Fetch the access token', async () => {
      it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
        accessToken = await requestAccessToken(clientScope);
      });
    });

    describe('BackOffice : Create categories & Link Products', async () => {
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

      dataCreated.forEach((datum:{category: FakerCategory, product: FakerProduct}, index: number) => {
        it('should go to add new category page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewCategoryPage${index}`, baseContext);

          await boCategoriesPage.goToAddNewCategoryPage(page);

          const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
        });

        it('should create category and check the categories number', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCategory${index}`, baseContext);

          const textResult = await boCategoriesCreatePage.createEditCategory(page, datum.category);
          expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);

          const numberOfCategoriesAfterCreation = await boCategoriesPage.getNumberOfElementInGrid(page);
          expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1 + index);
        });

        it('should search for the new category and check result', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchCreatedCategory${index}`, baseContext);

          await boCategoriesPage.filterCategories(
            page,
            'input',
            'name',
            datum.category.name,
          );

          const numRows = await boCategoriesPage.getNumberOfElementInGrid(page);
          expect(numRows).to.equal(1);

          const idCategory = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
          expect(idCategory).to.greaterThan(0);

          idCategories.push(idCategory);
        });

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

          const pageTitle = await boProductsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductsPage.pageTitle);
        });

        it('should filter list by name', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `filterProduct${index}`, baseContext);

          await boProductsPage.resetFilter(page);
          await boProductsPage.filterProducts(page, 'product_name', datum.product.name);

          const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
          expect(numProducts).to.be.equal(1);

          const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
          expect(productName).to.contains(datum.product.name);
        });

        it('should go to \'Catalog > Products > Product\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductPage${index}`, baseContext);

          await boProductsPage.goToProductPage(page, 1);

          const pageTitle = await boProductsCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
        });

        it('should set category', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `setCategory${index}`, baseContext);

          await boProductsCreateTabDescriptionPage.addNewCategory(page, [datum.category.name]);
          await boProductsCreateTabDescriptionPage.deleteCategory(page, 'Home');

          const createProductMessage = await boProductsCreatePage.saveProduct(page);
          expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);

          const selectedCategories = await boProductsCreateTabDescriptionPage.getSelectedCategories(page);
          expect(selectedCategories).to.eq(`${datum.category.name} x`);
        });

        it('should go to \'Catalog > Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `returnToCategoriesPage${index}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.catalogParentLink,
            boDashboardPage.categoriesLink,
          );
          await boCategoriesPage.closeSfToolBar(page);
          await boCategoriesPage.resetFilter(page);

          const pageTitle = await boCategoriesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
        });
      });
    });

    describe('API : Delete the Category (mode="associate_and_disable")', async () => {
      it('should request the endpoint /categories/{categoryId}/{deleteMode}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointMode_associate_and_disable', baseContext);

        const apiResponse = await apiContext.delete(`categories/${idCategories[0]}/associate_and_disable`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
        });
        expect(apiResponse.status()).to.eq(204);
      });
    });

    describe('BackOffice : Check the Category is deleted with mode "associate_and_disable" & Check product', async () => {
      it('should filter list of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDelete0', baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(page, 'input', 'id_category', idCategories[0].toString());

        const numberOfAttributesAfterDelete = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterDelete).to.equal(0);
      });

      it('should reset all filters and get number of categories in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete0', baseContext);

        const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategories).to.be.above(0);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageAfterDelete0', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterProductAfterDelete0', baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', dataCreated[0].product.name);

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(dataCreated[0].product.name);
      });

      it('should go to \'Catalog > Products > Product\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageAfterDelete0', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should check product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductAfterDelete0', baseContext);

        const productStatus = await boProductsCreatePage.getProductStatus(page);
        expect(productStatus).to.equals(false);

        const productCategories = await boProductsCreateTabDescriptionPage.getSelectedCategories(page);
        expect(productCategories).to.eq('Home x');
      });

      it('should go to \'Catalog > Categories\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnToCategoriesPageAfterDelete0', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.categoriesLink,
        );
        await boCategoriesPage.closeSfToolBar(page);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
      });
    });

    describe('API : Delete the Category (mode="associate_only")', async () => {
      it('should request the endpoint /categories/{categoryId}/{deleteMode}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointMode_associate_only', baseContext);

        const apiResponse = await apiContext.delete(`categories/${idCategories[1]}/associate_only`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
        });
        expect(apiResponse.status()).to.eq(204);
      });
    });

    describe('BackOffice : Check the Category is deleted with mode "associate_only" & Check product', async () => {
      it('should filter list of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDelete1', baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(page, 'input', 'id_category', idCategories[1].toString());

        const numberOfAttributesAfterDelete = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterDelete).to.equal(0);
      });

      it('should reset all filters and get number of categories in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete1', baseContext);

        const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategories).to.be.above(0);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageAfterDelete1', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterProductAfterDelete1', baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', dataCreated[1].product.name);

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(dataCreated[1].product.name);
      });

      it('should go to \'Catalog > Products > Product\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPageAfterDelete1', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should check product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkProductAfterDelete1', baseContext);

        const productStatus = await boProductsCreatePage.getProductStatus(page);
        expect(productStatus).to.equals(true);

        const productCategories = await boProductsCreateTabDescriptionPage.getSelectedCategories(page);
        expect(productCategories).to.eq('Home x');
      });

      it('should go to \'Catalog > Categories\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnToCategoriesPageAfterDelete1', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.categoriesLink,
        );
        await boCategoriesPage.closeSfToolBar(page);

        const pageTitle = await boCategoriesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
      });
    });

    describe('API : Delete the Category (mode="remove_associated")', async () => {
      it('should request the endpoint /categories/{categoryId}/{deleteMode}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointMode_remove_associated', baseContext);

        const apiResponse = await apiContext.delete(`categories/${idCategories[2]}/remove_associated`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
        });
        expect(apiResponse.status()).to.eq(204);
      });
    });

    describe('BackOffice : Check the Category is deleted with mode "remove_associated" & Check product', async () => {
      it('should filter list of categories', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDelete2', baseContext);

        await boCategoriesPage.resetFilter(page);
        await boCategoriesPage.filterCategories(page, 'input', 'id_category', idCategories[2].toString());

        const numberOfAttributesAfterDelete = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterDelete).to.equal(0);
      });

      it('should reset all filters and get number of categories in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete2', baseContext);

        const numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategories).to.be.above(0);
      });

      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageAfterDelete2', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterProductAfterDelete2', baseContext);

        await boProductsPage.resetFilter(page);
        await boProductsPage.filterProducts(page, 'product_name', dataCreated[2].product.name);

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(0);
      });
    });
  });

  deleteProductTest(createProduct1, `${baseContext}_postTest_0`);
  deleteProductTest(createProduct2, `${baseContext}_postTest_1`);
});
