require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');
const files = require('@utils/files');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingPage = require('@pages/BO/shopParameters/customerSettings');
const titlesPage = require('@pages/BO/shopParameters/customerSettings/titles');
const addTitlePage = require('@pages/BO/shopParameters/customerSettings/titles/add');

// Import data
const TitleFaker = require('@data/faker/title');

const baseContext = 'functional_BO_shopParameters_customerSettings_titles_bulkDeleteTitles';

// Browser and tab
let browserContext;
let page;

let numberOfTitles = 0;

const titlesToCreate = [
  new TitleFaker({name: 'todelete1'}),
  new TitleFaker({name: 'todelete2'}),
];

describe('BO - Shop Parameters - Customer Settings : Bulk delete files', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    titlesToCreate.forEach(titleToCreate => files.generateImage(titleToCreate.imageName));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    titlesToCreate.forEach(titleToCreate => files.deleteFile(titleToCreate.imageName));
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

  describe('Create 2 titles in BO', async () => {
    titlesToCreate.forEach((titleToCreate, index) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTitlePage${index + 1}`, baseContext);

        await titlesPage.goToAddNewTitle(page);
        const pageTitle = await addTitlePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addTitlePage.pageTitleCreate);
      });

      it('should create title and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTitle${index + 1}`, baseContext);

        const textResult = await addTitlePage.createEditTitle(page, titleToCreate);
        await expect(textResult).to.contains(titlesPage.successfulCreationMessage);

        const numberOfTitlesAfterCreation = await titlesPage.getNumberOfElementInGrid(page);
        await expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + index + 1);
      });
    });
  });

  describe('Delete titles with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await titlesPage.filterTitles(page, 'input', 'b!name', 'todelete');

      const numberOfTitlesAfterFilter = await titlesPage.getNumberOfElementInGrid(page);
      await expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

      for (let i = 1; i <= numberOfTitlesAfterFilter; i++) {
        const textColumn = await titlesPage.getTextColumn(page, i, 'b!name');
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete titles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTitles', baseContext);

      const deleteTextResult = await titlesPage.bulkDeleteTitles(page);
      await expect(deleteTextResult).to.be.contains(titlesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfTitlesAfterReset = await titlesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfTitlesAfterReset).to.be.equal(numberOfTitles);
    });
  });
});
