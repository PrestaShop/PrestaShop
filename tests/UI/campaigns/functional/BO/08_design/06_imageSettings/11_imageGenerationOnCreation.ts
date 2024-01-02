// Import utils
import basicHelper from '@utils/basicHelper';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import categoriesPage from '@pages/BO/catalog/categories';
import addCategoryPage from '@pages/BO/catalog/categories/add';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';

// Import data
import CategoryData from '@data/faker/category';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_imageGenerationOnCreation';

describe('BO - Design - Image Settings - Image Generation on creation', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let imageTypeProducts: string[] = [];
  let imageTypeCategories: string[] = [];
  let idProduct: number = 0;
  let idCategory: number = 0;

  const productData: ProductData = new ProductData({
    type: 'standard',
    coverImage: 'cover.jpg',
    thumbImage: 'thumb.jpg',
    taxRule: 'FR Taux standard (20%)',
    tax: 20,
    quantity: 100,
    minimumQuantity: 2,
    status: true,
  });
  const categoryData: CategoryData = new CategoryData({
    coverImage: 'cover.jpg',
    thumbnailImage: 'thumb.jpg',
  });

  // Pre-condition: Enable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, true, `${baseContext}_enableMultipleImageFormats`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      productData.coverImage,
      productData.thumbImage,
      categoryData.coverImage,
      categoryData.thumbnailImage,
    ].map(async (image: string|null) => {
      if (image) {
        await files.generateImage(image);
      }
    }));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      productData.coverImage,
      productData.thumbImage,
      categoryData.coverImage,
      categoryData.thumbnailImage,
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

    it('should fetch image name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchImageName', baseContext);

      imageTypeProducts = await imageSettingsPage.getRegenerateThumbnailsFormats(page, 'products');
      expect(imageTypeProducts.length).to.gt(0);

      imageTypeCategories = await imageSettingsPage.getRegenerateThumbnailsFormats(page, 'categories');
      expect(imageTypeCategories.length).to.gt(0);
    });
  });

  describe('Image Generation - Product', async () => {
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

    it('should check the standard product description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkStandardProductDescription', baseContext);

      const productTypeDescription = await productsPage.getProductDescription(page);
      expect(productTypeDescription).to.contains(productsPage.standardProductDescription);
    });

    it('should choose \'Standard product\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseStandardProduct', baseContext);

      await productsPage.selectProductType(page, productData.type);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should go to new product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewProductPage', baseContext);

      await productsPage.clickOnAddNewProduct(page);

      const pageTitle = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should create standard product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createStandardProduct', baseContext);

      await createProductsPage.closeSfToolBar(page);

      const createProductMessage = await createProductsPage.setProduct(page, productData);
      expect(createProductMessage).to.equal(createProductsPage.successfulUpdateMessage);
    });

    it('should check the product header details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductHeaderDetails', baseContext);

      const taxValue = await basicHelper.percentage(productData.priceTaxExcluded, productData.tax);

      const productHeaderSummary = await createProductsPage.getProductHeaderSummary(page);
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

      const saveButtonName = await createProductsPage.getSaveButtonName(page);
      expect(saveButtonName).to.equal('Save and publish');

      idProduct = await createProductsPage.getProductID(page);
      expect(idProduct).to.be.gt(0);
    });

    it('should check that images are generated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductImages', baseContext);

      const pathProductIdSplitted = idProduct.toString().match(/./g);

      if (!pathProductIdSplitted) {
        return;
      }

      const pathProductId: string = pathProductIdSplitted.join('/');

      const fileJpegExists = await files.doesFileExist(`${files.getRootPath()}/img/p/${pathProductId}/${idProduct}.jpg`);
      expect(fileJpegExists, 'File doesn\'t exist!').to.eq(true);

      await Promise.all(imageTypeProducts.map(async (imageTypeName: string) => {
        const fileJpegExists = await files.doesFileExist(
          `${files.getRootPath()}/img/p/${pathProductId}/${idProduct}-${imageTypeName}.jpg`,
        );
        expect(fileJpegExists, 'File doesn\'t exist!').to.eq(true);

        const fileWebpExists = await files.doesFileExist(
          `${files.getRootPath()}/img/p/${pathProductId}/${idProduct}-${imageTypeName}.webp`,
        );
        expect(fileWebpExists, 'File doesn\'t exist!').to.eq(true);
      }));
    });
  });

  describe('Image Generation - Category', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.categoriesLink,
      );
      await categoriesPage.closeSfToolBar(page);

      const pageTitle = await categoriesPage.getPageTitle(page);
      expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should go to add new category page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewCategoryPage', baseContext);

      await categoriesPage.goToAddNewCategoryPage(page);

      const pageTitle = await addCategoryPage.getPageTitle(page);
      expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
    });

    it('should create category', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createCategory', baseContext);

      const textResult = await addCategoryPage.createEditCategory(page, categoryData);
      expect(textResult).to.equal(categoriesPage.successfulCreationMessage);
    });

    it('should filter category by Name and fetch the ID', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCategoryByName', baseContext);

      await categoriesPage.filterCategories(
        page,
        'input',
        'name',
        categoryData.name,
      );

      const numberOfCategoriesAfterFilter = await categoriesPage.getNumberOfElementInGrid(page);
      expect(numberOfCategoriesAfterFilter).to.be.eq(1);

      idCategory = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
      expect(idCategory).to.be.gt(0);
    });

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/30520
    it('should check that images are generated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkProductImagesFinal', baseContext);

      this.skip();

      // Category Image
      const categoryImageExists = await files.doesFileExist(`${files.getRootPath()}/img/c/${idCategory}.jpg`);
      expect(categoryImageExists, `File ${idCategory}.jpg doesn't exist!`).to.eq(true);

      // Thumnbail Image
      const thumbnailImageExists = await files.doesFileExist(`${files.getRootPath()}/img/c/${idCategory}_thumb.jpg`);
      expect(thumbnailImageExists, `File ${idCategory}.jpg doesn't exist!`).to.eq(true);

      await Promise.all(imageTypeCategories.map(async (imageTypeName: string) => {
        const fileJpegExists = await files.doesFileExist(
          `${files.getRootPath()}/img/c/${idCategory}-${imageTypeName}.jpg`,
        );
        expect(fileJpegExists, `File ${idCategory}-${imageTypeName}.jpg doesn't exist!`).to.eq(true);

        const fileWebpExists = await files.doesFileExist(
          `${files.getRootPath()}/img/c/${idCategory}-${imageTypeName}.webp`,
        );
        expect(fileWebpExists, `File ${idCategory}-${imageTypeName}.webp doesn't exist!`).to.eq(true);
      }));
    });
  });

  // Post-condition: Disable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, false, `${baseContext}_disableMultipleImageFormats`);
});
