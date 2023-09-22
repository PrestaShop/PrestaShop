// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import merchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to enable merchandise returns
 * @param baseContext {string} String to identify the test
 */
function enableMerchandiseReturns(baseContext: string = 'commonTests-enableMerchandiseReturnsTest'): void {
  describe('PRE-TEST: Enable merchandise returns', async () => {
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

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );
      await merchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it('should enable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, true);
      expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });
}

/**
 * Function to disable merchandise returns
 * @param baseContext {string} String to identify the test
 */
function disableMerchandiseReturns(baseContext: string = 'commonTests-disableMerchandiseReturnsTest'): void {
  describe('POST-TEST: Disable merchandise returns', async () => {
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

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPageToDisable', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customerServiceParentLink,
        dashboardPage.merchandiseReturnsLink,
      );

      const pageTitle = await merchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(merchandiseReturnsPage.pageTitle);
    });

    it('should disable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReturns', baseContext);

      const result = await merchandiseReturnsPage.setOrderReturnStatus(page, false);
      expect(result).to.contains(merchandiseReturnsPage.successfulUpdateMessage);
    });
  });
}

export {enableMerchandiseReturns, disableMerchandiseReturns};
