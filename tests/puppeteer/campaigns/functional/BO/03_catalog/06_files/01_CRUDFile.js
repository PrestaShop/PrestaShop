require('module-alias/register');
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
const FileFaker = require('@data/faker/file');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const FilesPage = require('@pages/BO/catalog/files');
const AddFilePage = require('@pages/BO/catalog/files/add');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_files_CRUDFile';

let browser;
let page;
const createFileData = new FileFaker();
const editFileData = new FileFaker();

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    filesPage: new FilesPage(page),
    addFilePage: new AddFilePage(page),
  };
};

/*
Create file
Check download of file
Update file
Delete file
 */
describe('Create, update and delete file', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await helper.setDownloadBehavior(page);
    this.pageObjects = await init();
    await Promise.all([
      files.createFile('.', createFileData.filename, `test ${createFileData.filename}`),
      files.createFile('.', editFileData.filename, `test ${editFileData.filename}`),
    ]);
  });
  after(async () => {
    await helper.closeBrowser(browser);
    await Promise.all([
      files.deleteFile(createFileData.filename),
      files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${createFileData.filename}`),
      files.deleteFile(editFileData.filename),
      files.deleteFile(`${global.BO.DOWNLOAD_PATH}/${editFileData.filename}`),
    ]);
  });

  // Login into BO
  loginCommon.loginBO();

  // Go to files page
  it('should go to files page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.filesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.filesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.filesPage.pageTitle);
  });

  describe('Create file', async () => {
    it('should go to new file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewFilePage', baseContext);
      await this.pageObjects.filesPage.goToAddNewFilePage();
      const pageTitle = await this.pageObjects.addFilePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addFilePage.pageTitle);
    });

    it('should create file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createFile', baseContext);
      const result = await this.pageObjects.addFilePage.createEditFile(createFileData);
      await expect(result).to.equal(this.pageObjects.filesPage.successfulCreationMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewCreatedFile', baseContext);
      await this.pageObjects.filesPage.viewFile(1);
      const found = await files.doesFileExist(createFileData.filename);
      await expect(found, `${createFileData.filename} was not downloaded`).to.be.true;
    });
  });

  describe('Update File', async () => {
    it('should go to edit first file page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditFilePage', baseContext);
      await this.pageObjects.filesPage.goToEditFilePage(1);
      const pageTitle = await this.pageObjects.addFilePage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.addFilePage.pageTitleEdit);
    });

    it('should edit file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateFile', baseContext);
      const result = await this.pageObjects.addFilePage.createEditFile(editFileData);
      await expect(result).to.equal(this.pageObjects.filesPage.successfulUpdateMessage);
    });
  });

  describe('View file and check the existence of the downloaded file', async () => {
    it('should view file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'viewUpdatedFile', baseContext);
      await this.pageObjects.filesPage.viewFile(1);
      const found = await files.doesFileExist(editFileData.filename);
      await expect(found, `${editFileData.filename} was not downloaded`).to.be.true;
    });
  });

  describe('Delete file', async () => {
    it('should delete file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFile', baseContext);
      // delete file in first row
      const result = await this.pageObjects.filesPage.deleteFile(1);
      await expect(result).to.be.equal(this.pageObjects.filesPage.successfulDeleteMessage);
    });
  });
});
