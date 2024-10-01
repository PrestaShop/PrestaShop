// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import createProductPage from '@pages/BO/catalog/products/add';
import filesPage from '@pages/BO/catalog/files';
import addFilePage from '@pages/BO/catalog/files/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boAdministrationPage,
  boDashboardPage,
  boProductsPage,
  boProductsCreateTabDescriptionPage,
  boProductsCreateTabVirtualProductPage,
  FakerFile,
  FakerProduct,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_administration_uploadQuota';

describe('BO - Advanced Parameters - Administration : Upload quota', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  // Image data with size > 2MB
  const firstFileData: FakerFile = new FakerFile({filename: 'image1.jpg'});
  // Image data with size < 2MB
  const secondFileData: FakerFile = new FakerFile({filename: 'image2.jpg'});
  // Image data with size < 1MB
  const thirdFileData: FakerFile = new FakerFile({filename: 'image3.jpg'});
  const firstVirtualProductData: FakerProduct = new FakerProduct({
    type: 'virtual',
    downloadFile: true,
    fileName: firstFileData.filename,
    allowedDownload: 1,
    status: true,
  });
  const secondVirtualProductData: FakerProduct = new FakerProduct({
    type: 'virtual',
    downloadFile: true,
    fileName: secondFileData.filename,
    allowedDownload: 1,
    status: true,
  });
  const firstStandardProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: firstFileData.filename,
    status: true,
  });
  const secondStandardProductData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: thirdFileData.filename,
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create image with size > 2MB
    await utilsFile.generateImage(firstFileData.filename, 1000, 1500, 92);
    // Create image with size < 2MB
    await utilsFile.generateImage(secondFileData.filename, 1000, 1500, 70);
    // Create image with size < 1MB
    await utilsFile.generateImage(thirdFileData.filename, 100, 200);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(firstFileData.filename);
    await utilsFile.deleteFile(secondFileData.filename);
    await utilsFile.deleteFile(thirdFileData.filename);
  });

  describe('Check \'Maximum size for attached files\'', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.administrationLink,
      );

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should set the \'Maximum size for attached files\' to 2MB', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMaximumSizeForAttachedFiles', baseContext);

      const successMessage = await boAdministrationPage.setMaxSizeAttachedFiles(page, 2);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Files\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.filesLink,
      );
      await filesPage.closeSfToolBar(page);

      const pageTitle = await filesPage.getPageTitle(page);
      expect(pageTitle).to.contains(filesPage.pageTitle);
    });

    it('should go to new file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFilePage', baseContext);

      await filesPage.goToAddNewFilePage(page);

      const pageTitle = await addFilePage.getPageTitle(page);
      expect(pageTitle).to.contains(addFilePage.pageTitle);
    });

    it('should try to upload a file > 2MB and check the error message alert', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFileAndCheckError', baseContext);

      await addFilePage.createEditFile(page, firstFileData, false);

      const errorAlert = await addFilePage.getTextDanger(page);
      expect(errorAlert).to.equal('Upload error. Please check your server configurations for the maximum upload size allowed.');
    });

    it('should upload a file < 2MB and check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFileAndCheckSuccess', baseContext);

      const result = await addFilePage.createEditFile(page, secondFileData);
      expect(result).to.equal(filesPage.successfulCreationMessage);
    });

    it('should delete the created file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);

      const result = await filesPage.deleteFile(page, 1);
      expect(result).to.be.equal(filesPage.successfulDeleteMessage);
    });
  });

  describe('Check \'Maximum size for a downloadable product\'', async () => {
    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.administrationLink,
      );

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should set the \'Maximum size for a downloadable product\' to 2MB', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMaximumSizeForAttachedFiles2', baseContext);

      const successMessage = await boAdministrationPage.setMaxSizeDownloadedProduct(page, 2);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
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
      expect(isModalVisible).eq(true);
    });

    it('should choose \'Virtual product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseVirtualProduct', baseContext);

      await boProductsPage.selectProductType(page, firstVirtualProductData.type);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFoProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should try to add a file in virtual tab > 2 MB and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct1', baseContext);

      await createProductPage.setProductName(page, firstVirtualProductData.name, 'en');
      await boProductsCreateTabVirtualProductPage.setVirtualProduct(page, firstVirtualProductData);
      await createProductPage.clickOnSaveProductButton(page);

      const errorMessage = await boProductsCreateTabVirtualProductPage.getErrorMessageInDownloadFileInput(page);
      expect(errorMessage).to.contains('The file is too large')
        .and.to.contains('Allowed maximum size is 2 MB.');
    });

    it('should try to add a file in virtual tab < 2 MB', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createVirtualProduct2', baseContext);

      await boProductsCreateTabVirtualProductPage.setVirtualProduct(page, secondVirtualProductData);

      const createProductMessage = await createProductPage.saveProduct(page);
      expect(createProductMessage).to.equal(createProductPage.successfulUpdateMessage);
    });

    it('should delete the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCreatedProduct', baseContext);

      const createProductMessage = await createProductPage.deleteProduct(page);
      expect(createProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });

  describe('Check \'Maximum size for a product\'s image\'', async () => {
    it('should go to \'Advanced Parameters > Administration\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdministrationPage3', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.administrationLink,
      );

      const pageTitle = await boAdministrationPage.getPageTitle(page);
      expect(pageTitle).to.contains(boAdministrationPage.pageTitle);
    });

    it('should set the \'Maximum size for a product\' to 1MB', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMaximumSizeForAttachedFiles3', baseContext);

      const successMessage = await boAdministrationPage.setMaxSizeForProductImage(page, 1);
      expect(successMessage).to.eq(boAdministrationPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage2', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNewProductButton2', baseContext);

      const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
      expect(isModalVisible).eq(true);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, firstStandardProductData.type);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFoProductPage2', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductPage.pageTitle);
    });

    it('should create standard product and add an image size > 1MB then check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImageAndCheckErrorMessage', baseContext);

      await createProductPage.setProductName(page, firstVirtualProductData.name, 'en');
      await boProductsCreateTabDescriptionPage.uploadProductImages(
        page,
        [firstStandardProductData.coverImage, firstStandardProductData.thumbImage],
      );

      const message = await createProductPage.getGrowlMessageContent(page);
      expect(message).to.eq('Max file size allowed is "1048576" bytes.');
    });

    it('should create standard product and add an image size < 1MB then check the validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImageAndCheckSuccessMessage', baseContext);

      await createProductPage.setProductName(page, firstVirtualProductData.name, 'en');
      await boProductsCreateTabDescriptionPage.uploadProductImages(page,
        [secondStandardProductData.coverImage, secondStandardProductData.thumbImage]);

      const message = await createProductPage.saveProduct(page);
      expect(message).to.eq(createProductPage.successfulUpdateMessage);
    });

    it('should delete the created product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile2', baseContext);

      const createProductMessage = await createProductPage.deleteProduct(page);
      expect(createProductMessage).to.equal(boProductsPage.successfulDeleteMessage);
    });
  });
});
