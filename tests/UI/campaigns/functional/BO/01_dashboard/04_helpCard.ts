// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_dashboard_helpCard';

// Check help card language in dashboard page
describe('BO - dashboard : Help card in order page', async () => {
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

  it('should open the help side bar and check the document title', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await dashboardPage.openHelpCard(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await dashboardPage.getHelpDocumentTitle(page);
    expect(documentURL).to.contains('Discovering the Administration Area');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await dashboardPage.closeHelpCard(page);
    expect(isHelpSidebarClosed).to.eq(true);
  });
});
