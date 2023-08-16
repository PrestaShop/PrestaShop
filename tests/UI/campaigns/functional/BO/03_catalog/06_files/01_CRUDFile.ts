// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import filesPage from '@pages/BO/catalog/files';
import addFilePage from '@pages/BO/catalog/files/add';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import FileData from '@data/faker/file';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_files_CRUDFile';

/*
Create file
Check download of file
Update file
Delete file
 */
describe('BO - Catalog - Files : CRUD file', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const createFileData: FileData = new FileData();
  const editFileData: FileData = new FileData();

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    await Promise.all([
      files.createFile('.', createFileData.filename, `test ${createFileData.filename}`),
      files.createFile('.', editFileData.filename, `test ${editFileData.filename}`),
    ]);
  });

  after(async () => {
    await Promise.all([
      files.deleteFile(createFileData.filename),
      files.deleteFile(editFileData.filename),
    ]);

    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  // Go to files page
  it('should go to \'Catalog > Files\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.filesLink,
    );
    await filesPage.closeSfToolBar(page);

    const pageTitle = await filesPage.getPageTitle(page);
    expect(pageTitle).to.contains(filesPage.pageTitle);
  });

  describe('Create file', async () => {
    it('should go to new file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFilePage', baseContext);

      await filesPage.goToAddNewFilePage(page);

      const pageTitle = await addFilePage.getPageTitle(page);
      expect(pageTitle).to.contains(addFilePage.pageTitle);
    });

    it('should create file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFile', baseContext);

      const result = await addFilePage.createEditFile(page, createFileData);
      expect(result).to.equal(filesPage.successfulCreationMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedFile', baseContext);

      const filePath = await filesPage.viewFile(page, 1);

      const found = await files.doesFileExist(filePath);
      expect(found, `${createFileData.filename} was not downloaded`).to.eq(true);
    });
  });

  describe('Update file', async () => {
    it('should go to edit first file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditFilePage', baseContext);

      await filesPage.goToEditFilePage(page, 1);

      const pageTitle = await addFilePage.getPageTitle(page);
      expect(pageTitle).to.contains(addFilePage.pageTitleEdit);
    });

    it('should edit file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateFile', baseContext);

      const result = await addFilePage.createEditFile(page, editFileData);
      expect(result).to.equal(filesPage.successfulUpdateMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedFile', baseContext);

      const filePath = await filesPage.viewFile(page, 1);

      const found = await files.doesFileExist(filePath);
      expect(found, `${editFileData.filename} was not downloaded`).to.eq(true);
    });
  });

  describe('Delete file', async () => {
    it('should delete file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);

      // delete file in first row
      const result = await filesPage.deleteFile(page, 1);
      expect(result).to.be.equal(filesPage.successfulDeleteMessage);
    });
  });
});
