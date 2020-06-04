require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SeoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const FOHomePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_enableDisableFriendlyUrl';

let browser;
let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    seoAndUrlsPage: new SeoAndUrlsPage(page),
    foHomePage: new FOHomePage(page),
  };
};

describe('Enable/Disable friendly URL', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Shop parameters > SEO and Urls\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.trafficAndSeoLink,
    );

    await this.pageObjects.seoAndUrlsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.seoAndUrlsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.seoAndUrlsPage.pageTitle);
  });

  it('should disable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFriendlyUrl', baseContext);

    const result = await this.pageObjects.seoAndUrlsPage.enableDisableFriendlyURL(false);
    await expect(result).to.contains(this.pageObjects.seoAndUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await this.pageObjects.seoAndUrlsPage.viewMyShop();
    this.pageObjects = await init();

    const url = await this.pageObjects.foHomePage.getCurrentURL();
    await expect(url).to.contains('index.php');

    // Go back to BO
    page = await this.pageObjects.foHomePage.closePage(browserContext, 0);
    this.pageObjects = await init();
  });

  it('should enable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFriendlyUrl', baseContext);

    const result = await this.pageObjects.seoAndUrlsPage.enableDisableFriendlyURL(true);
    await expect(result).to.contains(this.pageObjects.seoAndUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await this.pageObjects.seoAndUrlsPage.viewMyShop();
    this.pageObjects = await init();

    await this.pageObjects.foHomePage.changeLanguage('en');

    const url = await this.pageObjects.foHomePage.getCurrentURL();
    await expect(url).to.contains('/en/');

    // Go back to BO
    page = await this.pageObjects.foHomePage.closePage(browserContext, 0);
    this.pageObjects = await init();
  });
});
