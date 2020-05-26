require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');


// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_helperCard';

let browser;
let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
  };
};

describe('Helper card', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to Traffic & SEO page
  loginCommon.loginBO();

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.trafficAndSeoLink,
    );

    await this.pageObjects.seoAndUrlsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.seoAndUrlsPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await this.pageObjects.seoAndUrlsPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await this.pageObjects.seoAndUrlsPage.closeHelpSideBar();
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
