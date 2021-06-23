require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const files = require('@utils/files');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const importPage = require('@pages/BO/advancedParameters/import');
const attributesPage = require('@pages/BO/catalog/attributes');

// Import Data
const {SampleFiles} = require('@data/demo/sampleFiles');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_import_importFile';

// Variables
let browserContext;
let page;

// Variable Used in the download / rename / upload test
let sampleFilePath;
const renamedSampleFilePath = `./${SampleFiles.combinations.name}.csv`;

// Variable used in the "check import success" test
const initialNumberOfAttributes = 4;
let numberOfAttributes = 0;

/*
Go to the import page
Download the combinations sample file
Import the combinations sample file
Go to attributes and features page
Check import success
 */
describe('Import combinations', async () => {
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

  it(`should download ${SampleFiles.combinations.name} sample file`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'downloadFile', baseContext);

    sampleFilePath = await importPage.downloadSampleFile(page, SampleFiles.combinations.name);

    const doesFileExist = await files.doesFileExist(sampleFilePath);
    await expect(doesFileExist, `${SampleFiles.combinations.name} sample file was not downloaded`).to.be.true;
  });

  describe('Import file', async () => {
    it(`should upload ${SampleFiles.combinations.name} sample text file`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      // Rename the file and add file extension to be able to upload it
      await files.renameFile(sampleFilePath, renamedSampleFilePath);

      const uploadSuccessText = await importPage.uploadSampleFile(
        page,
        SampleFiles.combinations.value,
        renamedSampleFilePath,
      );
      await expect(uploadSuccessText).contain(SampleFiles.combinations.name);
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
    it('should go to attributes and features page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCombinationsPage', baseContext);

      await importPage.goToSubMenu(
        page,
        importPage.catalogParentLink,
        importPage.attributesAndFeaturesLink,
      );

      await attributesPage.closeSfToolBar(page);

      const pageTitle = await attributesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(attributesPage.pageTitle);
    });

    it('should reset all filters and get number of attributes in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

      numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfAttributes).to.be.above(initialNumberOfAttributes);
    });
  });
});
