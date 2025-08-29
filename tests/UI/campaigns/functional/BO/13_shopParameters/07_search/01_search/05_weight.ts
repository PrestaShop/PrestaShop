import {expect} from 'chai';
import bulkDeleteCategoriesTest from '@commonTests/BO/catalog/category';
import {bulkDeleteFeaturesTest} from '@commonTests/BO/catalog/features';
import opsBulkDeleteAttributes from '@commonTests/BO/catalog/attributes';
import opsBulkDeleteBrands from '@commonTests/BO/catalog/brands';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import testContext from '@utils/testContext';

import {
  boAttributesPage,
  boAttributesCreatePage,
  boAttributesValueCreatePage,
  boAttributesViewPage,
  boBrandsCreatePage,
  boBrandsPage,
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boFeaturesPage,
  boFeaturesCreatePage,
  boFeaturesValueCreatePage,
  boFeaturesViewPage,
  boLoginPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  boProductsPage,
  boSearchPage,
  type BrowserContext,
  FakerAttribute,
  FakerAttributeValue,
  FakerBrand,
  FakerCategory,
  FakerFeature,
  FakerFeatureValue,
  FakerProduct,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_weight';

describe('BO - Shop Parameters - Search: Weight', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;
  let numberOfBrands: number = 0;
  let numberOfCategories: number = 0;
  let numberOfFeatures: number = 0;
  let attributeId: number = 0;

  const searchValue: string = 'preston';
  const brandsTable: string = 'manufacturer';
  const attributeData: FakerAttribute = new FakerAttribute({
    name: searchValue,
    publicName: searchValue,
  });
  const attributeValueData: FakerAttributeValue = new FakerAttributeValue({
    attributeName: attributeData.name,
    value: searchValue,
  });
  const brandData: FakerBrand = new FakerBrand({
    name: searchValue,
  });
  const categoryData: FakerCategory = new FakerCategory({
    name: searchValue,
  });
  const featureData: FakerFeature = new FakerFeature({
    name: searchValue,
  });
  const featureValueData: FakerFeatureValue = new FakerFeatureValue({
    featureName: featureData.name,
    value: searchValue,
  });
  const productData1: FakerProduct = new FakerProduct({
    name: searchValue,
  });
  const productData2: FakerProduct = new FakerProduct({
    name: 'Product Reference',
    reference: searchValue,
  });
  const productData3: FakerProduct = new FakerProduct({
    name: 'Product Summary',
    summary: searchValue,
  });
  const productData4: FakerProduct = new FakerProduct({
    name: 'Product Description',
    description: searchValue,
  });
  const productData5: FakerProduct = new FakerProduct({
    name: 'Product Category',
    category: searchValue,
  });
  const productData6: FakerProduct = new FakerProduct({
    name: 'Product Brand',
    brand: brandData,
  });
  const productData7: FakerProduct = new FakerProduct({
    name: 'Product Tags',
    tags: searchValue,
  });
  const productData8: FakerProduct = new FakerProduct({
    name: 'Product Attributes',
    type: 'combinations',
    attributes: [
      {
        name: attributeData.name,
        values: [
          attributeValueData.attributeName,
        ],
      },
    ],
  });
  const productData9: FakerProduct = new FakerProduct({
    name: 'Product Features',
    features: [
      {
        featureName: featureData.name,
        preDefinedValue: featureValueData.featureName,
      },
    ],
  });
  const productsData: FakerProduct[] = [
    productData1,
    productData2,
    productData3,
    productData4,
    productData5,
    productData6,
    productData7,
    productData8,
    productData9,
  ];

  describe('Weight', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);

      await Promise.all([
        utilsFile.generateImage(brandData.logo),
      ]);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);

      await Promise.all([
        utilsFile.deleteFile(brandData.logo),
      ]);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Shop Parameters > Search\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.searchLink,
      );

      const pageTitle = await boSearchPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSearchPage.pageTitle);
    });

    it('should check Weight', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkWeight', baseContext);

      const weightAttributes = await boSearchPage.getWeightInputValue(page, 'Attributes weight');
      expect(weightAttributes).to.equals(2);

      const weightBrand = await boSearchPage.getWeightInputValue(page, 'Brand weight');
      expect(weightBrand).to.equals(3);

      const weightCategory = await boSearchPage.getWeightInputValue(page, 'Category weight');
      expect(weightCategory).to.equals(3);

      const weightDescription = await boSearchPage.getWeightInputValue(page, 'Description weight');
      expect(weightDescription).to.equals(1);

      const weightFeatures = await boSearchPage.getWeightInputValue(page, 'Features weight');
      expect(weightFeatures).to.equals(2);

      const weightProductName = await boSearchPage.getWeightInputValue(page, 'Product name weight');
      expect(weightProductName).to.equals(6);

      const weightReference = await boSearchPage.getWeightInputValue(page, 'Reference weight');
      expect(weightReference).to.equals(10);

      const weightShortDescription = await boSearchPage.getWeightInputValue(page, 'Short description weight');
      expect(weightShortDescription).to.equals(1);

      const weightTags = await boSearchPage.getWeightInputValue(page, 'Tags weight');
      expect(weightTags).to.equals(4);
    });

    describe('Create category', async () => {
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
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersCategories', baseContext);

        numberOfCategories = await boCategoriesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCategories).to.be.above(0);
      });

      it('should go to add new category page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddCategoryPage', baseContext);

        await boCategoriesPage.goToAddNewCategoryPage(page);

        const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
      });

      it('should create category and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

        const textResult = await boCategoriesCreatePage.createEditCategory(page, categoryData);
        expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);

        const numberOfCategoriesAfterCreation = await boCategoriesPage.getNumberOfElementInGrid(page);
        expect(numberOfCategoriesAfterCreation).to.be.equal(numberOfCategories + 1);
      });
    });

    describe('Create brand', async () => {
      it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.brandsAndSuppliersLink,
        );
        await boBrandsPage.closeSfToolBar(page);

        const pageTitle = await boBrandsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boBrandsPage.pageTitle);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersBrands', baseContext);

        numberOfBrands = await boBrandsPage.resetAndGetNumberOfLines(page, brandsTable);
        expect(numberOfBrands).to.be.above(0);
      });

      it('should go to new brand page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddBrandPage', baseContext);

        await boBrandsPage.goToAddNewBrandPage(page);

        const pageTitle = await boBrandsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boBrandsCreatePage.pageTitle);
      });

      it('should create brand', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createBrand', baseContext);

        const result = await boBrandsCreatePage.createEditBrand(page, brandData);
        expect(result).to.equal(boBrandsPage.successfulCreationMessage);

        const numberOfBrandsAfterCreation = await boBrandsPage.getNumberOfElementInGrid(page, brandsTable);
        expect(numberOfBrandsAfterCreation).to.be.equal(numberOfBrands + 1);
      });
    });

    describe('Create attribute', async () => {
      it('should go to \'Catalog > Attributes & Features\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.attributesAndFeaturesLink,
        );
        await boAttributesPage.closeSfToolBar(page);

        const pageTitle = await boAttributesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boAttributesPage.pageTitle);
      });

      it('should reset all filters and get number of attributes in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersAttributes', baseContext);

        numberOfAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
        expect(numberOfAttributes).to.be.above(0);
      });

      it('should go to add new attribute page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAttributePage', baseContext);

        await boAttributesPage.goToAddAttributePage(page);

        const pageTitle = await boAttributesCreatePage.getPageTitle(page);
        expect(pageTitle).to.equal(boAttributesCreatePage.createPageTitle);
      });

      it('should create new attribute', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createNewAttribute', baseContext);

        const textResult = await boAttributesCreatePage.addEditAttribute(page, attributeData);
        expect(textResult).to.contains(boAttributesPage.successfulCreationMessage);

        const numberOfAttributesAfterCreation = await boAttributesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
      });

      it('should filter list of attributes', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

        await boAttributesPage.filterTable(page, 'name', attributeData.name);

        const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(attributeData.name);

        attributeId = parseInt(await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
        expect(attributeId).to.be.gt(0);
      });

      it('should view attribute', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedAttribute', baseContext);

        await boAttributesPage.viewAttribute(page, 1);

        const pageTitle = await boAttributesViewPage.getPageTitle(page);
        expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(attributeData.name));
      });

      it('should go to add new value page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

        await boAttributesViewPage.goToAddNewValuePage(page);

        const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
        expect(pageTitle).to.equal(boAttributesValueCreatePage.createPageTitle);
      });

      it('should create value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createValue', baseContext);

        attributeValueData.setAttributeId(attributeId);

        const textResult = await boAttributesValueCreatePage.addEditValue(page, attributeValueData);
        expect(textResult).to.contains(boAttributesViewPage.successfulCreationMessage);
      });
    });

    describe('Create feature', async () => {
      it('should go to \'Catalog > Attributes & Features\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesFeaturesPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.catalogParentLink,
          boDashboardPage.attributesAndFeaturesLink,
        );
        await boAttributesPage.closeSfToolBar(page);

        const pageTitle = await boAttributesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boAttributesPage.pageTitle);
      });

      it('should go to Features page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

        await boAttributesPage.goToFeaturesPage(page);

        const pageTitle = await boFeaturesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
      });

      it('should reset all filters and get number of features in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersFeatures', baseContext);

        numberOfFeatures = await boFeaturesPage.resetAndGetNumberOfLines(page);
        expect(numberOfFeatures).to.be.above(0);
      });

      it('should go to add new feature page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewFeaturePage', baseContext);

        await boFeaturesPage.goToAddFeaturePage(page);

        const pageTitle = await boFeaturesCreatePage.getPageTitle(page);
        expect(pageTitle).to.eq(boFeaturesCreatePage.createPageTitle);
      });

      it('should create feature', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createNewFeature', baseContext);

        const textResult = await boFeaturesCreatePage.setFeature(page, featureData);
        expect(textResult).to.contains(boFeaturesPage.successfulCreationMessage);
      });

      it('should filter list of features by the created feature', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterFeature', baseContext);

        await boFeaturesPage.filterTable(page, 'name', featureData.name);

        const textColumn = await boFeaturesPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(featureData.name);
      });

      it('should view feature', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewFeature', baseContext);

        await boFeaturesPage.viewFeature(page, 1);

        const pageTitle = await boFeaturesViewPage.getPageTitle(page);
        expect(pageTitle).to.contains(`${featureData.name} • ${global.INSTALL.SHOP_NAME}`);
      });

      it('should go to add new value page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewValuePage', baseContext);

        await boFeaturesViewPage.goToAddNewValuePage(page);

        const pageTitle = await boFeaturesValueCreatePage.getPageTitle(page);
        expect(pageTitle).to.eq(boFeaturesValueCreatePage.createPageTitle);
      });

      it('should create value', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createNewValue', baseContext);

        const textResult = await boFeaturesValueCreatePage.addEditValue(page, featureValueData);
        expect(textResult).to.contains(boFeaturesValueCreatePage.successfulCreationMessage);
      });
    });

    describe('Create products', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
        await boProductsPage.closeSfToolBar(page);

        const pageTitle = await boProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsPage.pageTitle);
      });

      it('should reset all filters and get number of products', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersProducts', baseContext);

        const numberOfProducts = await boProductsPage.resetAndGetNumberOfLines(page);
        expect(numberOfProducts).to.be.above(0);
      });

      productsData.forEach((product: FakerProduct, index: number) => {
        describe(`Create product : '${product.name}'`, async () => {
          if (index === 0) {
            it('should click on \'New product\' button and check new product modal', async function () {
              await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

              const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
              expect(isModalVisible).to.be.eq(true);
            });

            it(`should choose '${product.type} product'`, async function () {
              await testContext.addContextItem(this, 'testIdentifier', `chooseStandardProduct${index}`, baseContext);

              await boProductsPage.selectProductType(page, product.type);

              const pageTitle = await boProductsCreatePage.getPageTitle(page);
              expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
            });
          }

          it('should go to new product page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${index}`, baseContext);

            if (index !== 0) {
              await boProductsCreatePage.clickOnNewProductButton(page);
            } else {
              await boProductsPage.clickOnAddNewProduct(page);
            }

            const pageTitle = await boProductsCreatePage.getPageTitle(page);
            expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
          });

          if (index !== 0) {
            it(`should choose '${product.type} product'`, async function () {
              await testContext.addContextItem(this, 'testIdentifier', `chooseStandardProduct${index}`, baseContext);

              await boProductsCreatePage.chooseProductType(page, product.type);

              const pageTitle = await boProductsCreatePage.getPageTitle(page);
              expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
            });
          }

          it(`create product '${product.name}'`, async function () {
            await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

            const createProductMessage = await boProductsCreatePage.setProduct(page, product);
            expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
          });

          if (product.type === 'combinations') {
            it('should create combinations and check generate combinations button', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

              const generateCombinationsButton = await boProductsCreateTabCombinationsPage.setProductAttributes(
                page,
                product.attributes,
              );
              expect(generateCombinationsButton).to.equal('Generate combination');
            });

            it('should click on generate combinations button', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

              const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
              expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(1));
            });

            it('should close combinations generation modal', async function () {
              await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed', baseContext);

              const isModalClosed = await boProductsCreateTabCombinationsPage.generateCombinationModalIsClosed(page);
              expect(isModalClosed).to.be.eq(true);

              await boProductsCreateTabCombinationsPage.editCombinationRowQuantity(page, 1, product.quantity);

              const successMessage = await boProductsCreateTabCombinationsPage.saveCombinationsForm(page);
              expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulUpdateMessage);

              const updateProductMessage = await boProductsCreatePage.saveProduct(page);
              expect(updateProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
            });
          }
        });
      });
    });

    describe('Search : Re-build the entire index', async () => {
      it('should go to \'Shop Parameters > Search\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToSearchPage2', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.searchLink,
        );

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });

      it('should click on "Re-build the entire index"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickRebuildEntireIndex', baseContext);

        const result = await boSearchPage.clickRebuildEntireIndex(page);
        expect(result).to.contains(boSearchPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShop', baseContext);

        page = await boSearchPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.equals(true);
      });

      it('should check the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPage', baseContext);

        await foClassicHomePage.searchProduct(page, searchValue);

        const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

        const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
        expect(searchInputValue).to.equals(searchValue);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should close the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeSearchPage2', baseContext);

        page = await foClassicSearchResultsPage.closePage(browserContext, page, 0);

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });
    });

    describe('Search : Change weight for checking order', async () => {
      it('should change weight', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeWeightOrder', baseContext);

        const resultProductName = await boSearchPage.setWeightInputValue(page, 'Product name weight', 1);
        expect(resultProductName).to.equals(boSearchPage.settingsUpdateMessage);

        const resultReference = await boSearchPage.setWeightInputValue(page, 'Reference weight', 2);
        expect(resultReference).to.equals(boSearchPage.settingsUpdateMessage);

        const resultShortDescription = await boSearchPage.setWeightInputValue(page, 'Short description weight', 3);
        expect(resultShortDescription).to.equals(boSearchPage.settingsUpdateMessage);

        const resultDescription = await boSearchPage.setWeightInputValue(page, 'Description weight', 4);
        expect(resultDescription).to.equals(boSearchPage.settingsUpdateMessage);

        const resultCategory = await boSearchPage.setWeightInputValue(page, 'Category weight', 5);
        expect(resultCategory).to.equals(boSearchPage.settingsUpdateMessage);

        const resultBrand = await boSearchPage.setWeightInputValue(page, 'Brand weight', 6);
        expect(resultBrand).to.equals(boSearchPage.settingsUpdateMessage);

        const resultTags = await boSearchPage.setWeightInputValue(page, 'Tags weight', 7);
        expect(resultTags).to.equals(boSearchPage.settingsUpdateMessage);

        const resultAttributes = await boSearchPage.setWeightInputValue(page, 'Attributes weight', 8);
        expect(resultAttributes).to.equals(boSearchPage.settingsUpdateMessage);

        const resultFeatures = await boSearchPage.setWeightInputValue(page, 'Features weight', 9);
        expect(resultFeatures).to.equals(boSearchPage.settingsUpdateMessage);
      });

      it('should click on "Re-build the entire index"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickRebuildEntireIndexWeightOrder', baseContext);

        const result = await boSearchPage.clickRebuildEntireIndex(page);
        expect(result).to.contains(boSearchPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShopWeightOrder', baseContext);

        page = await boSearchPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.equals(true);
      });

      it('should check the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageWeightOrder', baseContext);

        await foClassicHomePage.searchProduct(page, searchValue);

        const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

        const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
        expect(searchInputValue).to.equals(searchValue);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should go to the result #1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage1', baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.equal(productData9.name);
      });

      it('should return search results page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnSearchResults', baseContext);

        await foClassicProductPage.goToPreviousPage(page);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should go to the result #5', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage5', baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 5);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.equal(productData5.name);
      });

      it('should return search results page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnSearchResults2', baseContext);

        await foClassicProductPage.goToPreviousPage(page);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should go to the result #9', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage9', baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 9);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.equal(productData1.name);
      });

      it('should close the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeSearchPage3', baseContext);

        page = await foClassicSearchResultsPage.closePage(browserContext, page, 0);

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });
    });

    describe('Search : Change weight for reversing order', async () => {
      it('should change weight', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'changeWeightReverseOrder', baseContext);

        const resultProductName = await boSearchPage.setWeightInputValue(page, 'Product name weight', 9);
        expect(resultProductName).to.equals(boSearchPage.settingsUpdateMessage);

        const resultReference = await boSearchPage.setWeightInputValue(page, 'Reference weight', 8);
        expect(resultReference).to.equals(boSearchPage.settingsUpdateMessage);

        const resultShortDescription = await boSearchPage.setWeightInputValue(page, 'Short description weight', 7);
        expect(resultShortDescription).to.equals(boSearchPage.settingsUpdateMessage);

        const resultDescription = await boSearchPage.setWeightInputValue(page, 'Description weight', 6);
        expect(resultDescription).to.equals(boSearchPage.settingsUpdateMessage);

        const resultCategory = await boSearchPage.setWeightInputValue(page, 'Category weight', 5);
        expect(resultCategory).to.equals(boSearchPage.settingsUpdateMessage);

        const resultBrand = await boSearchPage.setWeightInputValue(page, 'Brand weight', 4);
        expect(resultBrand).to.equals(boSearchPage.settingsUpdateMessage);

        const resultTags = await boSearchPage.setWeightInputValue(page, 'Tags weight', 3);
        expect(resultTags).to.equals(boSearchPage.settingsUpdateMessage);

        const resultAttributes = await boSearchPage.setWeightInputValue(page, 'Attributes weight', 2);
        expect(resultAttributes).to.equals(boSearchPage.settingsUpdateMessage);

        const resultFeatures = await boSearchPage.setWeightInputValue(page, 'Features weight', 1);
        expect(resultFeatures).to.equals(boSearchPage.settingsUpdateMessage);
      });

      it('should click on "Re-build the entire index"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickRebuildEntireIndexWeightReverseOrder', baseContext);

        const result = await boSearchPage.clickRebuildEntireIndex(page);
        expect(result).to.contains(boSearchPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShopReverseOrder', baseContext);

        page = await boSearchPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.equals(true);
      });

      it('should check the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageReverseOrder', baseContext);

        await foClassicHomePage.searchProduct(page, searchValue);

        const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

        const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
        expect(searchInputValue).to.equals(searchValue);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should go to the result #1', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 1);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.equal(productData1.name);
      });

      it('should return search results page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnSearchResults4', baseContext);

        await foClassicProductPage.goToPreviousPage(page);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should go to the result #2', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage2', baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 2);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.equal(productData2.name);
      });

      it('should return search results page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'returnSearchResults5', baseContext);

        await foClassicProductPage.goToPreviousPage(page);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(9);
      });

      it('should go to the result #8', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage8', baseContext);

        await foClassicSearchResultsPage.goToProductPage(page, 8);

        const pageTitle = await foClassicProductPage.getPageTitle(page);
        expect(pageTitle).to.equal(productData8.name);
      });

      it('should close the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeSearchPage4', baseContext);

        page = await foClassicSearchResultsPage.closePage(browserContext, page, 0);

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });
    });

    describe('Search : Empty weight for product name', async () => {
      it('should change weight', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setWeight0', baseContext);

        const resultProductName = await boSearchPage.setWeightInputValue(page, 'Product name weight', 0);
        expect(resultProductName).to.equals(boSearchPage.settingsUpdateMessage);
      });

      it('should click on "Re-build the entire index"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'clickRebuildEntireIndexEmptyWeight', baseContext);

        const result = await boSearchPage.clickRebuildEntireIndex(page);
        expect(result).to.contains(boSearchPage.successfulUpdateMessage);
      });

      it('should view my shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'viewMyShopEmptyWeight', baseContext);

        page = await boSearchPage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.equals(true);
      });

      it('should check the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSearchPageEmptyWeight', baseContext);

        await foClassicHomePage.searchProduct(page, searchValue);

        const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);

        const searchInputValue = await foClassicSearchResultsPage.getSearchValue(page);
        expect(searchInputValue).to.equals(searchValue);

        const hasResults = await foClassicSearchResultsPage.hasResults(page);
        expect(hasResults).to.eq(true);

        const numResults = await foClassicSearchResultsPage.getSearchResultsNumber(page);
        expect(numResults).to.eq(8);

        const titleTable = await foClassicSearchResultsPage.getAllProductsAttribute(page, 'title');
        expect(titleTable.length).to.equals(8);
        for (let nthTable = 0; nthTable < titleTable.length; nthTable++) {
          expect(titleTable[nthTable]).to.not.contains(searchValue);
        }
      });

      it('should close the search page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'closeSearchPage', baseContext);

        page = await foClassicSearchResultsPage.closePage(browserContext, page, 0);

        const pageTitle = await boSearchPage.getPageTitle(page);
        expect(pageTitle).to.contains(boSearchPage.pageTitle);
      });
    });

    describe('Search : Reset weight', async () => {
      it('should change weight', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'resetWeight', baseContext);

        const resultProductName = await boSearchPage.setWeightInputValue(page, 'Product name weight', 6);
        expect(resultProductName).to.equals(boSearchPage.settingsUpdateMessage);

        const resultReference = await boSearchPage.setWeightInputValue(page, 'Reference weight', 10);
        expect(resultReference).to.equals(boSearchPage.settingsUpdateMessage);

        const resultShortDescription = await boSearchPage.setWeightInputValue(page, 'Short description weight', 1);
        expect(resultShortDescription).to.equals(boSearchPage.settingsUpdateMessage);

        const resultDescription = await boSearchPage.setWeightInputValue(page, 'Description weight', 1);
        expect(resultDescription).to.equals(boSearchPage.settingsUpdateMessage);

        const resultCategory = await boSearchPage.setWeightInputValue(page, 'Category weight', 3);
        expect(resultCategory).to.equals(boSearchPage.settingsUpdateMessage);

        const resultBrand = await boSearchPage.setWeightInputValue(page, 'Brand weight', 3);
        expect(resultBrand).to.equals(boSearchPage.settingsUpdateMessage);

        const resultTags = await boSearchPage.setWeightInputValue(page, 'Tags weight', 4);
        expect(resultTags).to.equals(boSearchPage.settingsUpdateMessage);

        const resultAttributes = await boSearchPage.setWeightInputValue(page, 'Attributes weight', 2);
        expect(resultAttributes).to.equals(boSearchPage.settingsUpdateMessage);

        const resultFeatures = await boSearchPage.setWeightInputValue(page, 'Features weight', 2);
        expect(resultFeatures).to.equals(boSearchPage.settingsUpdateMessage);
      });
    });

    // POST-COND : Delete products
    productsData.forEach((product: FakerProduct, index: number) => {
      deleteProductTest(product, `${baseContext}_postTest_0_${index}`);
    });
    // POST COND : Delete brand
    opsBulkDeleteBrands(brandData.name, `${baseContext}_postTest_1`);
    // POST COND : Delete category
    bulkDeleteCategoriesTest({filterBy: 'name', value: categoryData.name}, `${baseContext}_postTest_2`);
    // POST COND : Delete feature
    bulkDeleteFeaturesTest(featureData.name, `${baseContext}_postTest_3`);
    // POST COND : Delete attribute
    opsBulkDeleteAttributes(attributeData.name, `${baseContext}_postTest_4`);
  });
});
