require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingPage = require('@pages/BO/shopParameters/customerSettings');
const titlesPage = require('@pages/BO/shopParameters/customerSettings/titles');
const addTitlePage = require('@pages/BO/shopParameters/customerSettings/titles/add');

// Import data
const TitleFaker = require('@data/faker/title');

const baseContext = 'functional_BO_shopParameters_customerSettings_titles_CRUDTitles';

// Browser and tab
let browserContext;
let page;

let numberOfTitles = 0;

const createTitleData = new TitleFaker();
const editTitleData = new TitleFaker();

describe('BO - Shop Parameters - Customer Settings : Create, update and delete title in BO', async () => {
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

    await customerSettingPage.closeSfToolBar(page);

    const pageTitle = await customerSettingPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customerSettingPage.pageTitle);
  });

  it('should go to \'Titles\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToTitlesPage', baseContext);

    await customerSettingPage.goToTitlesPage(page);

    const pageTitle = await titlesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(titlesPage.pageTitle);
  });

  it('should reset all filters and get number of titles in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfTitles = await titlesPage.resetAndGetNumberOfLines(page);
    await expect(numberOfTitles).to.be.above(0);
  });

  describe('Create title in BO', async () => {
    it('should go to add new title page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTitle', baseContext);

      await titlesPage.goToAddNewTitle(page);
      const pageTitle = await addTitlePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addTitlePage.pageTitleCreate);
    });

    it('should create title and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createTitle', baseContext);

      const textResult = await addTitlePage.createEditTitle(page, createTitleData);
      await expect(textResult).to.contains(titlesPage.successfulCreationMessage);

      const numberOfTitlesAfterCreation = await titlesPage.getNumberOfElementInGrid(page);
      await expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + 1);
    });
  });

  describe('Update title created', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForUpdate', baseContext);

      await titlesPage.resetFilter(page);
      await titlesPage.filterTitles(page, 'input', 'b!name', createTitleData.name);

      const textEmail = await titlesPage.getTextColumn(page, 1, 'b!name');
      await expect(textEmail).to.contains(createTitleData.name);
    });

    it('should go to edit title page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditTitlePage', baseContext);

      await titlesPage.gotoEditTitlePage(page, 1);
      const pageTitle = await addTitlePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addTitlePage.pageTitleEdit);
    });

    it('should update title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateTitle', baseContext);

      const textResult = await addTitlePage.createEditTitle(page, editTitleData);
      await expect(textResult).to.contains(titlesPage.successfulUpdateMessage);

      const numberOfTitlesAfterUpdate = await titlesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTitlesAfterUpdate).to.be.equal(numberOfTitles + 1);
    });
  });

  describe('Delete title', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForDelete', baseContext);

      await titlesPage.resetFilter(page);
      await titlesPage.filterTitles(page, 'input', 'b!name', editTitleData.name);

      const textEmail = await titlesPage.getTextColumn(page, 1, 'b!name');
      await expect(textEmail).to.contains(editTitleData.name);
    });

    it('should delete title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteTitle', baseContext);

      const textResult = await titlesPage.deleteTitle(page, 1);
      await expect(textResult).to.contains(titlesPage.successfulDeleteMessage);

      const numberOfTitlesAfterDelete = await titlesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTitlesAfterDelete).to.be.equal(numberOfTitles);
    });
  });
});
