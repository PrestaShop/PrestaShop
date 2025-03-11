import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boImageSettingsPage,
  boImageSettingsCreatePage,
  boLoginPage,
  type BrowserContext,
  dataProducts,
  FakerImageType,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

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

  it('should reset all filters and get number of image types in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfImageTypes = await boImageSettingsPage.resetAndGetNumberOfLines(page);
    expect(numberOfImageTypes).to.be.above(0);
  });

  // 1 : Create 2 new image types
  describe('Create 2 image types', async () => {
    testImageTypes.forEach((value: boolean, index: number) => {
      const createImageTypeData: FakerImageType = new FakerImageType({name: `todelete${index}`});

      it('should go to add new image type page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddImageTypePage${index}`, baseContext);

        await boImageSettingsPage.goToNewImageTypePage(page);

        const pageTitle = await boImageSettingsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boImageSettingsCreatePage.pageTitleCreate);
      });

      it(`should create image type n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createImageType${index}`, baseContext);

        const textResult = await boImageSettingsCreatePage.createEditImageType(page, createImageTypeData);
        expect(textResult).to.contains(boImageSettingsPage.successfulCreationMessage);

        const numberOfImageTypesAfterCreation = await boImageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1 + index);
      });
    });

    it('should regenerate thumbnails', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'regenerateThumbnails', baseContext);

      const textResult = await boImageSettingsPage.regenerateThumbnails(page);
      expect(textResult).to.contains(boImageSettingsPage.messageThumbnailsRegenerated);
    });

    testImageTypes.forEach((value: boolean, index: number) => {
      it(`should check thumbnails for the image type n°${index} are regenerated`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkProductsThumbnails${index}`, baseContext);

        const imagePath = await utilsFile.getFilePathAutomaticallyGenerated(
          `img/p/${dataProducts.demo_1.id}/`,
          `${dataProducts.demo_1.id}-todelete${index}.jpg`,
        );

        const exist = await utilsFile.doesFileExist(imagePath);
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

        await boImageSettingsPage.filterTable(page, 'input', 'name', `todelete${index}`);

        const numberOfImageTypesAfterFilter = await boImageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterFilter).to.be.eq(1);

        const textColumn = await boImageSettingsPage.getTextColumn(page, 1, 'name');
        expect(textColumn).to.contains(`todelete${index}`);
      });

      it('should delete image type', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteImageType${index}`, baseContext);

        const textResult = await boImageSettingsPage.deleteImageType(page, 1, value);
        expect(textResult).to.contains(boImageSettingsPage.successfulDeleteMessage);

        const numberOfImageTypesAfterDelete = await boImageSettingsPage.resetAndGetNumberOfLines(page);
        expect(numberOfImageTypesAfterDelete).to.be.equal(numberOfImageTypes + (1 - index));
      });

      it('should check that images relative to image type are not removed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkImagesRelativeImageType${index}`, baseContext);

        const imagePath = await utilsFile.getFilePathAutomaticallyGenerated(
          `img/p/${dataProducts.demo_1.id}/`,
          `${dataProducts.demo_1.id}-todelete${index}.jpg`,
        );

        const exist = await utilsFile.doesFileExist(imagePath);
        expect(exist, 'File doesn\'t exist!').to.be.eq(!value);
      });
    });
  });
});
