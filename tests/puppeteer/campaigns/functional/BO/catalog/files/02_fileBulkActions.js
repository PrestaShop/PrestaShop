require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const FilesPage = require('@pages/BO/catalog/files');
const AddFilePage = require('@pages/BO/catalog/files/add');
// Importing data
const FileFaker = require('@data/faker/file');

let browser;
let page;
let numberOfFiles = 0;
const firstFileData = new FileFaker({name: 'todelete'});
const secondFileData = new FileFaker({name: 'todelete'});

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

// Create Files and Delete with Bulk actions
describe('Create Files and Delete with Bulk actions', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    await Promise.all([
      files.createFile('.', firstFileData.filename, `test ${firstFileData.filename}`),
      files.createFile('.', secondFileData.filename, `test ${secondFileData.filename}`),
    ]);
  });
  after(async () => {
    await helper.closeBrowser(browser);
    /* Delete the generated files */
    await Promise.all([
      files.deleteFile(firstFileData.filename),
      files.deleteFile(secondFileData.filename),
    ]);
  });
  // Login into BO and go to files page
  loginCommon.loginBO();

  it('should go to \'Catalog>Files\' page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.filesLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.filesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.filesPage.pageTitle);
  });

  it('should reset all filters', async function () {
    numberOfFiles = await this.pageObjects.filesPage.resetAndGetNumberOfLines();
    if (numberOfFiles === 0) {
      await expect(numberOfFiles).to.be.equal(0);
    }
    if (numberOfFiles !== 0) {
      await expect(numberOfFiles).to.be.above(0);
    }
  });
  // 1 : Create 2 files In BO
  describe('Create 2 files in BO', async () => {
    const tests = [
      {args: {fileToCreate: firstFileData}},
      {args: {fileToCreate: secondFileData}},
    ];

    tests.forEach((test, index) => {
      it('should go to add new file page', async function () {
        await this.pageObjects.filesPage.goToAddNewFilePage();
        const pageTitle = await this.pageObjects.addFilePage.getPageTitle();
        await expect(pageTitle).to.contains(this.pageObjects.addFilePage.pageTitle);
      });

      it('should create file and check result', async function () {
        const textResult = await this.pageObjects.addFilePage.createEditFile(test.args.fileToCreate);
        await expect(textResult).to.equal(this.pageObjects.filesPage.successfulCreationMessage);
        const numberOfFilesAfterCreation = await this.pageObjects.filesPage.getNumberOfElementInGrid();
        await expect(numberOfFilesAfterCreation).to.be.equal(numberOfFiles + index + 1);
      });
    });
  });

  // 2 : Delete Files created with bulk actions
  describe('Delete files with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await this.pageObjects.filesPage.filterTable(
        'name',
        'todelete',
      );
      const numberOfFilesAfterFilter = await this.pageObjects.filesPage.getNumberOfElementInGrid();
      await expect(numberOfFilesAfterFilter).to.be.equal(2);
      for (let i = 1; i <= numberOfFilesAfterFilter; i++) {
        const textColumn = await this.pageObjects.filesPage.getTextColumnFromTable(
          i,
          'name',
        );
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete files with Bulk Actions and check Result', async function () {
      const deleteTextResult = await this.pageObjects.filesPage.deleteFilesBulkActions();
      // Should edit successful delete message after the fix of this issue
      // https://github.com/PrestaShop/PrestaShop/issues/16921
      await expect(deleteTextResult).to.be.equal(this.pageObjects.filesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      const numberOfFilesAfterReset = await this.pageObjects.filesPage.resetAndGetNumberOfLines();
      await expect(numberOfFilesAfterReset).to.be.equal(numberOfFiles);
    });
  });
});
