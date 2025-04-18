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
  foClassicCategoryPage,
  foClassicHomePage,
  foClassicModalQuickViewPage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_checkProductImageFormat';

describe('BO - Design - Image Settings - Check product image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number = 0;
  let idProductImage: number = 0;

  const productDataJPG: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'coverJPG.jpg',
    status: true,
  });
  const productDataPNG: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'coverPNG.png',
    status: true,
  });
  const productDataWEBP: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'coverWEBP.webp',
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      productDataJPG.coverImage,
      productDataPNG.coverImage,
      productDataWEBP.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.generateImage(image);
      }
    }));
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      productDataJPG.coverImage,
      productDataPNG.coverImage,
      productDataWEBP.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.deleteFile(image);
      }
    }));
  });

  describe('Enable WebP for image generation', async () => {
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

    it('should enable WebP image format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableWebP', baseContext);

      const result = await boImageSettingsPage.setImageFormatToGenerateChecked(page, 'webp', true);
      expect(result).to.be.eq(boImageSettingsPage.messageSettingsUpdated);
    });

    it('should check image generation options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // JPEG/PNG should be checked
      const jpegChecked = await boImageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // JPEG/PNG should be checked
      const jpegDisabled = await boImageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // WebP should be checked
      const webpChecked = await boImageSettingsPage.isImageFormatToGenerateChecked(page, 'webp');
      expect(webpChecked).to.eq(true);
    });
  });

  [
    {
      product: productDataPNG,
      extOriginal: 'png',
      extGenerated: 'jpg',
      extImageType: 'png',
    },
    {
      product: productDataJPG,
      extOriginal: 'jpg',
      extGenerated: 'jpg',
      extImageType: 'jpg',
    },
    {
      product: productDataWEBP,
      extOriginal: 'webp',
      extGenerated: 'jpg',
      extImageType: 'png',
    },
  ].forEach((arg: {product: FakerProduct, extOriginal: string, extGenerated: string, extImageType: string}, index: number) => {
    describe(`Image Generation - Product - Image Format : ${arg.extOriginal.toUpperCase()}`, async () => {
      if (index) {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBoProducts${arg.extOriginal}`, baseContext);

          page = await foClassicCategoryPage.closePage(browserContext, page, 0);
          await foClassicCategoryPage.goToBO(page);

          const pageTitle = await boDashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(boDashboardPage.pageTitle);
        });
      }
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${arg.extOriginal}`, baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${arg.extOriginal}`, baseContext);

        const isModalVisible = await boProductsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should check the standard product description', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkStandardProductDescription${arg.extOriginal}`,
          baseContext,
        );

        const productTypeDescription = await boProductsPage.getProductDescription(page);
        expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
      });

      it('should choose \'Standard product\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `chooseStandardProduct${arg.extOriginal}`, baseContext);

        await boProductsPage.selectProductType(page, arg.product.type);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${arg.extOriginal}`, baseContext);

        await boProductsPage.clickOnAddNewProduct(page);

        const pageTitle = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
      });

      it('should create standard product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createStandardProduct${arg.extOriginal}`, baseContext);

        await boProductsCreatePage.closeSfToolBar(page);

        const createProductMessage = await boProductsCreatePage.setProduct(page, arg.product);
        expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
      });

      it('should check that the save button is changed to \'Save and publish\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSaveButton${arg.extOriginal}`, baseContext);

        const saveButtonName = await boProductsCreatePage.getSaveButtonName(page);
        expect(saveButtonName).to.equal('Save and publish');

        idProduct = await boProductsCreatePage.getProductID(page);
        idProductImage = await boProductsCreateTabDescriptionPage.getProductIDImageCover(page);
        expect(idProduct).to.be.gt(0);
      });

      it('should check that images are generated', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductImages${arg.extOriginal}`, baseContext);

        const pathProductIdSplitted: RegExpMatchArray|null = idProductImage.toString().match(/./g);

        if (!pathProductIdSplitted) {
          return;
        }

        const pathProductId: string = pathProductIdSplitted.join('/');

        // Check the original file
        const pathImageOriginal: string = `${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProductImage}.jpg`;

        const fileExistsOriginal = await utilsFile.doesFileExist(pathImageOriginal);
        expect(fileExistsOriginal, `The file ${pathImageOriginal} doesn't exist!`).to.eq(true);

        const imageTypeOriginal = await utilsFile.getFileType(pathImageOriginal);
        expect(imageTypeOriginal).to.be.eq(arg.extImageType);

        // Check the Jpg file
        const pathImageJPG: string = `${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProductImage}-large_default.jpg`;

        const fileExistsJPG = await utilsFile.doesFileExist(pathImageJPG);
        expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

        const imageTypeJPG = await utilsFile.getFileType(pathImageJPG);
        expect(imageTypeJPG).to.be.eq(arg.extImageType);

        // Check the WebP file
        const pathImageWEBP: string = `${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProductImage}-large_default.webp`;

        const fileExistsWEBP = await utilsFile.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await utilsFile.getFileType(pathImageWEBP);
        expect(imageTypeWEBP).to.be.eq('webp');
      });

      it('should go to FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${arg.extOriginal}`, baseContext);

        page = await boProductsCreatePage.viewMyShop(page);
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to all products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAllProducts${arg.extOriginal}`, baseContext);

        await foClassicHomePage.goToAllProductsPage(page);

        const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
        expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
      });

      it(`should go to the second page and quick view the product '${arg.product.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `quickViewCustomizedProduct${arg.extOriginal}`, baseContext);

        await foClassicCategoryPage.goToNextPage(page);

        const nthProduct: number|null = await foClassicCategoryPage.getNThChildFromIDProduct(page, idProduct);
        expect(nthProduct).to.not.eq(null);

        await foClassicCategoryPage.quickViewProduct(page, nthProduct as number);

        const isModalVisible = await foClassicModalQuickViewPage.isQuickViewProductModalVisible(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should check that the main image of the quick view is a WebP', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkMainImageQuickView${arg.extOriginal}`, baseContext);

        const quickViewImageMain = await foClassicModalQuickViewPage.getQuickViewImageMain(page);
        expect(quickViewImageMain).to.not.eq(null);

        await utilsFile.downloadFile(quickViewImageMain as string, 'image.img');

        const quickViewImageMainType = await utilsFile.getFileType('image.img');
        expect(quickViewImageMainType).to.be.eq('webp');

        await utilsFile.deleteFile('image.img');
      });
    });
  });

  // Post-condition: Remove products
  [
    {
      product: productDataPNG,
      extension: 'png',
    },
    {
      product: productDataJPG,
      extension: 'jpg',
    },
    {
      product: productDataWEBP,
      extension: 'webp',
    },
  ].forEach((arg: {product: FakerProduct, extension: string}) => {
    deleteProductTest(arg.product, `${baseContext}_removeProduct${arg.extension}`);
  });
});
