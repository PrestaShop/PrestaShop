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

const baseContext: string = 'functional_BO_shopParameters_customerSettings_titles_bulkActions';

describe('BO - Shop Parameters - Customer Settings : Bulk actions', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfTitles: number = 0;

  const titlesToCreate: FakerTitle[] = [
    new FakerTitle({name: 'todelete1'}),
    new FakerTitle({name: 'todelete2'}),
  ];

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    // Create images
    titlesToCreate.forEach((titleToCreate: FakerTitle) => utilsFile.generateImage(titleToCreate.imageName));
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    titlesToCreate.forEach((titleToCreate: FakerTitle) => utilsFile.deleteFile(titleToCreate.imageName));
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

  describe('Create 2 titles in BO', async () => {
    titlesToCreate.forEach((titleToCreate: FakerTitle, index: number) => {
      it('should go to add new title page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewTitlePage${index + 1}`, baseContext);

        await boTitlesPage.goToAddNewTitle(page);

        const pageTitle = await boTitlesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boTitlesCreatePage.pageTitleCreate);
      });

      it('should create title and check result', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `CreateTitle${index + 1}`, baseContext);

        const textResult = await boTitlesCreatePage.createEditTitle(page, titleToCreate);
        expect(textResult).to.contains(boTitlesPage.successfulCreationMessage);

        const numberOfTitlesAfterCreation = await boTitlesPage.getNumberOfElementInGrid(page);
        expect(numberOfTitlesAfterCreation).to.be.equal(numberOfTitles + index + 1);
      });
    });
  });

  describe('Delete titles with Bulk Actions', async () => {
    it('should filter list by title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boTitlesPage.filterTitles(page, 'input', 'name', 'todelete');

      const numberOfTitlesAfterFilter = await boTitlesPage.getNumberOfElementInGrid(page);
      expect(numberOfTitlesAfterFilter).to.be.at.most(numberOfTitles);

      for (let i = 1; i <= numberOfTitlesAfterFilter; i++) {
        const textColumn = await boTitlesPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete titles with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteTitles', baseContext);

      const deleteTextResult = await boTitlesPage.bulkDeleteTitles(page);
      expect(deleteTextResult).to.be.contains(boTitlesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfTitlesAfterReset = await boTitlesPage.resetAndGetNumberOfLines(page);
      expect(numberOfTitlesAfterReset).to.be.equal(numberOfTitles);
    });
  });
});
