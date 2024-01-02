// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import outstandingPage from '@pages/BO/customers/outstanding';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_outstanding_helpCard';

/*
Pre-condition:
- Enable B2B
Scenario:
- Click on Help card
Post-condition:
- Disable B2B
*/

describe('BO - Customers - Outstanding : Help card in outstanding page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // Pre-Condition : Enable B2B
  enableB2BTest(baseContext);

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Click on the help card
  describe('Help card in outstanding page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.customersParentLink,
        dashboardPage.outstandingLink,
      );
      await outstandingPage.closeSfToolBar(page);

      const pageTitle = await outstandingPage.getPageTitle(page);
      expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });

    it('should open the help side bar and check the document language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

      const isHelpSidebarVisible = await outstandingPage.openHelpSideBar(page);
      expect(isHelpSidebarVisible).to.eq(true);

      const documentURL = await outstandingPage.getHelpDocumentURL(page);
      expect(documentURL).to.contains('country=en');
    });

    it('should close the help side bar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

      const isHelpSidebarNotVisible = await outstandingPage.closeHelpSideBar(page);
      expect(isHelpSidebarNotVisible).to.eq(true);
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
