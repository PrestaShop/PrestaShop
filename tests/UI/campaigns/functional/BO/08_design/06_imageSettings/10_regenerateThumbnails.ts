// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {setFeatureFlag} from '@commonTests/BO/advancedParameters/newFeatures';

// Import pages
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';

// Import datas
import {ImageTypeRegenerationSpecific} from '@data/types/imageType';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_regenerateThumbnails';

describe('BO - Design - Image Settings - Regenerate thumbnail', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  type testImageType = {
    name: string
    type: ImageTypeRegenerationSpecific
    directory: string
  };

  const imageTypes: testImageType[] = [
    {
      name: 'Categories',
      type: 'categories' as ImageTypeRegenerationSpecific,
      directory: 'c',
    },
    {
      name: 'Brands',
      type: 'manufacturers' as ImageTypeRegenerationSpecific,
      directory: 'm',
    },
    {
      name: 'Suppliers',
      type: 'suppliers' as ImageTypeRegenerationSpecific,
      directory: 'su',
    },
    {
      name: 'Products',
      type: 'products' as ImageTypeRegenerationSpecific,
      directory: 'p/1',
    },
    {
      name: 'Stores',
      type: 'stores' as ImageTypeRegenerationSpecific,
      directory: 'st',
    },
  ];
  const formats: {
    categories: string[],
    manufacturers: string[],
    suppliers: string[],
    products: string[],
    stores: string[],
  } = {
    categories: [],
    manufacturers: [],
    suppliers: [],
    products: [],
    stores: [],
  };
  const supplierImage: string = `${files.getRootPath()}/img/su/1.jpg`;

  // Pre-condition: Enable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, true, `${baseContext}_enableMultipleImageFormats`);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create image
    await files.generateImage(supplierImage);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    // Delete image
    await files.deleteFile(supplierImage);
  });

  describe('Regenerate thumbnail - BackOffice', async () => {
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

      // WebP should be checked
      const webpChecked = await imageSettingsPage.isImageFormatToGenerateChecked(page, 'webp');
      expect(webpChecked).to.eq(true);
    });
  });

  imageTypes.forEach((arg: testImageType) => {
    describe(`Regenerate thumbnail - ${arg.name}`, async () => {
      it('should fetch image name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.type}FetchImageName`, baseContext);

        formats[arg.type] = await imageSettingsPage.getRegenerateThumbnailsFormats(page, arg.type);
        expect(formats[arg.type].length).to.gt(0);
      });

      it('should delete all images excepted original', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.type}DeleteAllImages`, baseContext);

        await files.deleteFilePattern(`${files.getRootPath()}/img/${arg.directory}/`, /[0-9]+-[A-Za-z0-9_]+.jpg/);
        await files.deleteFilePattern(`${files.getRootPath()}/img/${arg.directory}/`, /[0-9]+-[A-Za-z0-9_]+.webp/);

        const searchedFilesJpeg = await files.getFilesPattern(
          `${files.getRootPath()}/img/${arg.directory}/`,
          /[0-9]+-[A-Za-z0-9_]+.jpg/,
        );
        expect(searchedFilesJpeg.length).to.eq(0);

        const searchedFilesWebp = await files.getFilesPattern(
          `${files.getRootPath()}/img/${arg.directory}/`,
          /[0-9]+-[A-Za-z0-9_]+.webp/,
        );
        expect(searchedFilesWebp.length).to.eq(0);

        await Promise.all(formats[arg.type].map(async (format: string) => {
          const formatFilesJpeg = await files.getFilesPattern(
            `${files.getRootPath()}/img/${arg.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.jpg$`),
          );
          expect(formatFilesJpeg.length).to.eq(
            0,
            `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to 0`,
          );
          const formatFilesWebp = await files.getFilesPattern(
            `${files.getRootPath()}/img/${arg.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.webp$`),
          );
          expect(formatFilesWebp.length).to.eq(
            0,
            `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to 0`,
          );
        }));
      });

      it('should regenerate thumbnails', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.type}RegenerateThumbnails`, baseContext);

        const textResult = await imageSettingsPage.regenerateThumbnails(page, arg.type);
        expect(textResult).to.contains(imageSettingsPage.messageThumbnailsRegenerated);
      });

      it('should check that the form is reset', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.type}CheckFormReset`, baseContext);

        const image = await imageSettingsPage.getRegenerateThumbnailsImage(page);
        expect(image).to.contains('all');
      });

      it('should check that images have been regenerated', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${arg.type}CheckImagesRegenerated`, baseContext);

        const jpgOriginalFiles = await files.getFilesPattern(`${files.getRootPath()}/img/${arg.directory}/`, /^[0-9]+\.jpg$/);
        expect(jpgOriginalFiles.length).to.gt(0);

        await Promise.all(formats[arg.type].map(async (format: string) => {
          const formatFilesJpeg = await files.getFilesPattern(
            `${files.getRootPath()}/img/${arg.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.jpg$`),
          );
          expect(formatFilesJpeg.length).to.eq(
            jpgOriginalFiles.length,
            `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
          );

          const formatFilesWebp = await files.getFilesPattern(
            `${files.getRootPath()}/img/${arg.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.webp$`),
          );
          expect(formatFilesWebp.length).to.eq(
            jpgOriginalFiles.length,
            `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
          );
        }));
      });
    });
  });

  describe('Regenerate thumbnail - All', async () => {
    it('should delete all images excepted original', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'allDeleteAllImages', baseContext);

      await Promise.all(imageTypes.map(async (imageType: testImageType) => {
        await files.deleteFilePattern(`${files.getRootPath()}/img/${imageType.directory}/`, /[0-9]+-[A-Za-z0-9_]+.jpg/);
        await files.deleteFilePattern(`${files.getRootPath()}/img/${imageType.directory}/`, /[0-9]+-[A-Za-z0-9_]+.webp/);

        const searchedFilesJpeg = await files.getFilesPattern(
          `${files.getRootPath()}/img/${imageType.directory}/`,
          /[0-9]+-[A-Za-z0-9_]+.jpg/,
        );
        expect(searchedFilesJpeg.length).to.eq(0);

        const searchedFilesWebp = await files.getFilesPattern(
          `${files.getRootPath()}/img/${imageType.directory}/`,
          /[0-9]+-[A-Za-z0-9_]+.webp/,
        );
        expect(searchedFilesWebp.length).to.eq(0);

        await Promise.all(formats[imageType.type].map(async (format: string) => {
          const formatFilesJpeg = await files.getFilesPattern(
            `${files.getRootPath()}/img/${imageType.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.jpg$`),
          );
          expect(formatFilesJpeg.length).to.eq(
            0,
            `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to 0`,
          );

          const formatFilesWebp = await files.getFilesPattern(
            `${files.getRootPath()}/img/${imageType.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.webp$`),
          );
          expect(formatFilesWebp.length).to.eq(
            0,
            `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to 0`,
          );
        }));
      }));
    });

    it('should regenerate thumbnails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'allRegenerateThumbnails', baseContext);

      const textResult = await imageSettingsPage.regenerateThumbnails(page, 'all');
      expect(textResult).to.contains(imageSettingsPage.messageThumbnailsRegenerated);
    });

    it('should check that the form is reset', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'allCheckFormReset', baseContext);

      const image = await imageSettingsPage.getRegenerateThumbnailsImage(page);
      expect(image).to.contains('all');
    });

    it('should check that images have been regenerated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'allCheckImagesRegenerated', baseContext);

      await Promise.all(imageTypes.map(async (imageType: testImageType) => {
        const jpgOriginalFiles = await files.getFilesPattern(
          `${files.getRootPath()}/img/${imageType.directory}/`,
          /^[0-9]+\.jpg$/,
        );
        expect(jpgOriginalFiles.length).to.gt(0);

        await Promise.all(formats[imageType.type].map(async (format: string) => {
          const formatFilesJpeg = await files.getFilesPattern(
            `${files.getRootPath()}/img/${imageType.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.jpg$`),
          );
          expect(formatFilesJpeg.length).to.eq(
            jpgOriginalFiles.length,
            `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
          );

          const formatFilesWebp = await files.getFilesPattern(
            `${files.getRootPath()}/img/${imageType.directory}/`,
            new RegExp(`^[0-9]+-${format}\\.jpg$`),
          );
          expect(formatFilesWebp.length).to.eq(
            jpgOriginalFiles.length,
            `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
          );
        }));
      }));
    });
  });

  describe('Regenerate thumbnail - Categories - Specific category', async () => {
    it('should delete all images excepted original', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'specificCategoryDeleteAllImages', baseContext);

      await files.deleteFilePattern(`${files.getRootPath()}/img/c/`, /[0-9]+-[A-Za-z0-9_]+.jpg/);
      await files.deleteFilePattern(`${files.getRootPath()}/img/c/`, /[0-9]+-[A-Za-z0-9_]+.webp/);

      const searchedFilesJpeg = await files.getFilesPattern(
        `${files.getRootPath()}/img/c/`,
        /[0-9]+-[A-Za-z0-9_]+.jpg/,
      );
      expect(searchedFilesJpeg.length).to.eq(0);

      const searchedFilesWebp = await files.getFilesPattern(
        `${files.getRootPath()}/img/c/`,
        /[0-9]+-[A-Za-z0-9_]+.webp/,
      );
      expect(searchedFilesWebp.length).to.eq(0);

      await Promise.all(formats.categories.map(async (format: string) => {
        const formatFilesJpeg = await files.getFilesPattern(
          `${files.getRootPath()}/img/c/`,
          new RegExp(`^[0-9]+-${format}\\.jpg$`),
        );
        expect(formatFilesJpeg.length).to.eq(
          0,
          `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to 0`,
        );

        const formatFilesWebp = await files.getFilesPattern(
          `${files.getRootPath()}/img/c/`,
          new RegExp(`^[0-9]+-${format}\\.webp$`),
        );
        expect(formatFilesWebp.length).to.eq(
          0,
          `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to 0`,
        );
      }));
    });

    it('should regenerate thumbnails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'specificCategoryRegenerateThumbnails', baseContext);

      const textResult = await imageSettingsPage.regenerateThumbnails(page, 'categories', formats.categories[0]);
      expect(textResult).to.contains(imageSettingsPage.messageThumbnailsRegenerated);
    });

    it('should check that the form is reset', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'specificCategoryCheckFormReset', baseContext);

      const image = await imageSettingsPage.getRegenerateThumbnailsImage(page);
      expect(image).to.contains('all');
    });

    it('should check that images have been regenerated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'specificCategoryCheckImagesRegenerated', baseContext);

      const jpgOriginalFiles = await files.getFilesPattern(
        `${files.getRootPath()}/img/c`,
        /^[0-9]+\.jpg$/,
      );
      expect(jpgOriginalFiles.length).to.gt(0);

      await Promise.all(formats.categories.map(async (format: string, index: number) => {
        const formatFilesJpeg = await files.getFilesPattern(
          `${files.getRootPath()}/img/c/`,
          new RegExp(`^[0-9]+-${format}\\.jpg$`),
        );
        expect(formatFilesJpeg.length).to.eq(
          index === 0 ? jpgOriginalFiles.length : 0,
          `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
        );

        const formatFilesWebp = await files.getFilesPattern(
          `${files.getRootPath()}/img/c/`,
          new RegExp(`^[0-9]+-${format}\\.webp$`),
        );
        expect(formatFilesWebp.length).to.eq(
          index === 0 ? jpgOriginalFiles.length : 0,
          `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
        );
      }));
    });
  });

  describe('Regenerate thumbnail - Categories - Erase previous images', async () => {
    it('should regenerate thumbnails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'categoryWithEraseRegenerateThumbnails', baseContext);

      const textResult = await imageSettingsPage.regenerateThumbnails(page, 'categories', 'All', true);
      expect(textResult).to.contains(imageSettingsPage.messageThumbnailsRegenerated);
    });

    it('should check that the form is reset', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'categoryWithEraseCheckFormReset', baseContext);

      const image = await imageSettingsPage.getRegenerateThumbnailsImage(page);
      expect(image).to.contains('all');
    });

    it('should check that images have been regenerated', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'categoryWithEraseCheckImagesRegenerated', baseContext);

      const jpgOriginalFiles = await files.getFilesPattern(
        `${files.getRootPath()}/img/c`,
        /^[0-9]+\.jpg$/,
      );
      expect(jpgOriginalFiles.length).to.gt(0);

      await Promise.all(formats.categories.map(async (format: string) => {
        const formatFilesJpeg = await files.getFilesPattern(
          `${files.getRootPath()}/img/c/`,
          new RegExp(`^[0-9]+-${format}\\.jpg$`),
        );
        expect(formatFilesJpeg.length).to.eq(
          jpgOriginalFiles.length,
          `The number of files (${formatFilesJpeg.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
        );

        const formatFilesWebp = await files.getFilesPattern(
          `${files.getRootPath()}/img/c/`,
          new RegExp(`^[0-9]+-${format}\\.webp$`),
        );
        expect(formatFilesWebp.length).to.eq(
          jpgOriginalFiles.length,
          `The number of files (${formatFilesWebp.length}) for format ${format} is not equals to ${jpgOriginalFiles.length}`,
        );
      }));
    });
  });

  // Post-condition: Disable Multiple image formats
  setFeatureFlag(featureFlagPage.featureFlagMultipleImageFormats, false, `${baseContext}_disableMultipleImageFormats`);
});
