// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import importPage from '@pages/BO/advancedParameters/import';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_import_importFile';

describe('BO - Advanced Parameters - Import : Import file', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let filePath: string | null;
  let secondFilePath: string | null;
  const firstFile:string = 'alias.csv';
  const secondFile:string = 'suppliers.csv';

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    // Delete downloaded csv files
    await files.deleteFile(firstFile);
    await files.deleteFile(secondFile);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

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

  describe('Download then import alias simple file', async () => {
    it('should download \'Sample alias file\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadFile', baseContext);

      filePath = await importPage.downloadSampleFile(page, 'alias_import');

      const doesFileExist = await files.doesFileExist(filePath);
      await expect(doesFileExist, 'alias_import sample file was not downloaded').to.be.true;
    });

    it('should upload the file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      await files.renameFile(filePath, 'alias.csv');

      const uploadSuccessText = await importPage.uploadImportFile(page, 'Alias', firstFile);
      await expect(uploadSuccessText).contain(firstFile);
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

  describe('Check choose from history / FTP then import suppliers simple file', async () => {
    it('should download \'Sample suppliers file\' file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'downloadFile', baseContext);

      secondFilePath = await importPage.downloadSampleFile(page, 'suppliers_import');

      const doesFileExist = await files.doesFileExist(secondFilePath);
      await expect(doesFileExist, 'suppliers sample file was not downloaded').to.be.true;
    });

    it('should upload the file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      await files.renameFile(secondFilePath, 'suppliers.csv');

      const uploadSuccessText = await importPage.uploadImportFile(page, 'Suppliers', secondFile);
      await expect(uploadSuccessText).contain('suppliers.csv');
    });

    it('should click on the downloaded file and check the existence of the button choose from history', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnDownloadedFile', baseContext);

      await importPage.clickOnDownloadedFile(page);

      const isButtonVisible = await importPage.isChooseFromHistoryButtonVisible(page);
      await expect(isButtonVisible).to.be.true;
    });

    it('should click on \'Choose from history / FTP\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      const isFilesListTableVisible = await importPage.chooseFromHistoryFTP(page);
      await expect(isFilesListTableVisible).to.be.true;
    });

    it('should check the imported files list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImportedFilesList', baseContext);

      const importedFilesList = await importPage.getImportedFilesList(page);
      await expect(importedFilesList).to.contains(firstFile)
        .and.to.contains(secondFile);
    });

    it('should delete the first imported file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFirstFile', baseContext);

      // Delete file and check that choose from history button is visible
      const isButtonVisible = await importPage.deleteFile(page);
      await expect(isButtonVisible).to.be.true;
    });

    it('should use the second imported file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'useSecondFile', baseContext);

      await importPage.chooseFromHistoryFTP(page);

      const uploadSuccessText = await importPage.useFile(page, 1);
      await expect(uploadSuccessText).contain(secondFile);
    });

    it('should go to next import file step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'nextStep2', baseContext);

      await importPage.selectFileType(page, 'Suppliers');

      const panelTitle = await importPage.goToImportNextStep(page);
      await expect(panelTitle).contain(importPage.importPanelTitle);
    });

    it('should start import file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmImport2', baseContext);

      const modalTitle = await importPage.startFileImport(page);
      await expect(modalTitle).contain(importPage.importModalTitle);
    });

    it('should check that the import is completed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'waitForImport2', baseContext);

      const isCompleted = await importPage.getImportValidationMessage(page);
      await expect(isCompleted, 'The import is not completed!')
        .contain('Data imported')
        .and.contain('Look at your listings to make sure it\'s all there as you wished.');
    });

    it('should close import progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeImportModal2', baseContext);

      const isModalClosed = await importPage.closeImportModal(page);
      await expect(isModalClosed).to.be.true;
    });
  });
});
