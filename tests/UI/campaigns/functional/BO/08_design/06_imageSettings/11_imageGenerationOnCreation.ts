import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  type BrowserContext,
  FakerCategory,
  FakerProduct,
  type Page,
  utilsCore,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_imageGenerationOnCreation';

describe('BO - Design - Image Settings - Image Generation on creation', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let imageTypeProducts: string[] = [];
  let imageTypeCategories: string[] = [];
  let idProduct: number = 0;
  let idCategory: number = 0;

  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });
  const categoryData: FakerCategory = new FakerCategory({
    coverImage: 'cover.jpg',
    thumbnailImage: 'thumb.jpg',
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      productData.coverImage,
      productData.thumbImage,
      categoryData.coverImage,
      categoryData.thumbnailImage,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.generateImage(image);
      }
    }));
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      productData.coverImage,
      productData.thumbImage,
      categoryData.coverImage,
      categoryData.thumbnailImage,
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

    it('should fetch image name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchImageName', baseContext);

      imageTypeProducts = await boImageSettingsPage.getRegenerateThumbnailsFormats(page, 'products');
      expect(imageTypeProducts.length).to.gt(0);

      imageTypeCategories = await boImageSettingsPage.getRegenerateThumbnailsFormats(page, 'categories');
      expect(imageTypeCategories.length).to.gt(0);
    });
  });

  describe('Image Generation - Product', async () => {
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

    it('should check the standard product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      const productTypeDescription = await boProductsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(boProductsPage.standardProductDescription);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await boProductsPage.selectProductType(page, productData.type);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await boProductsPage.clickOnAddNewProduct(page);

      const pageTitle = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await boProductsCreatePage.closeSfToolBar(page);

      const createProductMessage = await boProductsCreatePage.setProduct(page, productData);
      expect(createProductMessage).to.equal(boProductsCreatePage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue = utilsCore.percentage(productData.priceTaxExcluded, productData.tax);

      const productHeaderSummary = await boProductsCreatePage.getProductHeaderSummary(page);
      await Promise.all([
        expect(productHeaderSummary.priceTaxExc).to.equal(`€${(productData.priceTaxExcluded.toFixed(2))} tax excl.`),
        expect(productHeaderSummary.priceTaxIncl).to.equal(
          `€${(productData.priceTaxExcluded + taxValue).toFixed(2)} tax incl. (tax rule: ${productData.tax}%)`),
        expect(productHeaderSummary.quantity).to.equal(`${productData.quantity} in stock`),
        expect(productHeaderSummary.reference).to.contains(productData.reference),
      ]);
    });

    it('should check that the save button is changed to \'Save and publish\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSaveButton', baseContext);

      const saveButtonName = await boProductsCreatePage.getSaveButtonName(page);
      expect(saveButtonName).to.equal('Save and publish');

      idProduct = await boProductsCreatePage.getProductID(page);
      expect(idProduct).to.be.gt(0);
    });

    it('should check that images are generated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductImages', baseContext);

      const pathProductIdSplitted = idProduct.toString().match(/./g);

      if (!pathProductIdSplitted) {
        return;
      }

      const pathProductId: string = pathProductIdSplitted.join('/');

      const fileJpegExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProduct}.jpg`);
      expect(fileJpegExists, 'File doesn\'t exist!').to.eq(true);

      await Promise.all(imageTypeProducts.map(async (imageTypeName: string) => {
        const fileJpegExists = await utilsFile.doesFileExist(
          `${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProduct}-${imageTypeName}.jpg`,
        );
        expect(fileJpegExists, 'File doesn\'t exist!').to.eq(true);

        const fileWebpExists = await utilsFile.doesFileExist(
          `${utilsFile.getRootPath()}/img/p/${pathProductId}/${idProduct}-${imageTypeName}.webp`,
        );
        expect(fileWebpExists, 'File doesn\'t exist!').to.eq(true);
      }));
    });
  });

  describe('Image Generation - Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.categoriesLink,
      );
      await boCategoriesPage.closeSfToolBar(page);

      const pageTitle = await boCategoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
    });

    it('should go to add new category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCategoryPage', baseContext);

      await boCategoriesPage.goToAddNewCategoryPage(page);

      const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
    });

    it('should create category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

      const textResult = await boCategoriesCreatePage.createEditCategory(page, categoryData);
      expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);
    });

    it('should filter category by Name and fetch the ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCategoryByName', baseContext);

      await boCategoriesPage.filterCategories(
        page,
        'input',
        'name',
        categoryData.name,
      );

      const numberOfCategoriesAfterFilter = await boCategoriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCategoriesAfterFilter).to.be.eq(1);

      idCategory = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
      expect(idCategory).to.be.gt(0);
    });

    it('should check that images are generated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductImagesFinal', baseContext);

      // Category Image
      const categoryImageExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/img/c/${idCategory}.jpg`);
      expect(categoryImageExists, `File ${idCategory}.jpg doesn't exist!`).to.eq(true);

      // Thumnbail Image
      const thumbnailImageExists = await utilsFile.doesFileExist(`${utilsFile.getRootPath()}/img/c/${idCategory}_thumb.jpg`);
      expect(thumbnailImageExists, `File ${idCategory}.jpg doesn't exist!`).to.eq(true);

      await Promise.all(imageTypeCategories.map(async (imageTypeName: string) => {
        const fileJpegExists = await utilsFile.doesFileExist(
          `${utilsFile.getRootPath()}/img/c/${idCategory}-${imageTypeName}.jpg`,
        );
        expect(fileJpegExists, `File ${idCategory}-${imageTypeName}.jpg doesn't exist!`).to.eq(true);

        const fileWebpExists = await utilsFile.doesFileExist(
          `${utilsFile.getRootPath()}/img/c/${idCategory}-${imageTypeName}.webp`,
        );
        expect(fileWebpExists, `File ${idCategory}-${imageTypeName}.webp doesn't exist!`).to.eq(true);
      }));
    });
  });
});
