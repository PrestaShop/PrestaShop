// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import generalPage from '@pages/BO/shopParameters/general';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
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
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Shop Parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.shopParametersParentLink,
        boDashboardPage.shopParametersGeneralLink,
      );
      await generalPage.closeSfToolBar(page);

      const pageTitle = await generalPage.getPageTitle(page);
      expect(pageTitle).to.contains(generalPage.pageTitle);
    });

    it(`should ${status ? 'enable' : 'disable'} the multistore`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setMultiStoreStatus', baseContext);

      const result = await generalPage.setMultiStoreStatus(page, status);
      expect(result).to.contains(generalPage.successfulUpdateMessage);
    });
  });
}

export default setMultiStoreStatus;
