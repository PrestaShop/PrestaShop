import testContext from '@utils/testContext';
import {expect} from 'chai';

import {deleteProductTest} from '@commonTests/BO/catalog/product';

import {
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_checkBaseImageFormat';

describe('BO - Design - Image Settings - Check base image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number = 0;
  let idProductImage: number = 0;

  const productDataPNGBaseFormatJPEG: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'coverPNGBaseFormatJPEG.png',
    status: true,
  });
  const productDataJPEGBaseFormatJPEG: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'coverJPEGBaseFormatJPEG.jpg',
    status: true,
  });
  const productDataPNGBaseFormatPNG: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'coverPNGBaseFormatPNG.png',
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      productDataPNGBaseFormatJPEG.coverImage,
      productDataJPEGBaseFormatJPEG.coverImage,
      productDataPNGBaseFormatPNG.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.generateImage(image);
      }
    }));
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      productDataPNGBaseFormatJPEG.coverImage,
      productDataJPEGBaseFormatJPEG.coverImage,
      productDataPNGBaseFormatPNG.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.deleteFile(image);
      }
    }));
  });

  describe('Check base image format', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Design > Image Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.imageSettingsLink,
      );
      await boImageSettingsPage.closeSfToolBar(page);

      const pageTitle = await boImageSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boImageSettingsPage.pageTitle);
    });

    it('should check Image Generation Options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // Image Format : JPEG/PNG should be checked
      const jpegChecked = await boImageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // Image Format : JPEG/PNG should be disabled
      const jpegDisabled = await boImageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // Base Format : PNG should be checked
      const pngChecked = await boImageSettingsPage.isBaseFormatToGenerateChecked(page, 'png');
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
    ].forEach((arg: {baseFormat: string, product: FakerProduct, extOriginal: string, extGenerated: string}, index: number) => {
      describe(`Base Format : ${arg.baseFormat.toUpperCase()} - Image Extension : ${arg.extOriginal.toUpperCase()}`, async () => {
        if (index !== 0) {
          it('should go to \'Design > Image Settings\' page', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToImageSettingsPage${index}`, baseContext);

            await boDashboardPage.goToSubMenu(
              page,
              boDashboardPage.designParentLink,
              boDashboardPage.imageSettingsLink,
            );
            await boImageSettingsPage.closeSfToolBar(page);

            const pageTitle = await boImageSettingsPage.getPageTitle(page);
            expect(pageTitle).to.contains(boImageSettingsPage.pageTitle);
          });
        }

        it(`should enable ${arg.baseFormat.toUpperCase()} as Base Format in Image Generation Options`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `setBaseFormatImageGenerationOptions${index}`, baseContext);

          const textResult = await boImageSettingsPage.setBaseFormatChecked(page, arg.baseFormat, true);
          expect(textResult).to.be.eq(boImageSettingsPage.messageSettingsUpdated);

          const baseFormatChecked = await boImageSettingsPage.isBaseFormatToGenerateChecked(page, arg.baseFormat);
          expect(baseFormatChecked).to.eq(true);
        });

        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${index}`, baseContext);

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
          await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${index}`, baseContext);

          const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
          expect(isModalVisible).to.eq(true);
        });

        it('should check the standard product description', async function () {
          await testContext.addContextItem(
            this,
            'testIdentifier',
            `checkStandardProductDescription${index}`,
            baseContext,
          );

          const productTypeDescription = await boProductsPage.getProductDescription(page);
          expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
        });

        it('should choose \'Standard product\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `chooseStandardProduct${index}`, baseContext);

          await boProductsPage.selectProductType(page, arg.product.type);

          const pageTitle = await boProductsCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
        });

        it('should go to new product page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${index}`, baseContext);

          await boProductsPage.clickOnAddNewProduct(page);

          const pageTitle = await boProductsCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
        });

        it('should create standard product', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createStandardProduct${index}`, baseContext);

          await boProductsCreatePage.closeSfToolBar(page);

          const createProductMessage = await boProductsCreatePage.setProduct(page, arg.product);
          expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
        });

        it('should check that the save button is changed to \'Save and publish\'', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkSaveButton${index}`, baseContext);

          const saveButtonName = await boProductsCreatePage.getSaveButtonName(page);
          expect(saveButtonName).to.equal('Save and publish');

          idProduct = await boProductsCreatePage.getProductID(page);
          idProductImage = await boProductsCreateTabDescriptionPage.getProductIDImageCover(page);
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
          const pathImageJPG: string = `${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProductImage}-large_default.jpg`;

          const fileExistsJPG = await utilsFile.doesFileExist(pathImageJPG);
          expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

          const imageTypeJPG = await utilsFile.getFileType(pathImageJPG);
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
  ].forEach((product: FakerProduct, index: number) => {
    deleteProductTest(product, `${baseContext}_removeProduct${index}`);
  });
});
