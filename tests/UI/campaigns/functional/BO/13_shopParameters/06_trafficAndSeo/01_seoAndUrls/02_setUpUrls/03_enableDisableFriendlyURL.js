require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const seoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');
const foHomePage = require('@pages/FO/home');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_enableDisableFriendlyUrl';

let browserContext;
let page;

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable friendly URL', async () => {
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

  it('should go to \'Shop Parameters > SEO and Urls\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.trafficAndSeoLink,
    );

    await seoAndUrlsPage.closeSfToolBar(page);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should disable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFriendlyUrl', baseContext);

    const result = await seoAndUrlsPage.enableDisableFriendlyURL(page, false);
    await expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await seoAndUrlsPage.viewMyShop(page);

    const url = await foHomePage.getCurrentURL(page);
    await expect(url).to.contains('index.php');
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

    // Go back to BO
    page = await foHomePage.closePage(browserContext, page, 0);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should enable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFriendlyUrl', baseContext);

    const result = await seoAndUrlsPage.enableDisableFriendlyURL(page, true);
    await expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await seoAndUrlsPage.viewMyShop(page);

    await foHomePage.changeLanguage(page, 'en');

    const url = await foHomePage.getCurrentURL(page);
    await expect(url).to.contains('/en/');
  });
});
