// Import utils
import loginCommon from '@commonTests/BO/loginBO';
import type {BrowserContext, Page} from 'playwright';
import {expect} from 'chai';

// Import test context
import testContext from '@utils/testContext';

// Import pages
import {homePage as foHomePage} from '@pages/FO/classic/home';
import {
  boDashboardPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_header_viewMyShop';

describe('BO - Header : View My Shop', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

    const numPages = utilsPlaywright.getNumberTabs(browserContext);
    expect(numPages).to.be.eq(1);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should click on View my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickViewMyShop', baseContext);

    page = await boDashboardPage.viewMyShop(page);

    const numPages = utilsPlaywright.getNumberTabs(browserContext);
    expect(numPages).to.be.eq(2);

    const pageTitle = await foHomePage.getPageTitle(page);
    expect(pageTitle).to.contains(foHomePage.pageTitle);
  });
});
