// Import utils
import testContext from '@utils/testContext';

// import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import BO pages
import attributesPage from '@pages/BO/catalog/attributes';
import addAttributePage from '@pages/BO/catalog/attributes/addAttribute';
import viewAttributePage from '@pages/BO/catalog/attributes/view';
import addValuePage from '@pages/BO/catalog/attributes/addValue';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import combinationsTab from '@pages/BO/catalog/products/add/combinationsTab';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {productPage} from '@pages/FO/classic/product';
import {searchResultsPage} from '@pages/FO/classic/searchResults';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  FakerAttribute,
  FakerAttributeValue,
  FakerProduct,
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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );
      await attributesPage.closeSfToolBar(page);

      const pageTitle = await attributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(attributesPage.pageTitle);
    });

    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(0);
    });

    it('should go to add new attribute page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewAttributePage', baseContext);

      await attributesPage.goToAddAttributePage(page);

      const pageTitle = await addAttributePage.getPageTitle(page);
      expect(pageTitle).to.equal(addAttributePage.createPageTitle);
    });

    it('should create new attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAttribute', baseContext);

      const textResult = await addAttributePage.addEditAttribute(page, createAttributeData);
      expect(textResult).to.contains(attributesPage.successfulCreationMessage);

      const numberOfAttributesAfterCreation = await attributesPage.getNumberOfElementInGrid(page);
      expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1);
    });

    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToViewCreatedAttribute', baseContext);

      await attributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);

      attributeId = parseInt(await attributesPage.getTextColumn(page, 1, 'id_attribute_group'), 10);
      expect(attributeId).to.be.gt(0);
    });

    it('should view attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedAttribute', baseContext);

      await attributesPage.viewAttribute(page, 1);

      const pageTitle = await viewAttributePage.getPageTitle(page);
      expect(pageTitle).to.equal(viewAttributePage.pageTitle(createAttributeData.name));
    });

    it('should go to add new value page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateValuePage', baseContext);

      await viewAttributePage.goToAddNewValuePage(page);

      const pageTitle = await addValuePage.getPageTitle(page);
      expect(pageTitle).to.equal(addValuePage.createPageTitle);
    });

    valuesToCreate.forEach((valueToCreate: FakerAttributeValue, index: number) => {
      it(`should create value n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createValue${index}`, baseContext);

        valueToCreate.setAttributeId(attributeId);
        const textResult = await addValuePage.addEditValue(page, valueToCreate, index === 0);
        expect(textResult).to.contains(viewAttributePage.successfulCreationMessage);
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

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should select the product with combination and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton = await combinationsTab.setProductAttributes(
        page,
        newProductData.attributes,
      );
      expect(generateCombinationsButton).to.equal(combinationsTab.generateCombinationsMessage(8));
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await combinationsTab.generateCombinations(page);
      expect(successMessage).to.equal(combinationsTab.successfulGenerateCombinationsMessage(8));
    });
  });

  describe('Change combination', async () => {
    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

      page = await addValuePage.viewMyShop(page);
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it(`should search the product '${newProductData.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchProduct', baseContext);

      await homePage.searchProduct(page, newProductData.name);

      const pageTitle = await searchResultsPage.getPageTitle(page);
      expect(pageTitle).to.equal(searchResultsPage.pageTitle);
    });

    it('should go to the product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductPage', baseContext);

      await searchResultsPage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
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

      await productPage.selectAttributes(page, 'select', combination);

      const selectedAttribute = await productPage.getSelectedAttribute(page, 1, 'select');
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

      await productPage.selectAttributes(page, 'radio', combination, 2);

      const selectedAttribute = await productPage.getSelectedAttribute(page, 2, 'radio');
      expect(selectedAttribute).to.equal('Carton');
    });
  });

  describe('POST-TEST: Delete the created attribute', async () => {
    it('should close the FO tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFO', baseContext);

      page = await productPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to attributes page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToAttributesPageToDelete', baseContext);

      await createProductsPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.attributesAndFeaturesLink,
      );

      const pageTitle = await attributesPage.getPageTitle(page);
      expect(pageTitle).to.contains(attributesPage.pageTitle);
    });

    it('should filter attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAttributesToDelete', baseContext);

      await attributesPage.resetFilter(page);
      await attributesPage.filterTable(page, 'name', createAttributeData.name);

      const textColumn = await attributesPage.getTextColumn(page, 1, 'name');
      expect(textColumn).to.contains(createAttributeData.name);
    });

    it('should delete attribute', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAttribute', baseContext);

      const textResult = await attributesPage.deleteAttribute(page, 1);
      expect(textResult).to.contains(attributesPage.successfulDeleteMessage);

      const numberOfAttributesAfterDelete = await attributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterDelete).to.equal(numberOfAttributes);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);

      numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributes).to.be.above(1);
    });
  });

  // Post-condition: Delete Product
  deleteProductTest(newProductData, `${baseContext}_postTest`);
});
