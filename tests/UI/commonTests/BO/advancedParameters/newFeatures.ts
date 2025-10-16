import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boFeatureFlagPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

let browserContext: BrowserContext;
let page: Page;

function setFeatureFlag(featureFlag: string, expectedStatus: boolean, baseContext: string = 'commonTests-setFeatureFlag'): void {
  let title: string;

  switch (featureFlag) {
    case boFeatureFlagPage.featureFlagAdminAPIMultistore:
      title = 'Admin API - Multistore';
      break;
    case boFeatureFlagPage.featureFlagAdminAPI:
      title = 'Authorization server';
      break;
    case boFeatureFlagPage.featureFlagImprovedShipment:
      title = 'Improved shipment';
      break;
    default:
      throw new Error(`The feature flag ${featureFlag} is not defined`);
  }

  describe(`${expectedStatus ? 'Enable' : 'Disable'} the feature flag "${title}"`, async () => {
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

    it('should go to \'Advanced Parameters > New & Experimental Features\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFeatureFlagPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.featureFlagLink,
      );
      await boFeatureFlagPage.closeSfToolBar(page);

      const pageTitle = await boFeatureFlagPage.getPageTitle(page);
      expect(pageTitle).to.contains(boFeatureFlagPage.pageTitle);
    });

    it(`should ${expectedStatus ? 'enable' : 'disable'} "${title}"`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setFeatureFlag', baseContext);

      const successMessage = await boFeatureFlagPage.setFeatureFlag(page, featureFlag, expectedStatus);
      expect(successMessage).to.be.contain(boFeatureFlagPage.successfulUpdateMessage);
    });
  });
}

export default setFeatureFlag;
