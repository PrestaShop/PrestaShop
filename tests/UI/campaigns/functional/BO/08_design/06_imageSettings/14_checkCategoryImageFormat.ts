import testContext from '@utils/testContext';
import {expect} from 'chai';

import bulkDeleteCategoriesTest from '@commonTests/BO/catalog/category';

import {
  boCategoriesPage,
  boCategoriesCreatePage,
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  type BrowserContext,
  FakerCategory,
  foClassicCategoryPage,
  foClassicHomePage,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_checkCategoryImageFormat';

describe('BO - Design - Image Settings - Check category image format', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let idCategory: number = 0;

  const categoryDataJPG: FakerCategory = new FakerCategory({
    coverImage: 'coverJPG.jpg',
    thumbnailImage: 'thumbJPG.jpg',
  });
  const categoryDataPNG: FakerCategory = new FakerCategory({
    coverImage: 'coverPNG.png',
    thumbnailImage: 'thumbPNG.png',
  });
  const categoryDataWEBP: FakerCategory = new FakerCategory({
    coverImage: 'coverWEBP.webp',
    thumbnailImage: 'thumbWEBP.webp',
  });

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      categoryDataJPG.coverImage,
      categoryDataJPG.thumbnailImage,
      categoryDataPNG.coverImage,
      categoryDataPNG.thumbnailImage,
      categoryDataWEBP.coverImage,
      categoryDataWEBP.thumbnailImage,
    ].map(async (image: string|null) => {
      if (image) {
        await utilsFile.generateImage(image);
      }
    }));
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      categoryDataJPG.coverImage,
      categoryDataJPG.thumbnailImage,
      categoryDataPNG.coverImage,
      categoryDataPNG.thumbnailImage,
      categoryDataWEBP.coverImage,
      categoryDataWEBP.thumbnailImage,
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
      extOriginal: 'webp',
      extGenerated: 'jpg',
      extImageType: 'png',
    },
  ].forEach((arg: {category: FakerCategory, extOriginal: string, extGenerated: string, extImageType: string}, index: number) => {
    const argExtension: string = arg.extOriginal;
    describe(
      `Image Generation - Category - Image Format : ${argExtension.toUpperCase()}`,
      async () => {
        if (index) {
          it('should go to BO', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `goToBoProducts${argExtension}`, baseContext);

            page = await foClassicCategoryPage.closePage(browserContext, page, 0);
            await foClassicCategoryPage.goToBO(page);

            const pageTitle = await boDashboardPage.getPageTitle(page);
            expect(pageTitle).to.contains(boDashboardPage.pageTitle);
          });
        }

        it('should go to \'Catalog > Categories\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToCategoriesPage${argExtension}`, baseContext);

          await boDashboardPage.goToSubMenu(
            page,
            boDashboardPage.catalogParentLink,
            boDashboardPage.categoriesLink,
          );

          await boCategoriesPage.closeSfToolBar(page);

          const pageTitle = await boCategoriesPage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
        });

        it('should click on \'Add new category\' button', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `clickOnNewCategoryButton${argExtension}`, baseContext);

          await boCategoriesPage.goToAddNewCategoryPage(page);

          const pageTitle = await boCategoriesCreatePage.getPageTitle(page);
          expect(pageTitle).to.contains(boCategoriesCreatePage.pageTitleCreate);
        });

        it('should create category', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `createCategory${argExtension}`, baseContext);

          await boCategoriesCreatePage.closeSfToolBar(page);

          const textResult = await boCategoriesCreatePage.createEditCategory(page, arg.category);
          expect(textResult).to.equal(boCategoriesPage.successfulCreationMessage);
        });

        it('should search for the new category and fetch the ID', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `searchCreatedCategory${argExtension}`, baseContext);

          await boCategoriesPage.resetFilter(page);
          await boCategoriesPage.filterCategories(
            page,
            'input',
            'name',
            arg.category.name,
          );

          const textColumn = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
          expect(textColumn).to.contains(arg.category.name);

          idCategory = parseInt(await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'id_category'), 10);
        });

        it('should check that images are generated', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkCategoryImages${argExtension}`, baseContext);

          // Check the original image file
          const pathImageJPG: string = `${utilsFile.getRootPath()}/img/c/${idCategory}.jpg`;

          const fileExistsJPG = await utilsFile.doesFileExist(pathImageJPG);
          expect(fileExistsJPG, `The file ${pathImageJPG} doesn't exist!`).to.eq(true);

          const imageTypeJPG = await utilsFile.getFileType(pathImageJPG);
          expect(imageTypeJPG).to.be.eq(arg.extImageType);

          // Check the cover image file category_default jpg thumbnail is generated in proper format
          const pathImageCoverJPG: string = `${utilsFile.getRootPath()}/img/c/${idCategory}-category_default.jpg`;

          const fileExistsCoverJPG = await utilsFile.doesFileExist(pathImageCoverJPG);
          expect(fileExistsCoverJPG, `The file ${pathImageCoverJPG} doesn't exist!`).to.eq(true);

          const imageTypeCoverJPG = await utilsFile.getFileType(pathImageCoverJPG);
          expect(imageTypeCoverJPG).to.be.eq(arg.extImageType);

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/32404
          /*
          // Check the WebP file
          const pathImageWEBP: string = `${utilsFile.getRootPath()}/img/c/${idCategory}-large_default.webp`;

          const fileExistsWEBP = await utilsFile.doesFileExist(pathImageWEBP);
          expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

          const imageTypeWEBP = await utilsFile.getFileType(pathImageWEBP);
          expect(imageTypeWEBP).to.be.eq('webp');
          */

          // Check the cover image file small_default jpg thumbnail is generated in proper format
          const pathImageMetaJPG: string = `${utilsFile.getRootPath()}/img/c/${idCategory}-small_default.jpg`;

          const fileExistsMetaJPG = await utilsFile.doesFileExist(pathImageMetaJPG);
          expect(fileExistsMetaJPG, `The file ${pathImageMetaJPG} doesn't exist!`).to.eq(true);

          const imageTypeMetaJPG = await utilsFile.getFileType(pathImageMetaJPG);
          expect(imageTypeMetaJPG).to.be.eq(arg.extImageType);

          // @todo : https://github.com/PrestaShop/PrestaShop/issues/32404
          /*
          // Check the WebP file
          const pathImageWEBP: string = `${utilsFile.getRootPath()}/img/c/${idCategory}-large_default.webp`;

          const fileExistsWEBP = await utilsFile.doesFileExist(pathImageWEBP);
          expect(fileExistsWEBP, `The file ${pathImageWEBP} doesn't exist!`).to.eq(true);

          const imageTypeWEBP = await utilsFile.getFileType(pathImageWEBP);
          expect(imageTypeWEBP).to.be.eq('webp');
          */
        });

        it('should go to FO page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFo${argExtension}`, baseContext);

          page = await boCategoriesCreatePage.viewMyShop(page);
          await foClassicHomePage.changeLanguage(page, 'en');

          const isHomePage = await foClassicHomePage.isHomePage(page);
          expect(isHomePage, 'Fail to open FO home page').to.eq(true);
        });

        it('should go to all products page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToFoAllProducts${argExtension}`, baseContext);

          await foClassicHomePage.goToAllProductsPage(page);

          const isCategoryPageVisible = await foClassicCategoryPage.isCategoryPage(page);
          expect(isCategoryPageVisible, 'Home category page was not opened').to.eq(true);
        });

        it('should check that the main image of the quick view is a WebP', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkMainImageQuickView${argExtension}`, baseContext);

          const categoryImage = await foClassicCategoryPage.getCategoryImageMain(page, arg.category.name);
          expect(categoryImage).to.not.eq(null);

          await utilsFile.downloadFile(categoryImage as string, 'image.img');

          const categoryImageType = await utilsFile.getFileType('image.img');
          expect(categoryImageType).to.be.eq('webp');

          await utilsFile.deleteFile('image.img');
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
  ].forEach((arg: {category: FakerCategory, extension: string}) => {
    bulkDeleteCategoriesTest(
      {filterBy: 'name', value: arg.category.name},
      `${baseContext}_removeProduct${arg.extension}`,
    );
  });
});
