import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boFeaturesPage,
  boFilesPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDetailsPage,
  type BrowserContext,
  FakerProduct,
  foClassicProductPage,
  type Page,
  type ProductFeatures,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_products_detailsTab';

describe('BO - Catalog - Products : Details tab', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Data to create standard product
  const newProductData: FakerProduct = new FakerProduct({
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
  const editProductData: FakerProduct = new FakerProduct({
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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.generateImage(editProductData.files[0].file);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(editProductData.files[0].file);
  });

  // 1 - Create product
  describe('Create product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

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

    it('should choose \'Standard product\' and go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, newProductData.type);
      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      const createProductMessage = await boProductsCreatePage.setProduct(page, newProductData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 2 - Check all options in Details tab
  describe('Check all options in Details tab', async () => {
    it('should go to details tab and set References form with a wrong data and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setWrongData', baseContext);

      await boProductsCreateTabDetailsPage.setProductDetails(page, newProductData);
      await boProductsCreateTabDetailsPage.setMPN(page, newProductData.mpn!);
      await boProductsCreateTabDetailsPage.setUPC(page, newProductData.upc!);

      let errorMessage = await boProductsCreateTabDetailsPage.getErrorMessageInReferencesForm(page, 3);
      expect(errorMessage).to.eq(`"${newProductData.upc}" is invalid`);

      await boProductsCreateTabDetailsPage.setEAN13(page, newProductData.ean13!);

      errorMessage = await boProductsCreateTabDetailsPage.getErrorMessageInReferencesForm(page, 4);
      expect(errorMessage).to.eq(`"${newProductData.ean13}" is invalid`);

      await boProductsCreateTabDetailsPage.setISBN(page, newProductData.isbn!);

      errorMessage = await boProductsCreateTabDetailsPage.getErrorMessageInReferencesForm(page, 5);
      expect(errorMessage).to.eq(`"${newProductData.isbn}" is invalid`);
    });

    it('should set References form with a good data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setGoodDara', baseContext);

      await boProductsCreateTabDetailsPage.setMPN(page, editProductData.mpn!);
      await boProductsCreateTabDetailsPage.setUPC(page, editProductData.upc!);
      await boProductsCreateTabDetailsPage.setEAN13(page, editProductData.ean13!);
      await boProductsCreateTabDetailsPage.setISBN(page, editProductData.isbn!);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should add 2 features', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFirstFeature', baseContext);

      await boProductsCreateTabDetailsPage.deleteFeatures(page, newProductData.features.length);
      await boProductsCreateTabDetailsPage.setFeature(page, editProductData.features);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product features list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getProductFeaturesList', baseContext);

      const productFeatures = await foClassicProductPage.getProductFeaturesList(page);
      expect(productFeatures).to.eq(
        `Data sheet ${editProductData.features[0].featureName} ${editProductData.features[0].preDefinedValue}`
        + ` ${editProductData.features[1].customizedValueEn}`);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the Features link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFeatureLink', baseContext);

      await boProductsCreatePage.goToTab(page, 'details');
      page = await boProductsCreateTabDetailsPage.clickonManageFeatures(page);

      const pageTitle = await boFeaturesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
    });

    it('should close the Features pages', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFeaturesTab', baseContext);

      page = await boFilesPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should add a custom feature value only on French', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCustomFeatureValueInFr', baseContext);

      await boProductsCreateTabDetailsPage.setFeature(page, productFeaturesFr);
      await boProductsCreatePage.clickOnSaveProductButton(page);

      const message = await boProductsCreateTabDetailsPage.getAlertDangerBlockParagraphContent(page);
      expect(message).to.eq(boProductsCreateTabDetailsPage.featureCustomValueNotDefaultLanguageMessage);
    });

    it('should delete the created features', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFeatures', baseContext);

      await boProductsCreateTabDetailsPage.deleteFeatures(page, editProductData.features.concat(productFeaturesFr).length);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct2', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check that product features list is empty', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'isFeatureBlockNotVisible', baseContext);

      const isVisible = await foClassicProductPage.isFeaturesBlockVisible(page);
      expect(isVisible).to.eq(false);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO2', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should click on \'Manage all files\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnManageAllFiles', baseContext);

      page = await boProductsCreateTabDetailsPage.clickOnManageAllFiles(page);

      const pageTitle = await boFilesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFilesPage.pageTitle);
    });

    it('should close Files page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeFilesPage', baseContext);

      page = await boFilesPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should search for a not existing file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchNotExistingFile', baseContext);

      const searchResult = await boProductsCreateTabDetailsPage.searchFile(page, 'hello world');
      expect(searchResult).to.eq('No results found for "hello world"');
    });

    it('should add new file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addNewFile', baseContext);

      await boProductsCreateTabDetailsPage.addNewFile(page, editProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should delete the file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);

      await boProductsCreateTabDetailsPage.deleteFiles(page, editProductData);

      const alertMessage = await boProductsCreateTabDetailsPage.getNoFileAttachedMessage(page);
      expect(alertMessage).to.eq('No files attached');
    });

    it('should set the condition in product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setCondition', baseContext);

      await boProductsCreateTabDetailsPage.setCondition(page, editProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct3', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the product condition', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductCondition', baseContext);

      const productCondition = await foClassicProductPage.getProductCondition(page);
      expect(productCondition).to.eq(`Condition ${editProductData.condition}`);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO3', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create 4 customizations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCustomizations', baseContext);

      await boProductsCreateTabDetailsPage.addNewCustomizations(page, editProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should preview product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewProduct4', baseContext);

      // Click on preview button
      page = await boProductsCreatePage.previewProduct(page);

      await foClassicProductPage.changeLanguage(page, 'en');

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(newProductData.name);
    });

    it('should check the customization section', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductCustomizations', baseContext);

      const productCondition = await foClassicProductPage.isCustomizationBlockVisible(page);
      expect(productCondition).to.eq(true);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO4', baseContext);

      // Go back to BO
      page = await foClassicProductPage.closePage(browserContext, page, 0);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should delete the 4 customizations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCustomizations', baseContext);

      await boProductsCreateTabDetailsPage.deleteCustomizations(page, editProductData);

      const message = await boProductsCreatePage.saveProduct(page);
      expect(message).to.eq(boProductsCreatePage.successfulUpdateMessage);
    });
  });

  // 3 - Delete product
  describe('POST-TEST: Delete product', async () => {
    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);

      const deleteProductMessage = await boProductsCreatePage.deleteProduct(page);
      expect(deleteProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });

  // 4 - Delete file
  describe('POST-TEST: Delete file', async () => {
    it('should go to \'Catalog > Files\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.filesLink,
      );

      const pageTitle = await boFilesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFilesPage.pageTitle);
    });

    it('should delete files with Bulk Actions', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'BulkDelete', baseContext);

      const deleteTextResult = await boFilesPage.deleteFilesBulkActions(page);
      expect(deleteTextResult).to.be.equal(boFilesPage.successfulMultiDeleteMessage);
    });
  });
});
