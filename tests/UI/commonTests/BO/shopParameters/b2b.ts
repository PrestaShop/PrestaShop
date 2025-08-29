// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to enable B2B mode
 * @param baseContext {string} String to identify the test
 */
function enableB2BTest(baseContext: string = 'commonTests-enableB2BTest'): void {
  describe('PRE-TEST: Enable B2B', async () => {
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

    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should enable B2B mode', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableB2BMode', baseContext);

      const result = await boCustomerSettingsPage.setOptionStatus(
        page,
        boCustomerSettingsPage.OPTION_B2B,
        true,
      );
      expect(result).to.contains(boCustomerSettingsPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to disable B2B mode
 * @param baseContext {string} String to identify the test
 */
function disableB2BTest(baseContext: string = 'commonTests-disableB2BTest'): void {
  describe('POST-TEST: Disable B2B', async () => {
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

    it('should go to \'Shop parameters > Customer Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.customerSettingsLink,
      );
      await boCustomerSettingsPage.closeSfToolBar(page);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });

    it('should disable B2B mode', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableB2BMode', baseContext);

      const result = await boCustomerSettingsPage.setOptionStatus(
        page,
        boCustomerSettingsPage.OPTION_B2B,
        false,
      );
      expect(result).to.contains(boCustomerSettingsPage.successfulUpdateMessage);
    });
  });
}

export {enableB2BTest, disableB2BTest};
