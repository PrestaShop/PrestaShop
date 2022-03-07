require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const importPage = require('@pages/BO/advancedParameters/import');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to import file
 * @param fileName {string} File name to import
 * @param entityToImport {string} Value to import
 * @param baseContext {string} String to identify the test
 */
function importFileTest(fileName, entityToImport, baseContext = 'commonTests-importFileTest') {
  describe(`PRE-TEST: Import file '${fileName}'`, async () => {
    // before and after functions
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
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

    it(`should import '${fileName}' file`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'importFile', baseContext);

      const uploadSuccessText = await importPage.uploadFile(page, entityToImport, fileName);
      await expect(uploadSuccessText).contain(fileName);

      if (await importPage.isForceAllIDNumbersVisible(page)) {
        await importPage.setForceAllIDNumbers(page);
      }
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
}

module.exports = {importFileTest};
