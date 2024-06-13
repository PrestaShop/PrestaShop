// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';

// Import pages
import outstandingPage from '@pages/BO/customers/outstanding';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Click on the help card
  describe('Help card in outstanding page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.outstandingLink,
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
