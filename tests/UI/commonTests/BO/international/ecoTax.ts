import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boTaxesPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.internationalParentLink, boDashboardPage.taxesLink);
      await boTaxesPage.closeSfToolBar(page);

      const pageTitle = await boTaxesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxesPage.pageTitle);
    });

    it('should enable ecotax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEcoTax', baseContext);

      const textResult = await boTaxesPage.enableEcoTax(page, true);
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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.internationalParentLink, boDashboardPage.taxesLink);
      await boTaxesPage.closeSfToolBar(page);

      const pageTitle = await boTaxesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boTaxesPage.pageTitle);
    });

    it('should disable Ecotax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableEcoTax', baseContext);

      const textResult = await boTaxesPage.enableEcoTax(page, false);
      expect(textResult).to.be.equal('Update successful');
    });
  });
}

export {enableEcoTaxTest, disableEcoTaxTest};
