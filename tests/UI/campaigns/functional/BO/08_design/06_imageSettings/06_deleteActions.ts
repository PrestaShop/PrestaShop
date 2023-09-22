// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';
import addImageTypePage from '@pages/BO/design/imageSettings/add';

// Import data
import Products from '@data/demo/products';
import ImageTypeData from '@data/faker/imageType';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_deleteActions';

/*
  Create 2 image settings
  Remove image type without deletion of linked images
  Remove image type with deletion of linked images
 */
describe('BO - Design - Image Settings : Delete Actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfImageTypes: number;

  const testImageTypes: boolean[] = [true, false];

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

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

  it('should reset all filters and get number of image types in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfImageTypes = await imageSettingsPage.resetAndGetNumberOfLines(page);
    expect(numberOfImageTypes).to.be.above(0);
  });

  // 1 : Create 2 new image types
  describe('Create 2 image types', async () => {
    testImageTypes.forEach((value: boolean, index: number) => {
      const createImageTypeData: ImageTypeData = new ImageTypeData({name: `todelete${index}`});

      it('should go to add new image type page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddImageTypePage${index}`, baseContext);

        await imageSettingsPage.goToNewImageTypePage(page);

        const pageTitle = await addImageTypePage.getPageTitle(page);
        expect(pageTitle).to.contains(addImageTypePage.pageTitleCreate);
      });

      it(`should create image type n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createImageType${index}`, baseContext);

        const textResult = await addImageTypePage.createEditImageType(page, createImageTypeData);
        expect(textResult).to.contains(imageSettingsPage.successfulCreationMessage);

        const numberOfImageTypesAfterCreation = await imageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1 + index);
      });
    });

    it('should regenerate thumbnails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'regenerateThumbnails', baseContext);

      const textResult = await imageSettingsPage.regenerateThumbnails(page);
      expect(textResult).to.contains(imageSettingsPage.messageThumbnailsRegenerated);
    });

    testImageTypes.forEach((value: boolean, index: number) => {
      it(`should check thumbnails for the image type n°${index} are regenerated`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsThumbnails${index}`, baseContext);

        const imagePath = await files.getFilePathAutomaticallyGenerated(
          `img/p/${Products.demo_1.id}/`,
          `${Products.demo_1.id}-todelete${index}.jpg`,
        );

        const exist = await files.doesFileExist(imagePath);
        expect(exist, 'File doesn\'t exist!').to.eq(true);
      });
    });
  });

  // 2 : Remove image type without deletion of linked images
  // 3 : Remove image type with deletion of linked images
  testImageTypes.forEach((value: boolean, index: number) => {
    describe(`Remove image type ${value ? 'with' : 'without'} deletion of linked images`, async () => {
      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterForBulkDelete${index}`, baseContext);

        await imageSettingsPage.filterTable(page, 'input', 'name', `todelete${index}`);

        const numberOfImageTypesAfterFilter = await imageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterFilter).to.be.eq(1);

        const textColumn = await imageSettingsPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(`todelete${index}`);
      });

      it('should delete image type', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteImageType${index}`, baseContext);

        const textResult = await imageSettingsPage.deleteImageType(page, 1, value);
        expect(textResult).to.contains(imageSettingsPage.successfulDeleteMessage);

        const numberOfImageTypesAfterDelete = await imageSettingsPage.resetAndGetNumberOfLines(page);
        expect(numberOfImageTypesAfterDelete).to.be.equal(numberOfImageTypes + (1 - index));
      });

      it('should check that images relative to image type are not removed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkImagesRelativeImageType${index}`, baseContext);

        const imagePath = await files.getFilePathAutomaticallyGenerated(
          `img/p/${Products.demo_1.id}/`,
          `${Products.demo_1.id}-todelete${index}.jpg`,
        );

        const exist = await files.doesFileExist(imagePath);
        expect(exist, 'File doesn\'t exist!').to.be.eq(!value);
      });
    });
  });
});
