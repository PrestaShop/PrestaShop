import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boMerchandiseReturnsPage,
  type BrowserContext,
  type Page,
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
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
      );
      await boMerchandiseReturnsPage.closeSfToolBar(page);

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should enable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, true);
      expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customer Service > Merchandise Returns\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToMerchandiseReturnsPageToDisable', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customerServiceParentLink,
        boDashboardPage.merchandiseReturnsLink,
      );

      const pageTitle = await boMerchandiseReturnsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boMerchandiseReturnsPage.pageTitle);
    });

    it('should disable merchandise returns', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableReturns', baseContext);

      const result = await boMerchandiseReturnsPage.setOrderReturnStatus(page, false);
      expect(result).to.contains(boMerchandiseReturnsPage.successfulUpdateMessage);
    });
  });
}

export {enableMerchandiseReturns, disableMerchandiseReturns};
