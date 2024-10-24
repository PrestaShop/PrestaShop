// Import utils
import testContext from '@utils/testContext';

// Import pages
import imageSettingsPage from '@pages/BO/design/imageSettings';
import addImageTypePage from '@pages/BO/design/imageSettings/add';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerImageType,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_bulkDeleteImageTypes';

/*
Create 2 image types
Delete image types by bulk actions
 */
describe('BO - Design - Image Settings : Bulk delete image types', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfImageTypes: number = 0;

  const ImageTypesToCreate: FakerImageType[] = [
    new FakerImageType({name: 'todelete1'}),
    new FakerImageType({name: 'todelete2'}),
  ];

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

  it('should go to \'Catalog > Image Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.imageSettingsLink,
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
    ImageTypesToCreate.forEach((ImageTypeToCreate: FakerImageType, index: number) => {
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
