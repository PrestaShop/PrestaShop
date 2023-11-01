// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import taxesPage from '@pages/BO/international/taxes';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to enable Ecotax
 * @param baseContext {string} String to identify the test
 */
function enableEcoTaxTest(baseContext: string = 'commonTests-enableEcoTaxTest'): void {
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
      expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should enable ecotax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, true);
      expect(textResult).to.be.equal('Update successful');
    });
  });
}

/**
 * Function to disable eco tax
 * @param baseContext {string} String to identify the test
 */
function disableEcoTaxTest(baseContext: string = 'commonTests-disableEcoTaxTest'): void {
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
      expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should disable Ecotax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, false);
      expect(textResult).to.be.equal('Update successful');
    });
  });
}

export {enableEcoTaxTest, disableEcoTaxTest};
