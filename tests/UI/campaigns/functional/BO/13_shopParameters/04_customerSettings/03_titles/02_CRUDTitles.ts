import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  boTitlesPage,
  boTitlesCreatePage,
  type BrowserContext,
  FakerTitle,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_CRUDTitles';

describe('BO - Shop Parameters - Customer Settings : Create, update and delete title in BO', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

  const createTitleData: FakerTitle = new FakerTitle();
  const editTitleData: FakerTitle = new FakerTitle();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    await Promise.all([
      utilsFile.generateImage(createTitleData.imageName),
      utilsFile.generateImage(editTitleData.imageName),
    ]);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await Promise.all([
      utilsFile.deleteFile(createTitleData.imageName),
      utilsFile.deleteFile(editTitleData.imageName),
    ]);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );
    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await boCustomerSettingsPage.goToTitlesPage(page);

    const pageTitle = await boTitlesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boTitlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await boTitlesPage.resetAndGetNumberOfLines(page);
    expect(numberOfTitles).to.be.above(0);
  });

  describe('Create title in BO', async () => {
    it('should go to add new title page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTitle', baseContext);

      await boTitlesPage.goToAddNewTitle(page);

      const pageTitle = await boTitlesCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boTitlesCreatePage.pageTitleCreate);
    });

    it('should create title and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTitle', baseContext);

      const textResult = await boTitlesCreatePage.createEditTitle(page, createTitleData);
      expect(textResult).to.contains(boTitlesPage.successfulCreationMessage);

      const numberOfTitlesAfterCreation = await boTitlesPage.getNumberOfElementInGrid(page);
      expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + 1);
    });
  });

  describe('Update title created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await boTitlesPage.resetFilter(page);
      await boTitlesPage.filterTitles(page, 'input', 'name', createTitleData.name);

      const textEmail = await boTitlesPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(createTitleData.name);
    });

    it('should go to edit title page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditTitlePage', baseContext);

      await boTitlesPage.gotoEditTitlePage(page, 1);

      const pageTitle = await boTitlesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boTitlesCreatePage.pageTitleEdit(createTitleData.name));
    });

    it('should update title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTitle', baseContext);

      const textResult = await boTitlesCreatePage.createEditTitle(page, editTitleData);
      expect(textResult).to.contains(boTitlesPage.successfulUpdateMessage);

      const numberOfTitlesAfterUpdate = await boTitlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterUpdate).to.be.equal(numberOfTitles + 1);
    });
  });

  describe('Delete title', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await boTitlesPage.resetFilter(page);
      await boTitlesPage.filterTitles(page, 'input', 'name', editTitleData.name);

      const textEmail = await boTitlesPage.getTextColumn(page, 1, 'name');
      expect(textEmail).to.contains(editTitleData.name);
    });

    it('should delete title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTitle', baseContext);

      const textResult = await boTitlesPage.deleteTitle(page, 1);
      expect(textResult).to.contains(boTitlesPage.successfulDeleteMessage);

      const numberOfTitlesAfterDelete = await boTitlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterDelete).to.be.equal(numberOfTitles);
    });
  });
});
