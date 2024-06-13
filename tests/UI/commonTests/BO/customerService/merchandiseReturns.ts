// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import merchandiseReturnsPage from '@pages/BO/customerService/merchandiseReturns';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
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
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPageToDisable', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
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
