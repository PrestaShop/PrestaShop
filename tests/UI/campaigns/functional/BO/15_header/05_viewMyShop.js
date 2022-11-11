require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/BO/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');

// Import FO pages
const foHomePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_header_viewMyShop';

let browserContext;
let page;

describe('BO - Header : View My Shop', async () => {
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
    await expect(numPages).to.be.eq(1);

    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });

  it('should click on View my shop', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickViewMyShop', baseContext);

    page = await dashboardPage.viewMyShop(page);

    const numPages = helper.getNumberTabs(browserContext);
    await expect(numPages).to.be.eq(2);

    const pageTitle = await foHomePage.getPageTitle(page);
    await expect(pageTitle).to.contains(foHomePage.pageTitle);
  });
});
