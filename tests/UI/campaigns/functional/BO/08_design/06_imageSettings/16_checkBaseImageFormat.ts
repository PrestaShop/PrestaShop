// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_checkBaseImageFormat';

describe('BO - Design - Image Settings - Check base image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number = 0;
  let idProductImage: number = 0;

  const productDataPNGBaseFormatJPEG: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'coverPNGBaseFormatJPEG.png',
    status: true,
  });
  const productDataJPEGBaseFormatJPEG: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'coverJPEGBaseFormatJPEG.jpg',
    status: true,
  });
  const productDataPNGBaseFormatPNG: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'coverPNGBaseFormatPNG.png',
    status: true,
  });

  // Pre-condition: Enable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, true, `${baseContext}_enableMultipleImageFormats`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      productDataPNGBaseFormatJPEG.coverImage,
      productDataJPEGBaseFormatJPEG.coverImage,
      productDataPNGBaseFormatPNG.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await files.generateImage(image);
      }
    }));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      productDataPNGBaseFormatJPEG.coverImage,
      productDataJPEGBaseFormatJPEG.coverImage,
      productDataPNGBaseFormatPNG.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await files.deleteFile(image);
      }
    }));
  });

  describe('Check base image format', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Design > Image Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.designParentLink,
        dashboardPage.imageSettingsLink,
      );
      await imageSettingsPage.closeSfToolBar(page);

      const pageTitle = await imageSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
    });

    it('should check Image Generation Options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // Image Format : JPEG/PNG should be checked
      const jpegChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // Image Format : JPEG/PNG should be disabled
      const jpegDisabled = await imageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // Base Format : PNG should be checked
      const pngChecked = await imageSettingsPage.isBaseFormatToGenerateChecked(page, 'png');
      expect(pngChecked).to.eq(true);
    });

    [
      {
        baseFormat: 'jpg',
        product: productDataPNGBaseFormatJPEG,
        extOriginal: 'png',
        extGenerated: 'jpg',
      },
      {
        baseFormat: 'jpg',
        product: productDataJPEGBaseFormatJPEG,
        extOriginal: 'jpg',
        extGenerated: 'jpg',
      },
      {
        baseFormat: 'png',
        product: productDataPNGBaseFormatPNG,
        extOriginal: 'png',
        extGenerated: 'png',
      },
    ].forEach((arg: {baseFormat: string, product: ProductData, extOriginal: string, extGenerated: string}, index: number) => {
      describe(`Base Format : ${arg.baseFormat.toUpperCase()} - Image Extension : ${arg.extOriginal.toUpperCase()}`, async () => {
        if (index !== 0) {
          it('should go to \'Design > Image Settings\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToImageSettingsPage${index}`, baseContext);

            await dashboardPage.goToSubMenu(
              page,
              dashboardPage.designParentLink,
              dashboardPage.imageSettingsLink,
            );
            await imageSettingsPage.closeSfToolBar(page);

            const pageTitle = await imageSettingsPage.getPageTitle(page);
            expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
          });
        }

        it(`should enable ${arg.baseFormat.toUpperCase()} as Base Format in Image Generation Options`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `setBaseFormatImageGenerationOptions${index}`, baseContext);

          const textResult = await imageSettingsPage.setBaseFormatChecked(page, arg.baseFormat, true);
          expect(textResult).to.be.eq(imageSettingsPage.messageSettingsUpdated);

          const baseFormatChecked = await imageSettingsPage.isBaseFormatToGenerateChecked(page, arg.baseFormat);
          expect(baseFormatChecked).to.eq(true);
        });

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

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
          await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

          const isModalVisible = await productsPage.clickOnNewProductButton(page);
          expect(isModalVisible).to.eq(true);
        });

        it('should check the standard product description', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkStandardProductDescription${index}`,
            baseContext,
          );

          const productTypeDescription = await productsPage.getProductDescription(page);
          expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
        });

        it('should choose \'Standard product\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `chooseStandardProduct${index}`, baseContext);

          await productsPage.selectProductType(page, arg.product.type);

          const pageTitle = await createProductsPage.getPageTitle(page);
          expect(pageTitle).to.contains(createProductsPage.pageTitle);
        });

        it('should go to new product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${index}`, baseContext);

          await productsPage.clickOnAddNewProduct(page);

          const pageTitle = await createProductsPage.getPageTitle(page);
          expect(pageTitle).to.contains(createProductsPage.pageTitle);
        });

        it('should create standard product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createStandardProduct${index}`, baseContext);

          await createProductsPage.closeSfToolBar(page);

          const createProductMessage = await createProductsPage.setProduct(page, arg.product);
          expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
        });

        it('should check that the save button is changed to \'Save and publish\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkSaveButton${index}`, baseContext);

          const saveButtonName = await createProductsPage.getSaveButtonName(page);
          expect(saveButtonName).to.equal('Save and publish');

          idProduct = await createProductsPage.getProductID(page);
          idProductImage = await descriptionTab.getProductIDImageCover(page);
          expect(idProduct).to.be.gt(0);
        });

        it(`should check that image is generated in format ${arg.extGenerated.toUpperCase()}`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProductImages${index}`, baseContext);

          const pathProductIdSplitted: RegExpMatchArray|null = idProductImage.toString().match(/./g);
          expect(pathProductIdSplitted).to.not.eq(null);

          if (!pathProductIdSplitted) {
            return;
          }

          const pathProductId: string = pathProductIdSplitted.join('/');

          // Check the original file
          const pathImageJPG: string = `${files.getRootPath()}/img/p/${pathProductId}/${idProductImage}-large_default.jpg`;

          const fileExistsJPG = await files.doesFileExist(pathImageJPG);
          expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

          const imageTypeJPG = await files.getFileType(pathImageJPG);
          expect(imageTypeJPG).to.be.eq(arg.extGenerated);
        });
      });
    });
  });

  // Post-condition: Remove products
  [
    productDataPNGBaseFormatJPEG,
    productDataJPEGBaseFormatJPEG,
    productDataPNGBaseFormatPNG,
  ].forEach((product: ProductData, index: number) => {
    deleteProductTest(product, `${baseContext}_removeProduct${index}`);
  });

  // Post-condition: Disable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, false, `${baseContext}_disableMultipleImageFormats`);
});
