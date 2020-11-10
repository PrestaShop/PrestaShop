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

// Import Data
const {SampleFiles} = require('@data/demo/sampleFiles');
const {ImportedCustomer} = require('@data/demo/customer');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_import_importFile';

// Variables
let browserContext;
let page;

// Variable Used in the download / rename / upload test
let sampleFilePath;

const renamedSampleFilePath = `./${SampleFiles.customers.name}.csv`;

/*
Go to the import page
Download the customers sample file
Import the customer sample file
Go to customers page
Check import success
 */
describe('Import customers', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile(renamedSampleFilePath);
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

  it(`should download ${SampleFiles.customers.name} sample file`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadFile', baseContext);

    sampleFilePath = await importPage.downloadSampleFile(page, SampleFiles.customers.name);

    const doesFileExist = await files.doesFileExist(sampleFilePath);
    await expect(doesFileExist, `${SampleFiles.customers.name} sample file was not downloaded`).to.be.true;
  });

  describe('Import file', async () => {
    it(`should upload ${SampleFiles.customers.name} sample text file`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      // Rename the file and add file extension to be able to upload it
      await files.renameFile(sampleFilePath, renamedSampleFilePath);

      const uploadSuccessText = await importPage.uploadSampleFile(
        page,
        SampleFiles.customers.value,
        renamedSampleFilePath,
      );
      await expect(uploadSuccessText).contain(SampleFiles.customers.name);
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

    it('should close import progress modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeImportModal', baseContext);

      const isModalClosed = await importPage.closeImportModal(page);
      await expect(isModalClosed).to.be.true;
    });
  });

  describe('Check import success', async () => {
    it('should go to customers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

      await importPage.goToSubMenu(
        page,
        importPage.customersParentLink,
        importPage.customersLink,
      );

      await customersPage.closeSfToolBar(page);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });

    it('should filter list by email', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToCheckImportedCustomer', baseContext);

      await customersPage.resetFilter(page);

      await customersPage.filterCustomers(
        page,
        'input',
        'email',
        ImportedCustomer.email,
      );

      const textEmail = await customersPage.getTextColumnFromTableCustomers(page, 1, 'email');
      await expect(textEmail).to.contains(ImportedCustomer.email);
    });

    it('should check import success', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImportSuccess', baseContext);

      const customersEmailList = await customersPage.getAllRowsColumnContent(page, 'email');
      await expect(customersEmailList).contain(ImportedCustomer.email);
    });
  });
});
