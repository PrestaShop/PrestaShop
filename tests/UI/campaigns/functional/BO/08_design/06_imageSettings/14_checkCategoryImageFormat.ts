// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {setFeatureFlag} from '@commonTests/BO/advancedParameters/newFeatures';
import bulkDeleteCategoriesTest from '@commonTests/BO/catalog/category';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import categoriesPage from '@pages/BO/catalog/categories';
import addCategoryPage from '@pages/BO/catalog/categories/add';
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';
// Import FO pages
import {homePage} from '@pages/FO/home';
import categoryPage from '@pages/FO/category';

// Import data
import CategoryData from '@data/faker/category';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_checkCategoryImageFormat';

describe('BO - Design - Image Settings - Check category image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idCategory: number = 0;

  const categoryDataJPG: CategoryData = new CategoryData({
    coverImage: 'coverJPG.jpg',
    thumbnailImage: 'thumbJPG.jpg',
    metaImage: 'metaJPG.jpg',
  });
  const categoryDataPNG: CategoryData = new CategoryData({
    coverImage: 'coverPNG.png',
    thumbnailImage: 'thumbPNG.png',
    metaImage: 'metaPNG.png',
  });
  const categoryDataWEBP: CategoryData = new CategoryData({
    coverImage: 'coverWEBP.webp',
    thumbnailImage: 'thumbWEBP.webp',
    metaImage: 'metaWEBP.webp',
  });

  // Pre-condition: Enable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, true, `${baseContext}_enableMultipleImageFormats`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      categoryDataJPG.coverImage,
      categoryDataJPG.thumbnailImage,
      categoryDataJPG.metaImage,
      categoryDataPNG.coverImage,
      categoryDataPNG.thumbnailImage,
      categoryDataPNG.metaImage,
      categoryDataWEBP.coverImage,
      categoryDataWEBP.thumbnailImage,
      categoryDataWEBP.metaImage,
    ].map(async (image: string|null) => {
      if (image) {
        await files.generateImage(image);
      }
    }));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      categoryDataJPG.coverImage,
      categoryDataJPG.thumbnailImage,
      categoryDataJPG.metaImage,
      categoryDataPNG.coverImage,
      categoryDataPNG.thumbnailImage,
      categoryDataPNG.metaImage,
      categoryDataWEBP.coverImage,
      categoryDataWEBP.thumbnailImage,
      categoryDataWEBP.metaImage,
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
      await expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
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
      await expect(jpegChecked).to.be.true;

      // JPEG/PNG should be checked
      const jpegDisabled = await imageSettingsPage.isImageFormatToGenerateDisabled(page, 'jpg');
      await expect(jpegDisabled).to.be.true;

      // WebP should be checked
      const webpChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'webp');
      await expect(webpChecked).to.be.true;
    });
  });

  [
    {
      category: categoryDataPNG,
      extOriginal: 'png',
      extGenerated: 'jpg',
      extImageType: 'png',
    },
    {
      category: categoryDataJPG,
      extOriginal: 'jpg',
      extGenerated: 'jpg',
      extImageType: 'jpg',
    },
    {
      category: categoryDataWEBP,
      extOriginal: 'jpg',
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/32408
      // extOriginal: 'webp',
      extGenerated: 'webp',
      extImageType: 'jpg',
    },
  ].forEach((arg: {category: CategoryData, extOriginal: string, extGenerated: string, extImageType: string}, index: number) => {
    const argExtension: string = index === 2 ? arg.extGenerated : arg.extOriginal;
    describe(
      `Image Generation - Category - Image Format : ${argExtension.toUpperCase()}`,
      async () => {
        if (index) {
          it('should go to BO', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToBoProducts${argExtension}`, baseContext);

            page = await categoryPage.closePage(browserContext, page, 0);
            await categoryPage.goToBO(page);

            const pageTitle = await dashboardPage.getPageTitle(page);
            await expect(pageTitle).to.contains(dashboardPage.pageTitle);
          });
        }

        it('should go to \'Catalog > Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCategoriesPage${argExtension}`, baseContext);

          await dashboardPage.goToSubMenu(
            page,
            dashboardPage.catalogParentLink,
            dashboardPage.categoriesLink,
          );

          await categoriesPage.closeSfToolBar(page);

          const pageTitle = await categoriesPage.getPageTitle(page);
          await expect(pageTitle).to.contains(categoriesPage.pageTitle);
        });

        it('should click on \'Add new category\' button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `clickOnNewCategoryButton${argExtension}`, baseContext);

          await categoriesPage.goToAddNewCategoryPage(page);

          const pageTitle = await addCategoryPage.getPageTitle(page);
          await expect(pageTitle).to.contains(addCategoryPage.pageTitleCreate);
        });

        it('should create category', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCategory${argExtension}`, baseContext);

          await addCategoryPage.closeSfToolBar(page);

          const textResult = await addCategoryPage.createEditCategory(page, arg.category);
          await expect(textResult).to.equal(categoriesPage.successfulCreationMessage);
        });

        it('should search for the new category and fetch the ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchCreatedCategory${argExtension}`, baseContext);

          await categoriesPage.resetFilter(page);
          await categoriesPage.filterCategories(
            page,
            'input',
            'name',
            arg.category.name,
          );

          const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
          await expect(textColumn).to.contains(arg.category.name);

          idCategory = parseInt(await categoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
        });

        it('should check that images are generated', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkCategoryImages${argExtension}`, baseContext);

          // Check the original image file
          const pathImageJPG: string = `${files.getRootPath()}/img/c/${idCategory}.jpg`;

          const fileExistsJPG = await files.doesFileExist(pathImageJPG);
          await expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.be.true;

          const imageTypeJPG = await files.getImageType(pathImageJPG);
          await expect(imageTypeJPG).to.be.eq(arg.extOriginal);

          // Check the cover image file
          const pathImageCoverJPG: string = `${files.getRootPath()}/img/c/${idCategory}-category_default.jpg`;

          const fileExistsCoverJPG = await files.doesFileExist(pathImageCoverJPG);
          await expect(fileExistsCoverJPG, `The file ${pathImageCoverJPG} doesn't exist!`).to.be.true;

          const imageTypeCoverJPG = await files.getImageType(pathImageCoverJPG);
          await expect(imageTypeCoverJPG).to.be.eq(arg.extImageType);

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/32404
          /*
          // Check the WebP file
          const pathImageWEBP: string = `${files.getRootPath()}/img/c/${idCategory}-large_default.webp`;

          const fileExistsWEBP = await files.doesFileExist(pathImageWEBP);
          await expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.be.true;

          const imageTypeWEBP = await files.getImageType(pathImageWEBP);
          await expect(imageTypeWEBP).to.be.eq('webp');
          */

          // Check the thumbnail image file
          const pathImageThumbJPG: string = `${files.getRootPath()}/img/c/${idCategory}-0_thumb.jpg`;

          const fileExistsThumbJPG = await files.doesFileExist(pathImageThumbJPG);
          await expect(fileExistsThumbJPG, `The file ${pathImageThumbJPG} doesn't exist!`).to.be.true;

          const imageTypeThumbJPG = await files.getImageType(pathImageThumbJPG);
          await expect(imageTypeThumbJPG).to.be.eq(arg.extOriginal);

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/32404
          /*
          // Check the WebP file
          const pathImageWEBP: string = `${files.getRootPath()}/img/c/${idCategory}-large_default.webp`;

          const fileExistsWEBP = await files.doesFileExist(pathImageWEBP);
          await expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.be.true;

          const imageTypeWEBP = await files.getImageType(pathImageWEBP);
          await expect(imageTypeWEBP).to.be.eq('webp');
          */

          // Check the Menu image file
          const pathImageMetaJPG: string = `${files.getRootPath()}/img/c/${idCategory}-small_default.jpg`;

          const fileExistsMetaJPG = await files.doesFileExist(pathImageMetaJPG);
          await expect(fileExistsMetaJPG, `The file ${pathImageMetaJPG} doesn't exist!`).to.be.true;

          const imageTypeMetaJPG = await files.getImageType(pathImageMetaJPG);
          await expect(imageTypeMetaJPG).to.be.eq(arg.extOriginal);

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/32404
          /*
          // Check the WebP file
          const pathImageWEBP: string = `${files.getRootPath()}/img/c/${idCategory}-large_default.webp`;

          const fileExistsWEBP = await files.doesFileExist(pathImageWEBP);
          await expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.be.true;

          const imageTypeWEBP = await files.getImageType(pathImageWEBP);
          await expect(imageTypeWEBP).to.be.eq('webp');
          */
        });

        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFo${argExtension}`, baseContext);

          page = await addCategoryPage.viewMyShop(page);
          await homePage.changeLanguage(page, 'en');

          const isHomePage = await homePage.isHomePage(page);
          await expect(isHomePage, 'Fail to open FO home page').to.be.true;
        });

        it('should go to all products page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFoAllProducts${argExtension}`, baseContext);

          await homePage.goToAllProductsPage(page);

          const isCategoryPageVisible = await categoryPage.isCategoryPage(page);
          await expect(isCategoryPageVisible, 'Home category page was not opened').to.be.true;
        });

        it('should check that the main image of the quick view is a WebP', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkMainImageQuickView${argExtension}`, baseContext);

          const categoryImage = await categoryPage.getCategoryImageMain(page, arg.category.name);
          await expect(categoryImage).to.be.not.null;

          await files.downloadFile(categoryImage as string, 'image.img');

          const categoryImageType = await files.getImageType('image.img');
          await expect(categoryImageType).to.be.eq('webp');

          await files.deleteFile('image.img');
        });
      });
  });

  // Post-condition: Remove categories
  [
    {
      category: categoryDataPNG,
      extension: 'png',
    },
    {
      category: categoryDataJPG,
      extension: 'jpg',
    },
    {
      category: categoryDataWEBP,
      extension: 'webp',
    },
  ].forEach((arg: {category: CategoryData, extension: string}) => {
    bulkDeleteCategoriesTest(
      {filterBy: 'name', value: arg.category.name},
      `${baseContext}_removeProduct${arg.extension}`,
    );
  });

  // Post-condition: Disable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, false, `${baseContext}_disableMultipleImageFormats`);
});
