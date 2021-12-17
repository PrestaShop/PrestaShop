require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import data
const FileFaker = require('@data/faker/file');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const filesPage = require('@pages/BO/catalog/files');
const addFilePage = require('@pages/BO/catalog/files/add');

const baseContext = 'functional_BO_catalog_files_CRUDFile';

let browserContext;
let page;

const createFileData = new FileFaker();
const editFileData = new FileFaker();

/*
Create file
Check download of file
Update file
Delete file
 */
describe('BO - Catalog - Files : CRUD file', async () => {
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
    await expect(pageTitle).to.contains(filesPage.pageTitle);
  });

  describe('Create file', async () => {
    it('should go to new file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFilePage', baseContext);

      await filesPage.goToAddNewFilePage(page);
      const pageTitle = await addFilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addFilePage.pageTitle);
    });

    it('should create file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFile', baseContext);

      const result = await addFilePage.createEditFile(page, createFileData);
      await expect(result).to.equal(filesPage.successfulCreationMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedFile', baseContext);

      const filePath = await filesPage.viewFile(page, 1);

      const found = await files.doesFileExist(filePath);
      await expect(found, `${createFileData.filename} was not downloaded`).to.be.true;
    });
  });

  describe('Update file', async () => {
    it('should go to edit first file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditFilePage', baseContext);

      await filesPage.goToEditFilePage(page, 1);
      const pageTitle = await addFilePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addFilePage.pageTitleEdit);
    });

    it('should edit file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateFile', baseContext);

      const result = await addFilePage.createEditFile(page, editFileData);
      await expect(result).to.equal(filesPage.successfulUpdateMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedFile', baseContext);

      const filePath = await filesPage.viewFile(page, 1);

      const found = await files.doesFileExist(filePath);
      await expect(found, `${editFileData.filename} was not downloaded`).to.be.true;
    });
  });

  describe('Delete file', async () => {
    it('should delete file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);

      // delete file in first row
      const result = await filesPage.deleteFile(page, 1);
      await expect(result).to.be.equal(filesPage.successfulDeleteMessage);
    });
  });
});
