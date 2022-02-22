require('module-alias/register');
// Import utils
const helper = require('@utils/helpers');

// Import login test
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');

// Import test context
const testContext = require('@utils/testContext');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

/**
 * Function to enable Ecotax
 * @param baseContext {string} String to identify the test
 */
function enableEcoTaxTest(baseContext = 'commonTests-enableEcoTaxTest') {
  describe('PRE-TEST: Enable Ecotax', async () => {
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

    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

      await taxesPage.closeSfToolBar(page);

      const pageTitle = await taxesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should enable ecotax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, true);
      await expect(textResult).to.be.equal('Update successful');
    });
  });
}

/**
 * Function to disable eco tax
 * @param baseContext {string} String to identify the test
 */
function disableEcoTaxTest(baseContext = 'commonTests-disableEcoTaxTest') {
  describe('POST-TEST: Disable Ecotax', async () => {
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

    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

      await taxesPage.closeSfToolBar(page);

      const pageTitle = await taxesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should disable Ecotax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, false);
      await expect(textResult).to.be.equal('Update successful');
    });
  });
}

module.exports = {enableEcoTaxTest, disableEcoTaxTest};
