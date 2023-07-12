// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import productsPage from '@pages/BO/catalog/productsV2';
import createProductsPage from '@pages/BO/catalog/productsV2/add';
import combinationsTab from '@pages/BO/catalog/productsV2/add/combinationsTab';
// Import FO pages
import foProductPage from '@pages/FO/product';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'productV2_sanity_CRUDProductWithCombinations';

describe('BO - Catalog - Products : CRUD product with combinations', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create product with combinations
  const newProductData: ProductData = new ProductData({
    type: 'combinations',
    taxRule: 'No tax',
    quantity: 50,
    minimumQuantity: 1,
    status: true,
  });
  // Data to edit product with combinations
  const editProductData: ProductData = new ProductData({
    type: 'combinations',
    taxRule: 'No tax',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
    attributes: [
      {
        name: 'color',
        values: ['Grey', 'Taupe', 'Red'],
      },
      {
        name: 'size',
        values: ['L', 'XL'],
      },
    ],
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
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

      const pageTitle: string = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible: boolean = await productsPage.clickOnNewProductButton(page);
      await expect(isModalVisible).to.be.true;
    });

    it('should choose \'Product with combinations\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseProductWithCombinations', baseContext);

      await productsPage.selectProductType(page, newProductData.type);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage: string = await createProductsPage.setProduct(page, newProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName: string = await createProductsPage.getSaveButtonName(page);
      await expect(saveButtonName).to.equal('Save and publish');
    });

    it('should create combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCombinations', baseContext);

      const generateCombinationsButton: string = await combinationsTab.setProductAttributes(
        page,
        newProductData.attributes,
      );
      await expect(generateCombinationsButton).to.equal('Generate 4 combinations');
    });

    it('should click on generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinations', baseContext);

      const successMessage = await combinationsTab.generateCombinations(page);
      await expect(successMessage).to.equal('Successfully generated 4 combinations.');
    });

    it('should check that combinations generation modal is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed', baseContext);

      const isModalClosed = await combinationsTab.generateCombinationModalIsClosed(page);
      await expect(isModalClosed).to.be.true;
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct', baseContext);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductInformation', baseContext);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(newProductData.name),
        await expect(result.price).to.equal(newProductData.price),
        await expect(result.summary).to.equal(newProductData.summary),
        await expect(result.description).to.equal(newProductData.description),
      ]);

      const productAttributes = await foProductPage.getProductAttributes(page);
      await Promise.all([
        // color
        await expect(productAttributes[0].value).to.equal(newProductData.attributes[1].values.join(' ')),
        // size
        await expect(productAttributes[1].value).to.equal(newProductData.attributes[0].values.join(' ')),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  describe('Edit product', async () => {
    it('should edit the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'editProduct', baseContext);

      const createProductMessage: string = await createProductsPage.setProduct(page, editProductData);
      await expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should add combinations and check generate combinations button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCombinations', baseContext);

      const generateCombinationsButton: string = await combinationsTab.setProductAttributes(
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

    it('should check that combinations generation modal is closed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'generateCombinationsModalIsClosed2', baseContext);

      const isModalClosed = await combinationsTab.generateCombinationModalIsClosed(page);
      await expect(isModalClosed).to.be.true;
    });

    it('should save the product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'saveProduct2', baseContext);

      const updateProductMessage: string = await createProductsPage.saveProduct(page);
      await expect(updateProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEditedProduct', baseContext);

      // Click on preview button
      page = await createProductsPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle: string = await foProductPage.getPageTitle(page);
      await expect(pageTitle).to.contains(editProductData.name);
    });

    it('should check all product information', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkEditedProductInformation', baseContext);

      const result = await foProductPage.getProductInformation(page);
      await Promise.all([
        await expect(result.name).to.equal(editProductData.name),
        await expect(result.price).to.equal(editProductData.price),
        await expect(result.description).to.equal(editProductData.description),
      ]);

      const productAttributes = await foProductPage.getProductAttributes(page);
      await Promise.all([
        await expect(productAttributes[0].value).to.equal(
          `${newProductData.attributes[1].values.join(' ')} ${editProductData.attributes[1].values.join(' ')}`),
        await expect(productAttributes[1].value).to.equal(newProductData.attributes[0].values.join(' ')),
      ]);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });
  });

  describe('Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const createProductMessage: string = await createProductsPage.deleteProduct(page);
      await expect(createProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });
});
