import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boFilesPage,
  boFilesCreatePage,
  boLoginPage,
  type BrowserContext,
  FakerFile,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const createFileData: FakerFile = new FakerFile();
  const editFileData: FakerFile = new FakerFile();

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await Promise.all([
      utilsFile.createFile('.', createFileData.filename, `test ${createFileData.filename}`),
      utilsFile.createFile('.', editFileData.filename, `test ${editFileData.filename}`),
    ]);
  });

  after(async () => {
    await Promise.all([
      utilsFile.deleteFile(createFileData.filename),
      utilsFile.deleteFile(editFileData.filename),
    ]);

    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // Go to files page
  it('should go to \'Catalog > Files\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.filesLink,
    );
    await boFilesPage.closeSfToolBar(page);

    const pageTitle = await boFilesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boFilesPage.pageTitle);
  });

  describe('Create file', async () => {
    it('should go to new file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFilePage', baseContext);

      await boFilesPage.goToAddNewFilePage(page);

      const pageTitle = await boFilesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boFilesCreatePage.pageTitle);
    });

    it('should create file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFile', baseContext);

      const result = await boFilesCreatePage.createEditFile(page, createFileData);
      expect(result).to.equal(boFilesPage.successfulCreationMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedFile', baseContext);

      const filePath = await boFilesPage.viewFile(page, 1);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found, `${createFileData.filename} was not downloaded`).to.eq(true);
    });
  });

  describe('Update file', async () => {
    it('should go to edit first file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditFilePage', baseContext);

      await boFilesPage.goToEditFilePage(page, 1);

      const pageTitle = await boFilesCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boFilesCreatePage.pageTitleEdit);
    });

    it('should edit file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateFile', baseContext);

      const result = await boFilesCreatePage.createEditFile(page, editFileData);
      expect(result).to.equal(boFilesPage.successfulUpdateMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedFile', baseContext);

      const filePath = await boFilesPage.viewFile(page, 1);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found, `${editFileData.filename} was not downloaded`).to.eq(true);
    });
  });

  describe('Delete file', async () => {
    it('should delete file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);

      // delete file in first row
      const result = await boFilesPage.deleteFile(page, 1);
      expect(result).to.be.equal(boFilesPage.successfulDeleteMessage);
    });
  });
});
