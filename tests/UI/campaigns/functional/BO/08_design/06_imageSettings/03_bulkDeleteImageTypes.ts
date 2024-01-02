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

const baseContext: string = 'functional_BO_design_imageSettings_bulkDeleteImageTypes';

/*
Create 2 image types
Delete image types by bulk actions
 */
describe('BO - Design - Image Settings : Bulk delete image types', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfImageTypes: number = 0;

  const ImageTypesToCreate: ImageTypeData[] = [
    new ImageTypeData({name: 'todelete1'}),
    new ImageTypeData({name: 'todelete2'}),
  ];

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

  it('should go to \'Catalog > Image Settings\' page', async function () {
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

  describe('Create 2 image types in BO', async () => {
    ImageTypesToCreate.forEach((ImageTypeToCreate: ImageTypeData, index: number) => {
      it('should go to add new image type page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewImageTypePage${index + 1}`, baseContext);

        await imageSettingsPage.goToNewImageTypePage(page);

        const pageTitle = await addImageTypePage.getPageTitle(page);
        expect(pageTitle).to.contains(addImageTypePage.pageTitleCreate);
      });

      it(`should create image type n° ${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createImageType${index + 1}`, baseContext);

        const textResult = await addImageTypePage.createEditImageType(page, ImageTypeToCreate);
        expect(textResult).to.contains(imageSettingsPage.successfulCreationMessage);

        const numberOfImageTypesAfterCreation = await imageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + index + 1);
      });
    });
  });

  describe('Bulk delete image types', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await imageSettingsPage.filterTable(page, 'input', 'name', 'todelete');

      const numberOfImageTypesAfterFilter = await imageSettingsPage.getNumberOfElementInGrid(page);
      expect(numberOfImageTypesAfterFilter).to.be.at.most(numberOfImageTypes);

      for (let i = 1; i <= numberOfImageTypesAfterFilter; i++) {
        const textColumn = await imageSettingsPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete image types with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteImageTypes', baseContext);

      const deleteTextResult = await imageSettingsPage.bulkDeleteImageTypes(page);
      expect(deleteTextResult).to.be.contains(imageSettingsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfImageTypesAfterReset = await imageSettingsPage.resetAndGetNumberOfLines(page);
      expect(numberOfImageTypesAfterReset).to.be.equal(numberOfImageTypes);
    });
  });
});
