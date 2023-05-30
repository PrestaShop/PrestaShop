// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import importPage from '@pages/BO/advancedParameters/import';
import categoriesPage from '@pages/BO/catalog/categories';

// Import data
import ImportCategories from '@data/import/categories';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_import_importFile';

/*
Pre-condition:
- Get categories number
Scenario
- Import categories csv file
- Check validation progress message
- Go to categories page to check number of categories
Post-Condition:
- delete imported categories
 */
describe('BO - Advanced Parameters - Import : Import categories', async () => {
  // Variable Used to create csv file
  const fileName: string = 'categories.csv';

  let browserContext: BrowserContext;
  let page: Page;
  // Variable used in the "check import success" test
  let numberOfCategories: number = 0;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    // Create csv file with all data
    await files.createCSVFile('.', fileName, ImportCategories);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    // delete file
    await files.deleteFile(fileName);
  });

  // Pre-condition: Get number of categories
  describe('PRE-TEST: Get number of categories', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.categoriesLink,
      );

      await categoriesPage.closeSfToolBar(page);

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should reset all filters and get number of Categories in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFirst', baseContext);

      numberOfCategories = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategories).to.be.above(0);
    });
  });

  // 1 - Import categories.csv
  describe('Import file', async () => {
    it('should go to \'Advanced Parameters > Import\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToImportPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.importLink,
      );
      await importPage.closeSfToolBar(page);

      const pageTitle = await importPage.getPageTitle(page);
      await expect(pageTitle).to.contains(importPage.pageTitle);
    });

    it(`should upload '${fileName}' file`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      const uploadSuccessText = await importPage.uploadImportFile(page, 'Categories', fileName);
      await expect(uploadSuccessText).contain(fileName);
    });

    it('should go to next import file step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'nextStep', baseContext);

      const panelTitle = await importPage.goToImportNextStep(page);
      await expect(panelTitle).contain(importPage.importPanelTitle);
    });

    it('should start import file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmImport', baseContext);

      const modalTitle = await importPage.startFileImport(page);
      await expect(modalTitle).contain(importPage.importModalTitle);
    });

    it('should check that the import is completed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'waitForImport', baseContext);

      const isCompleted = await importPage.getImportValidationMessage(page);
      await expect(isCompleted, 'The import is not completed!')
        .contain('Data imported')
        .and.contain('Look at your listings to make sure it\'s all there as you wished.');
    });

    it('should close import progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeImportModal', baseContext);

      const isModalClosed = await importPage.closeImportModal(page);
      await expect(isModalClosed).to.be.true;
    });
  });

  // 2 - Check number of categories imported
  describe('Check number of categories', async () => {
    it('should go to \'Catalog > Categories\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPageToCheckImport', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.categoriesLink,
      );

      await categoriesPage.closeSfToolBar(page);

      const pageTitle = await categoriesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(categoriesPage.pageTitle);
    });

    it('should check number of categories in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetSecond', baseContext);

      const numberOfCategoriesAfterImport = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterImport).to.be.above(numberOfCategories);
    });
  });

  // Post-condition: Delete imported categories
  describe('POST-TEST: Delete imported categories', async () => {
    it('should filter list by Name \'category\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterCategoriesTable', baseContext);

      await categoriesPage.filterCategories(page, 'input', 'name', 'category');

      const textColumn = await categoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
      await expect(textColumn).to.contains('category');
    });

    it('should delete categories', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);

      const deleteTextResult = await categoriesPage.deleteCategoriesBulkActions(page);
      await expect(deleteTextResult).to.be.equal(categoriesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDelete', baseContext);

      const numberOfCategoriesAfterReset = await categoriesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCategoriesAfterReset).to.equal(numberOfCategories);
    });
  });
});
