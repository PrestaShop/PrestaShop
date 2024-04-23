// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';
import addImageTypePage from '@pages/BO/design/imageSettings/add';

// Import data
import ImageTypeData from '@data/faker/imageType';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_imageSettings_CRUDImageType';

/*
Create image type
Update image type
Delete image type
 */
describe('BO - Design - Image Settings : CRUD image type in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfImageTypes: number = 0;

  const createImageTypeData: ImageTypeData = new ImageTypeData();
  const editImageTypeData: ImageTypeData = new ImageTypeData();

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

  // 1 - Create image type
  describe('Create image type in BO', async () => {
    it('should go to add new image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddImageTypePage', baseContext);

      await imageSettingsPage.goToNewImageTypePage(page);

      const pageTitle = await addImageTypePage.getPageTitle(page);
      expect(pageTitle).to.equal(addImageTypePage.pageTitleCreate);
    });

    it('should create image type and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createImageType', baseContext);

      const textResult = await addImageTypePage.createEditImageType(page, createImageTypeData);
      expect(textResult).to.contains(imageSettingsPage.successfulCreationMessage);

      const numberOfImageTypesAfterCreation = await imageSettingsPage.getNumberOfElementInGrid(page);
      expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1);
    });
  });

  // 2 - Update image type
  describe('Update image type', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await imageSettingsPage.resetFilter(page);
      await imageSettingsPage.filterTable(
        page,
        'input',
        'name',
        createImageTypeData.name,
      );

      const textEmail = await imageSettingsPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(createImageTypeData.name);
    });

    it('should go to edit image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditImageTypePage', baseContext);

      await imageSettingsPage.gotoEditImageTypePage(page, 1);

      const pageTitle = await addImageTypePage.getPageTitle(page);
      expect(pageTitle).to.equal(addImageTypePage.pageTitleEdit(createImageTypeData.name));
    });

    it('should update image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateImageType', baseContext);

      const textResult = await addImageTypePage.createEditImageType(page, editImageTypeData);
      expect(textResult).to.contains(imageSettingsPage.successfulUpdateMessage);

      const numberOfImageTypesAfterUpdate = await imageSettingsPage.resetAndGetNumberOfLines(page);
      expect(numberOfImageTypesAfterUpdate).to.be.equal(numberOfImageTypes + 1);
    });
  });

  // 3 - Delete image type
  describe('Delete image type', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await imageSettingsPage.resetFilter(page);
      await imageSettingsPage.filterTable(
        page,
        'input',
        'name',
        editImageTypeData.name,
      );

      const textEmail = await imageSettingsPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(editImageTypeData.name);
    });

    it('should delete image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteImageType', baseContext);

      const textResult = await imageSettingsPage.deleteImageType(page, 1);
      expect(textResult).to.contains(imageSettingsPage.successfulDeleteMessage);

      const numberOfImageTypesAfterDelete = await imageSettingsPage.resetAndGetNumberOfLines(page);
      expect(numberOfImageTypesAfterDelete).to.be.equal(numberOfImageTypes);
    });
  });
});
