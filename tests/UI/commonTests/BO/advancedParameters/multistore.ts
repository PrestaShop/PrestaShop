// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

let browserContext: BrowserContext;
let page: Page;

/**
 * Function to set multistore status
 * @param status {boolean} Status of the multistore
 * @param baseContext {string} String to identify the test
 */
function setMultiStoreStatus(status: boolean, baseContext: string = 'commonTests-setMultiStoreStatus'): void {
  describe(`${status ? 'Enable' : 'Disable'} the Webservice`, async () => {
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

    it('should go to \'Shop Parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.shopParametersGeneralLink,
      );
      await generalPage.closeSfToolBar(page);

      const pageTitle = await generalPage.getPageTitle(page);
      await expect(pageTitle).to.contains(generalPage.pageTitle);
    });

    it(`should ${status ? 'enable' : 'disable'} the webservice`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMultiStoreStatus', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, status);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });
}

export default setMultiStoreStatus;
