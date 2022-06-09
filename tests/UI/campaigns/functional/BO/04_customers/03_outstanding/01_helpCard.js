require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');

// Import common tests
const {enableB2BTest, disableB2BTest} = require('@commonTests/BO/shopParameters/enableDisableB2B');

// Import test context
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/BO/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const outstandingPage = require('@pages/BO/customers/outstanding');

const baseContext = 'functional_BO_customers_outstanding_helpCard';

let browserContext;
let page;

/*
Pre-condition:
- Enable B2B
Scenario:
- Click on Help card
Post-condition:
- Disable B2B
*/

describe('BO - Customers - Outstanding : Help card in outstanding page', async () => {
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
      await expect(pageTitle).to.contains(outstandingPage.pageTitle);
    });

    it('should open the help side bar and check the document language', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

      const isHelpSidebarVisible = await outstandingPage.openHelpSideBar(page);
      await expect(isHelpSidebarVisible).to.be.true;

      const documentURL = await outstandingPage.getHelpDocumentURL(page);
      await expect(documentURL).to.contains('country=en');
    });

    it('should close the help side bar', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

      const isHelpSidebarNotVisible = await outstandingPage.closeHelpSideBar(page);
      await expect(isHelpSidebarNotVisible).to.be.true;
    });
  });

  // Post-Condition : Disable B2B
  disableB2BTest(baseContext);
});
