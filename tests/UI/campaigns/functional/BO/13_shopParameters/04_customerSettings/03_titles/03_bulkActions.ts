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

// Import data
import TitleData from '@data/faker/title';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_bulkActions';

describe('BO - Shop Parameters - Customer Settings : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

  const titlesToCreate: TitleData[] = [
    new TitleData({name: 'todelete1'}),
    new TitleData({name: 'todelete2'}),
  ];

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    // Create images
    titlesToCreate.forEach((titleToCreate: TitleData) => files.generateImage(titleToCreate.imageName));
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);

    titlesToCreate.forEach((titleToCreate: TitleData) => files.deleteFile(titleToCreate.imageName));
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

  describe('Create 2 titles in BO', async () => {
    titlesToCreate.forEach((titleToCreate: TitleData, index: number) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTitlePage${index + 1}`, baseContext);

        await titlesPage.goToAddNewTitle(page);

        const pageTitle = await addTitlePage.getPageTitle(page);
        expect(pageTitle).to.contains(addTitlePage.pageTitleCreate);
      });

      it('should create title and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTitle${index + 1}`, baseContext);

        const textResult = await addTitlePage.createEditTitle(page, titleToCreate);
        expect(textResult).to.contains(titlesPage.successfulCreationMessage);

        const numberOfTitlesAfterCreation = await titlesPage.getNumberOfElementInGrid(page);
        expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + index + 1);
      });
    });
  });

  describe('Delete titles with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await titlesPage.filterTitles(page, 'input', 'name', 'todelete');

      const numberOfTitlesAfterFilter = await titlesPage.getNumberOfElementInGrid(page);
      expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

      for (let i = 1; i <= numberOfTitlesAfterFilter; i++) {
        const textColumn = await titlesPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete titles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTitles', baseContext);

      const deleteTextResult = await titlesPage.bulkDeleteTitles(page);
      expect(deleteTextResult).to.be.contains(titlesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfTitlesAfterReset = await titlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterReset).to.be.equal(numberOfTitles);
    });
  });
});
