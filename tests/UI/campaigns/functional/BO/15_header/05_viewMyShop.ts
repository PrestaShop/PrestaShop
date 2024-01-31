// Import utils
import helper from '@utils/helpers';
import loginCommon from '@commonTests/BO/loginBO';
import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import test context
import testContext from '@utils/testContext';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import {homePage as foHomePage} from '@pages/FO/classic/home';

const baseContext: string = 'functional_BO_header_viewMyShop';

describe('BO - Header : View My Shop', async () => {
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

    const numPages = helper.getNumberTabs(browserContext);
    expect(numPages).to.be.eq(1);

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });

  it('should click on View my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickViewMyShop', baseContext);

    page = await dashboardPage.viewMyShop(page);

    const numPages = helper.getNumberTabs(browserContext);
    expect(numPages).to.be.eq(2);

    const pageTitle = await foHomePage.getPageTitle(page);
    expect(pageTitle).to.contains(foHomePage.pageTitle);
  });
});
