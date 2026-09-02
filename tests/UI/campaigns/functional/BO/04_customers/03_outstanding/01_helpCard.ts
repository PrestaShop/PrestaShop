import testContext from '@utils/testContext';
import {expect} from 'chai';

import {disableB2BTest, enableB2BTest} from '@commonTests/BO/shopParameters/b2b';

import {
  boDashboardPage,
  boLoginPage,
  boOutstandingPage,
  type BrowserContext,
  type Page,
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
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Customers > Outstanding\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOutstandingPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.customersParentLink,
        boDashboardPage.outstandingLink,
      );
      await boOutstandingPage.closeSfToolBar(page);

      const pageTitle = await boOutstandingPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOutstandingPage.pageTitle);
    });

    it('should open the help side bar and check the document language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

      const isHelpSidebarVisible = await boOutstandingPage.openHelpSideBar(page);
      expect(isHelpSidebarVisible).to.eq(true);

      const documentURL = await boOutstandingPage.getHelpDocumentURL(page);
      expect(documentURL).to.contains('country=en');
    });

    it('should close the help side bar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

      const isHelpSidebarNotVisible = await boOutstandingPage.closeHelpSideBar(page);
      expect(isHelpSidebarNotVisible).to.eq(true);
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
