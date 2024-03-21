// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import customerSettingsPage from '@pages/BO/shopParameters/customerSettings';
import titlesPage from '@pages/BO/shopParameters/customerSettings/titles';
import addTitlePage from '@pages/BO/shopParameters/customerSettings/titles/add';

import {
  FakerTitle,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_CRUDTitles';

describe('BO - Shop Parameters - Customer Settings : Create, update and delete title in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

  const createTitleData: FakerTitle = new FakerTitle();
  const editTitleData: FakerTitle = new FakerTitle();

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    await Promise.all([
      files.generateImage(createTitleData.imageName),
      files.generateImage(editTitleData.imageName),
    ]);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    await Promise.all([
      files.deleteFile(createTitleData.imageName),
      files.deleteFile(editTitleData.imageName),
    ]);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.customerSettingsLink,
    );
    await customerSettingsPage.closeSfToolBar(page);

    const pageTitle = await customerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await customerSettingsPage.goToTitlesPage(page);

    const pageTitle = await titlesPage.getPageTitle(page);
    expect(pageTitle).to.contains(titlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await titlesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTitles).to.be.above(0);
  });

  describe('Create title in BO', async () => {
    it('should go to add new title page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTitle', baseContext);

      await titlesPage.goToAddNewTitle(page);

      const pageTitle = await addTitlePage.getPageTitle(page);
      expect(pageTitle).to.eq(addTitlePage.pageTitleCreate);
    });

    it('should create title and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTitle', baseContext);

      const textResult = await addTitlePage.createEditTitle(page, createTitleData);
      expect(textResult).to.contains(titlesPage.successfulCreationMessage);

      const numberOfTitlesAfterCreation = await titlesPage.getNumberOfElementInGrid(page);
      expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + 1);
    });
  });

  describe('Update title created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await titlesPage.resetFilter(page);
      await titlesPage.filterTitles(page, 'input', 'name', createTitleData.name);

      const textEmail = await titlesPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(createTitleData.name);
    });

    it('should go to edit title page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditTitlePage', baseContext);

      await titlesPage.gotoEditTitlePage(page, 1);

      const pageTitle = await addTitlePage.getPageTitle(page);
      expect(pageTitle).to.contains(addTitlePage.pageTitleEdit(createTitleData.name));
    });

    it('should update title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTitle', baseContext);

      const textResult = await addTitlePage.createEditTitle(page, editTitleData);
      expect(textResult).to.contains(titlesPage.successfulUpdateMessage);

      const numberOfTitlesAfterUpdate = await titlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterUpdate).to.be.equal(numberOfTitles + 1);
    });
  });

  describe('Delete title', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await titlesPage.resetFilter(page);
      await titlesPage.filterTitles(page, 'input', 'name', editTitleData.name);

      const textEmail = await titlesPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(editTitleData.name);
    });

    it('should delete title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTitle', baseContext);

      const textResult = await titlesPage.deleteTitle(page, 1);
      expect(textResult).to.contains(titlesPage.successfulDeleteMessage);

      const numberOfTitlesAfterDelete = await titlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterDelete).to.be.equal(numberOfTitles);
    });
  });
});
