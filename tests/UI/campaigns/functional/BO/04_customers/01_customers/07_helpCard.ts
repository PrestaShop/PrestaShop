// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_helpCard';

// Check that help card is in english in customers page
describe('BO - Customers - Customers : Help card on customers page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );
    await customersPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await customersPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await customersPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarNotVisible = await customersPage.closeHelpSideBar(page);
    expect(isHelpSidebarNotVisible).to.eq(true);
  });
});
