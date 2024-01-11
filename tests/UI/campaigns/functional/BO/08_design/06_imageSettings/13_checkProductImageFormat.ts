// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';
// Import FO pages
import {homePage} from '@pages/FO/home';
import categoryPage from '@pages/FO/category';

// Import data
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_checkProductImageFormat';

describe('BO - Design - Image Settings - Check product image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number = 0;
  let idProductImage: number = 0;

  const productDataJPG: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'coverJPG.jpg',
    status: true,
  });
  const productDataPNG: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'coverPNG.png',
    status: true,
  });
  const productDataWEBP: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'coverWEBP.webp',
    status: true,
  });

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      productDataJPG.coverImage,
      productDataPNG.coverImage,
      productDataWEBP.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await files.generateImage(image);
      }
    }));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      productDataJPG.coverImage,
      productDataPNG.coverImage,
      productDataWEBP.coverImage,
    ].map(async (image: string|null) => {
      if (image) {
        await files.deleteFile(image);
      }
    }));
  });

  describe('Enable WebP for image generation', async () => {
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

    it('should enable WebP image format', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableWebP', baseContext);

      const result = await imageSettingsPage.setImageFormatToGenerateChecked(page, 'webp', true);
      expect(result).to.be.eq(imageSettingsPage.messageSettingsUpdated);
    });

    it('should check image generation options', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageGenerationOptions', baseContext);

      // JPEG/PNG should be checked
      const jpegChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'jpg');
      expect(jpegChecked).to.eq(true);

      // JPEG/PNG should be checked
      const jpegDisabled = await imageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      expect(jpegDisabled).to.eq(true);

      // WebP should be checked
      const webpChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'webp');
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
  ].forEach((arg: {product: ProductData, extOriginal: string, extGenerated: string, extImageType: string}, index: number) => {
    describe(`Image Generation - Product - Image Format : ${arg.extOriginal.toUpperCase()}`, async () => {
      if (index) {
        it('should go to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBoProducts${arg.extOriginal}`, baseContext);

          page = await categoryPage.closePage(browserContext, page, 0);
          await categoryPage.goToBO(page);

          const pageTitle = await dashboardPage.getPageTitle(page);
          expect(pageTitle).to.contains(dashboardPage.pageTitle);
        });
      }
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToProductsPage${arg.extOriginal}`, baseContext);

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
        await testContext.addContextItem(this, 'testIdentifier', `clickOnNewProductButton${arg.extOriginal}`, baseContext);

        const isModalVisible = await productsPage.clickOnNewProductButton(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should check the standard product description', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkStandardProductDescription${arg.extOriginal}`,
          baseContext,
        );

        const productTypeDescription = await productsPage.getProductDescription(page);
        expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
      });

      it('should choose \'Standard product\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `chooseStandardProduct${arg.extOriginal}`, baseContext);

        await productsPage.selectProductType(page, arg.product.type);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should go to new product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewProductPage${arg.extOriginal}`, baseContext);

        await productsPage.clickOnAddNewProduct(page);

        const pageTitle = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });

      it('should create standard product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createStandardProduct${arg.extOriginal}`, baseContext);

        await createProductsPage.closeSfToolBar(page);

        const createProductMessage = await createProductsPage.setProduct(page, arg.product);
        expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
      });

      it('should check that the save button is changed to \'Save and publish\'', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkSaveButton${arg.extOriginal}`, baseContext);

        const saveButtonName = await createProductsPage.getSaveButtonName(page);
        expect(saveButtonName).to.equal('Save and publish');

        idProduct = await createProductsPage.getProductID(page);
        idProductImage = await descriptionTab.getProductIDImageCover(page);
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
        const pathImageOriginal: string = `${files.getRootPath()}/img/p/${pathProductId}/${idProductImage}.jpg`;

        const fileExistsOriginal = await files.doesFileExist(pathImageOriginal);
        expect(fileExistsOriginal, `The file ${pathImageOriginal} doesn't exist!`).to.eq(true);

        const imageTypeOriginal = await files.getFileType(pathImageOriginal);
        expect(imageTypeOriginal).to.be.eq(arg.extImageType);

        // Check the Jpg file
        const pathImageJPG: string = `${files.getRootPath()}/img/p/${pathProductId}/${idProductImage}-large_default.jpg`;

        const fileExistsJPG = await files.doesFileExist(pathImageJPG);
        expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

        const imageTypeJPG = await files.getFileType(pathImageJPG);
        expect(imageTypeJPG).to.be.eq(arg.extImageType);

        // Check the WebP file
        const pathImageWEBP: string = `${files.getRootPath()}/img/p/${pathProductId}/${idProductImage}-large_default.webp`;

        const fileExistsWEBP = await files.doesFileExist(pathImageWEBP);
        expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

        const imageTypeWEBP = await files.getFileType(pathImageWEBP);
        expect(imageTypeWEBP).to.be.eq('webp');
      });

      it('should go to FO page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFo${arg.extOriginal}`, baseContext);

        page = await createProductsPage.viewMyShop(page);
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage, 'Fail to open FO home page').to.eq(true);
      });

      it('should go to all products page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAllProducts${arg.extOriginal}`, baseContext);

        await homePage.goToAllProductsPage(page);

        const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
        expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
      });

      it(`should go to the second page and quick view the product '${arg.product.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `quickViewCustomizedProduct${arg.extOriginal}`, baseContext);

        await categoryPage.goToNextPage(page);

        const nthProduct: number|null = await categoryPage.getNThChildFromIDProduct(page, idProduct);
        expect(nthProduct).to.not.eq(null);

        await categoryPage.quickViewProduct(page, nthProduct as number);

        const isModalVisible = await categoryPage.isQuickViewProductModalVisible(page);
        expect(isModalVisible).to.eq(true);
      });

      it('should check that the main image of the quick view is a WebP', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkMainImageQuickView${arg.extOriginal}`, baseContext);

        const quickViewImageMain = await categoryPage.getQuickViewImageMain(page);
        expect(quickViewImageMain).to.not.eq(null);

        await files.downloadFile(quickViewImageMain as string, 'image.img');

        const quickViewImageMainType = await files.getFileType('image.img');
        expect(quickViewImageMainType).to.be.eq('webp');

        await files.deleteFile('image.img');
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
  ].forEach((arg: {product: ProductData, extension: string}) => {
    deleteProductTest(arg.product, `${baseContext}_removeProduct${arg.extension}`);
  });
});
