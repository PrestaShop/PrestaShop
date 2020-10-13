require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const importPage = require('@pages/BO/advancedParameters/import');
const customersPage = require('@pages/BO/customers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_import_importFile';

// Variables
let browserContext;
let page;

// Variable Used in the download / rename / upload test
let sampleFilePath;
const sampleFile = {
  args:
    {
      dropdownValue: 'Customers',
      type: 'customers_import',
    },
};
const newPath = `./${sampleFile.args.type}.csv`;

// Variables used for assertions
const importModalTitle = 'Importing your data...';
const importPanelTitle = 'Match your data';
const emailToCheck = 'Tiger.Lily@prestashop.com';

/*
Go to the import page
Download the customers sample file
Import the customer sample file
Go to customers page
Check import success
 */
describe('Import customers.csv file', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(newPath);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to import page', async function () {
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

  it(`should download ${sampleFile.args.type} sample file`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadFile', baseContext);

    sampleFilePath = await importPage.downloadSampleFile(page, sampleFile.args.type);

    const doesFileExist = await files.doesFileExist(sampleFilePath);
    await expect(doesFileExist, `${sampleFile.args.type} sample file was not downloaded`).to.be.true;
  });

  describe('Import file', async () => {
    it(`should upload ${sampleFile.args.type} sample text file`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      await files.renameFile(sampleFilePath, newPath);

      const uploadSuccessText = await importPage.uploadSampleFile(page, sampleFile.args.dropdownValue, newPath);
      await expect(uploadSuccessText).contain(sampleFile.args.type);
    });

    it('should go to next import file step', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'nextStep', baseContext);

      const panelTitle = await importPage.goToImportNextStep(page);
      await expect(panelTitle).contain(importPanelTitle);
    });

    it('should start import file', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'confirmImport', baseContext);

      const modalTitle = await importPage.startFileImport(page);
      await expect(modalTitle).contain(importModalTitle);
    });

    it('should close import progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeImportModal', baseContext);

      const fileTypeDropdownSelector = await importPage.closeImportModal(page);
      await expect(fileTypeDropdownSelector).to.be.true;
    });
  });

  describe('Check import success', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should check import success', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImportSuccess', baseContext);

      const customersEmailList = await customersPage.getAllRowsColumnContent(page, 'email');
      await expect(customersEmailList).contain(emailToCheck);
    });
  });
});
