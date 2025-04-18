import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boImageSettingsPage,
  boImageSettingsCreatePage,
  boLoginPage,
  type BrowserContext,
  FakerImageType,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const createImageTypeData: FakerImageType = new FakerImageType();
  const editImageTypeData: FakerImageType = new FakerImageType();

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

  // 1 - Create image type
  describe('Create image type in BO', async () => {
    it('should go to add new image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddImageTypePage', baseContext);

      await boImageSettingsPage.goToNewImageTypePage(page);

      const pageTitle = await boImageSettingsCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boImageSettingsCreatePage.pageTitleCreate);
    });

    it('should create image type and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createImageType', baseContext);

      const textResult = await boImageSettingsCreatePage.createEditImageType(page, createImageTypeData);
      expect(textResult).to.contains(boImageSettingsPage.successfulCreationMessage);

      const numberOfImageTypesAfterCreation = await boImageSettingsPage.getNumberOfElementInGrid(page);
      expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1);
    });
  });

  // 2 - Update image type
  describe('Update image type', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boImageSettingsPage.resetFilter(page);
      await boImageSettingsPage.filterTable(
        page,
        'input',
        'name',
        createImageTypeData.name,
      );

      const textEmail = await boImageSettingsPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(createImageTypeData.name);
    });

    it('should go to edit image type page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditImageTypePage', baseContext);

      await boImageSettingsPage.gotoEditImageTypePage(page, 1);

      const pageTitle = await boImageSettingsCreatePage.getPageTitle(page);
      expect(pageTitle).to.equal(boImageSettingsCreatePage.pageTitleEdit(createImageTypeData.name));
    });

    it('should update image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateImageType', baseContext);

      const textResult = await boImageSettingsCreatePage.createEditImageType(page, editImageTypeData);
      expect(textResult).to.contains(boImageSettingsPage.successfulUpdateMessage);

      const numberOfImageTypesAfterUpdate = await boImageSettingsPage.resetAndGetNumberOfLines(page);
      expect(numberOfImageTypesAfterUpdate).to.be.equal(numberOfImageTypes + 1);
    });
  });

  // 3 - Delete image type
  describe('Delete image type', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boImageSettingsPage.resetFilter(page);
      await boImageSettingsPage.filterTable(
        page,
        'input',
        'name',
        editImageTypeData.name,
      );

      const textEmail = await boImageSettingsPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(editImageTypeData.name);
    });

    it('should delete image type', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteImageType', baseContext);

      const textResult = await boImageSettingsPage.deleteImageType(page, 1);
      expect(textResult).to.contains(boImageSettingsPage.successfulDeleteMessage);

      const numberOfImageTypesAfterDelete = await boImageSettingsPage.resetAndGetNumberOfLines(page);
      expect(numberOfImageTypesAfterDelete).to.be.equal(numberOfImageTypes);
    });
  });
});
