import testContext from '@utils/testContext';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  boAttributesPage,
  boAttributesCreatePage,
  boAttributesValueCreatePage,
  boAttributesViewPage,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabCombinationsPage,
  type BrowserContext,
  FakerAttribute,
  FakerAttributeValue,
  FakerProduct,
  foClassicHomePage,
  foClassicProductPage,
  foClassicSearchResultsPage,
  type Page,
  type ProductAttribute,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_FO_classic_productPage_productPage_changeCombination';

describe('FO - Product page - Product page : Change combination', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const createAttributeData: FakerAttribute = new FakerAttribute({name: 'Emballage', attributeType: 'Radio buttons'});
  const valuesToCreate: FakerAttributeValue[] = [
    new FakerAttributeValue({attributeName: 'Emballage', value: 'Soie'}),
    new FakerAttributeValue({attributeName: 'Emballage', value: 'Carton'}),
  ];
  let numberOfAttributes: number = 0;
  let attributeId: number = 0;
  // Data to create product with combinations
  const newProductData: FakerProduct = new FakerProduct({
    type: 'combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    attributes: [
      {
        name: 'Emballage',
        values: ['Soie', 'Carton'],
      },
      {
        name: 'Size',
        values: ['S', 'M', 'L', 'XL'],
      },
    ],
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.generateImage(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.generateImage(newProductData.thumbImage);
    }
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    if (newProductData.coverImage) {
      await utilsFile.deleteFile(newProductData.coverImage);
    }
    if (newProductData.thumbImage) {
      await utilsFile.deleteFile(newProductData.thumbImage);
    }
  });

  describe('Create new attribute and values', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

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
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

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

      const textResult = await boAttributesCreatePage.addEditAttribute(page, createAttributeData);
      expect(textResult).to.contains(boAttributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await boAttributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
    });

    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await boAttributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);

      attributeId = parseInt(await boAttributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
      expect(attributeId).to.be.gt(0);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedAttribute', baseContext);

      await boAttributesPage.viewAttribute(page, 1);

      const pageTitle = await boAttributesViewPage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesViewPage.pageTitle(createAttributeData.name));
    });

    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

      await boAttributesViewPage.goToAddNewValuePage(page);

      const pageTitle = await boAttributesValueCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boAttributesValueCreatePage.createPageTitle);
    });

    valuesToCreate.forEach((valueToCreate: FakerAttributeValue, index: number) => {
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createValue${index}`, baseContext);

        valueToCreate.setAttributeId(attributeId);
        const textResult = await boAttributesValueCreatePage.addEditValue(page, valueToCreate, index === 0);
        expect(textResult).to.contains(boAttributesViewPage.successfulCreationMessage);
      });
    });
  });

  describe('Create product with combination', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.productsLink,
      );

      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should select the product with combination and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton = await boProductsCreateTabCombinationsPage.setProductAttributes(
        page,
        newProductData.attributes,
      );
      expect(generateCombinationsButton).to.equal(boProductsCreateTabCombinationsPage.generateCombinationsMessage(8));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await boProductsCreateTabCombinationsPage.generateCombinations(page);
      expect(successMessage).to.equal(boProductsCreateTabCombinationsPage.successfulGenerateCombinationsMessage(8));
    });
  });

  describe('Change combination', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      page = await boAttributesValueCreatePage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it(`should search the product '${newProductData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await foClassicHomePage.searchProduct(page, newProductData.name);

      const pageTitle = await foClassicSearchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicSearchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await foClassicSearchResultsPage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should select the size \'M\' and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectSize', baseContext);

      const combination: ProductAttribute[] = [
        {
          name: 'size',
          value: 'M',
        },
      ];

      await foClassicProductPage.selectAttributes(page, 'select', combination);

      const selectedAttribute = await foClassicProductPage.getSelectedAttribute(page, 1, 'select');
      expect(selectedAttribute).to.equal('S');
    });

    it('should select the \'Emballage Carton\' and check it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectEmballage', baseContext);

      const combination: ProductAttribute[] = [
        {
          name: 'Emballage',
          value: 'Carton',
        },
      ];

      await foClassicProductPage.selectAttributes(page, 'radio', combination, 2);

      const selectedAttribute = await foClassicProductPage.getSelectedAttribute(page, 2, 'radio');
      expect(selectedAttribute).to.equal('Carton');
    });
  });

  describe('POST-TEST: Delete the created attribute', async () => {
    it('should close the FO tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFO', baseContext);

      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to attributes page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAttributesPageToDelete', baseContext);

      await boProductsCreatePage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );

      const pageTitle = await boAttributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAttributesPage.pageTitle);
    });

    it('should filter attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributesToDelete', baseContext);

      await boAttributesPage.resetFilter(page);
      await boAttributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await boAttributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);
    });

    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await boAttributesPage.deleteAttribute(page, 1);
      expect(textResult).to.contains(boAttributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      numberOfAttributes = await boAttributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(1);
    });
  });

  // Post-condition: Delete Product
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
