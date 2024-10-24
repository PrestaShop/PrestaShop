// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boShopParametersPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to set multistore status
 * @param status {boolean} Status of the multistore
 * @param baseContext {string} String to identify the test
 */
function setMultiStoreStatus(status: boolean, baseContext: string = 'commonTests-setMultiStoreStatus'): void {
  describe(`${status ? 'Enable' : 'Disable'} the multistore`, async () => {
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

    it('should go to \'Shop Parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await boShopParametersPage.closeSfToolBar(page);

      const pageTitle = await boShopParametersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
    });

    it(`should ${status ? 'enable' : 'disable'} the multistore`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMultiStoreStatus', baseContext);

      const result = await boShopParametersPage.setMultiStoreStatus(page, status);
      expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
    });
  });
}

export default setMultiStoreStatus;
