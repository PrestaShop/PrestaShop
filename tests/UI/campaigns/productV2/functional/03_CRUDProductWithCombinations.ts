// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';
import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';
import {enableNewProductPageTest, disableNewProductPageTest} from '@commonTests/BO/advancedParameters/newFeatures';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/productsV2';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import combinationsTab from '@pages/BO/catalog/productsV2/add/combinationsTab';
import attributesPage from '@pages/BO/catalog/attributes';

// Import FO pages
import foProductPage from '@pages/FO/product';

// Import faker data
import ProductFaker from '@data/faker/product';

const baseContext = 'productV2_functional_CRUDProductWithCombinations';

describe('BO - Catalog - Products : CRUD product with combinations', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create product with combinations
  const newProductData = new ProductFaker({
    type: 'combinations',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    attributes: {
      size: ['S', 'M', 'L', 'XL'],
      color: ['Grey', 'Taupe', 'Beige', 'White', 'Red', 'Black', 'Orange', 'Green', 'Yellow', 'Brown'],

    },
    status: true,
  });

  // Data to edit product with combinations
  const editProductData = new ProductFaker({
    type: 'combinations',
    taxRule: 'No tax',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
    attributes: {
      color: ['Grey', 'Taupe', 'Red'],
      size: ['L', 'XL'],
    },
  });

  // Pre-condition: Enable new product page
  //enableNewProductPageTest(`${baseContext}_enableNewProduct`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    await files.generateImage('cover.jpg');
    await files.generateImage('thumb.jpg');
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('cover.jpg');
    await files.deleteFile('thumb.jpg');
  });

  describe('Create product', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.productsLink,
      );

      await productsPage.closeSfToolBar(page);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should select the product with combination and check the description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const productTypeDescription = await productsPage.getProductDescription(page);
      await expect(productTypeDescription).to.contains(productsPage.productWithCombinationsDescription);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName = await createProductsPage.getSaveButtonName(page);
      await expect(saveButtonName).to.equal('Save and publish');
    });

    it('should go to \'Combinations\' tab and click on \'Attributes & Features\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkAttributes&FeaturesLink', baseContext);

      page = await combinationsTab.clickOnAttributesAndFeaturesLink(page);

      const pageTitle = await attributesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(attributesPage.pageTitle);
    });

    it('should close \'Attributes & Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closePage', baseContext);

      page = await attributesPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton = await combinationsTab.setProductAttributes(
        page,
        newProductData.attributes,
      );
      await expect(generateCombinationsButton).to.equal('Generate 40 combinations');
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await combinationsTab.generateCombinations(page);
      await expect(successMessage).to.equal('Successfully generated 40 combinations.');
    });

    it('should close generate combinations modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeGenerateCombinationsModal', baseContext);

      const isModalVisible = await combinationsTab.closeGenerateCombinationModal(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should edit the first combination', async function(){
      await testContext.addContextItem(this, 'testIdentifier', 'editFirstCombination', baseContext);


    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      let result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(newProductData.name),
        await expect(result.price).to.equal(newProductData.price),
        await expect(result.shortDescription).to.equal(newProductData.summary),
        await expect(result.description).to.equal(newProductData.description),
      ]);

      result = await foProductPage.getProductAttributes(page);
      await Promise.all([
        await expect(result.size).to.equal(newProductData.attributes.size.join(' ')),
        await expect(result.color).to.equal(newProductData.attributes.color.join(' ')),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  /*describe('Edit product', async () => {
    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage = await createProductsPage.setProduct(page, editProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCombinations', baseContext);

      const generateCombinationsButton = await combinationsTab.setProductAttributes(
        page,
        editProductData.attributes,
      );
      await expect(generateCombinationsButton).to.equal('Generate 6 combinations');
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations2', baseContext);

      const successMessage = await combinationsTab.generateCombinations(page);
      await expect(successMessage).to.equal('Successfully generated 6 combinations.');
    });

    it('should close generate combinations modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeGenerateCombinationsModal2', baseContext);

      const isModalVisible = await combinationsTab.closeGenerateCombinationModal(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct2', baseContext);

      const updateProductMessage = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEditedProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      let result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(editProductData.name),
        await expect(result.price).to.equal(editProductData.price),
        await expect(result.description).to.equal(editProductData.description),
      ]);

      result = await foProductPage.getProductAttributes(page);
      await Promise.all([
        await expect(result.size).to.equal(
          `${newProductData.attributes.size.join(' ')} ${editProductData.attributes.size.join(' ')}`),
        await expect(result.color).to.equal(newProductData.attributes.color.join(' ')),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage = await createProductsPage.deleteProduct(page);
      await expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // Post-condition: Disable new product page
  disableNewProductPageTest(`${baseContext}_disableNewProduct`);*/
});
