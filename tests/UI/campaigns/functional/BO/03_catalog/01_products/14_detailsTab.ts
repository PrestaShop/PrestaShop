// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';
import files from '@utils/files';

// Import common tests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import featuresPage from '@pages/BO/catalog/features';
import createProductPage from '@pages/BO/catalog/products/add';
import detailsTab from '@pages/BO/catalog/products/add/detailsTab';
import productsPage from '@pages/BO/catalog/products';
import filesPage from '@pages/BO/catalog/files';

// Import FO pages
import foProductPage from '@pages/FO/product';

// Import data
import ProductData from '@data/faker/product';
import {ProductFeatures} from '@data/types/product';

import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

const baseContext: string = 'functional_BO_catalog_products_detailsTab';

describe('BO - Catalog - Products : Details tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: ProductData = new ProductData({
    type: 'standard',
    quantity: 100,
    minimumQuantity: 1,
    status: true,
    mpn: 'lorem ipsum',
    upc: 'lorem ipsum',
    ean13: 'lorem ipsum',
    isbn: 'lorem ipsum',
  });
  // Data to edit standard product
  const editProductData: ProductData = new ProductData({
    mpn: 'HSC0424PP',
    upc: '987654321098',
    ean13: '9782409038600',
    isbn: '978-2-409-03860-0',
    features: [
      {
        featureName: 'Composition',
        preDefinedValue: 'Cotton',
      }, {
        featureName: 'Composition',
        customizedValueEn: 'Lorem Ipsum',
      },
    ],
    files: [
      {
        fileName: 'Hello world',
        description: 'hello world',
        file: 'test.png',
      },
    ],
    displayCondition: true,
    condition: 'Used',
    customizations: [
      {
        label: 'Lorem ipsum',
        type: 'Text',
        required: false,
      },
      {
        label: 'Lorem ipsum',
        type: 'Text',
        required: true,
      },
      {
        label: 'Lorem ipsum',
        type: 'File',
        required: false,
      },
      {
        label: 'Lorem ipsum',
        type: 'File',
        required: true,
      },
    ],
  });
  // Product Feature only in French
  const productFeaturesFr: ProductFeatures[] = [{
    featureName: 'Composition',
    customizedValueFr: 'Only in French',
  }];

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await files.generateImage(editProductData.files[0].file);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await files.deleteFile(editProductData.files[0].file);
  });

  // 1 - Create product
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
      expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    it('should click on \'New product\' button and check new product modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton', baseContext);

      const isModalVisible = await productsPage.clickOnNewProductButton(page);
      expect(isModalVisible).to.eq(true);
    });

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, newProductData.type);
      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await createProductPage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });
  });

  // 2 - Check all options in Details tab
  describe('Check all options in Details tab', async () => {
    it('should go to details tab and set References form with a wrong data and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setWrongData', baseContext);

      await detailsTab.setProductDetails(page, newProductData);
      await detailsTab.setMPN(page, newProductData.mpn!);
      await detailsTab.setUPC(page, newProductData.upc!);

      let errorMessage = await detailsTab.getErrorMessageInReferencesForm(page, 3);
      expect(errorMessage).to.eq(`"${newProductData.upc}" is invalid`);

      await detailsTab.setEAN13(page, newProductData.ean13!);

      errorMessage = await detailsTab.getErrorMessageInReferencesForm(page, 4);
      expect(errorMessage).to.eq(`"${newProductData.ean13}" is invalid`);

      await detailsTab.setISBN(page, newProductData.isbn!);

      errorMessage = await detailsTab.getErrorMessageInReferencesForm(page, 5);
      expect(errorMessage).to.eq(`"${newProductData.isbn}" is invalid`);
    });

    it('should set References form with a good data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setGoodDara', baseContext);

      await detailsTab.setMPN(page, editProductData.mpn!);
      await detailsTab.setUPC(page, editProductData.upc!);
      await detailsTab.setEAN13(page, editProductData.ean13!);
      await detailsTab.setISBN(page, editProductData.isbn!);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should add 2 features', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstFeature', baseContext);

      await detailsTab.setFeature(page, editProductData.features);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product features list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getProductFeaturesList', baseContext);

      const productFeatures = await foProductPage.getProductFeaturesList(page);
      expect(productFeatures).to.eq(
        `Data sheet ${editProductData.features[0].featureName} ${editProductData.features[0].preDefinedValue}`
        + ` ${editProductData.features[1].customizedValueEn}`);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should check the Features link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFeatureLink', baseContext);

      await createProductPage.goToTab(page, 'details');
      page = await detailsTab.clickonManageFeatures(page);

      const pageTitle = await featuresPage.getPageTitle(page);
      expect(pageTitle).to.contains(featuresPage.pageTitle);
    });

    it('should close the Features pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFeaturesTab', baseContext);

      page = await filesPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should add a custom feature value only on French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCustomFeatureValueInFr', baseContext);

      await detailsTab.setFeature(page, productFeaturesFr);
      await createProductPage.clickOnSaveProductButton(page);

      const message = await detailsTab.getAlertDangerBlockParagraphContent(page);
      expect(message).to.eq(detailsTab.featureCustomValueNotDefaultLanguageMessage);
    });

    it('should delete the created features', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFeatures', baseContext);

      await detailsTab.deleteFeatures(page, editProductData.features.concat(productFeaturesFr));

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that product features list is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isFeatureBlockNotVisible', baseContext);

      const isVisible = await foProductPage.isFeaturesBlockVisible(page);
      expect(isVisible).to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should click on \'Manage all files\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageAllFiles', baseContext);

      page = await detailsTab.clickOnManageAllFiles(page);

      const pageTitle = await filesPage.getPageTitle(page);
      expect(pageTitle).to.contains(filesPage.pageTitle);
    });

    it('should close Files page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFilesPage', baseContext);

      page = await filesPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should search for a not existing file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNotExistingFile', baseContext);

      const searchResult = await detailsTab.searchFile(page, 'hello world');
      expect(searchResult).to.eq('No results found for "hello world"');
    });

    it('should add new file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewFile', baseContext);

      await detailsTab.addNewFile(page, editProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should delete the file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);

      await detailsTab.deleteFiles(page, editProductData);

      const alertMessage = await detailsTab.getNoFileAttachedMessage(page);
      expect(alertMessage).to.eq('No files attached');
    });

    it('should set the condition in product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setCondition', baseContext);

      await detailsTab.setCondition(page, editProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product condition', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductCondition', baseContext);

      const productCondition = await foProductPage.getProductCondition(page);
      expect(productCondition).to.eq(`Condition ${editProductData.condition}`);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create 4 customizations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomizations', baseContext);

      await detailsTab.addNewCustomizations(page, editProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await createProductPage.previewProduct(page);

      await foProductPage.changeLanguage(page, 'en');

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the customization section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductCustomizations', baseContext);

      const productCondition = await foProductPage.isCustomizationBlockVisible(page);
      expect(productCondition).to.eq(true);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      // Go back to BO
      page = await foProductPage.closePage(browserContext, page, 0);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should delete the 4 customizations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomizations', baseContext);

      await detailsTab.deleteCustomizations(page, editProductData);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await createProductPage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(productsPage.successfulDeleteMessage);
    });
  });

  // 4 - Delete file
  describe('POST-TEST: Delete file', async () => {
    it('should go to \'Catalog > Files\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.filesLink,
      );

      const pageTitle = await filesPage.getPageTitle(page);
      expect(pageTitle).to.contains(filesPage.pageTitle);
    });

    it('should delete files with Bulk Actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await filesPage.deleteFilesBulkActions(page);
      expect(deleteTextResult).to.be.equal(filesPage.successfulMultiDeleteMessage);
    });
  });
});
