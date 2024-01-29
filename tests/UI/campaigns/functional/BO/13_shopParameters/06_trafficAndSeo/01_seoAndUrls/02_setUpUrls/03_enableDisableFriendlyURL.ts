// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import seoAndUrlsPage from '@pages/BO/shopParameters/trafficAndSeo/seoAndUrls';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/classic/home';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_setUpUrls_enableDisableFriendlyURL';

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable friendly URL', async () => {
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

  it('should go to \'Shop Parameters > SEO and Urls\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.trafficAndSeoLink,
    );
    await seoAndUrlsPage.closeSfToolBar(page);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should disable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFriendlyUrl', baseContext);

    const result = await seoAndUrlsPage.enableDisableFriendlyURL(page, false);
    expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await seoAndUrlsPage.viewMyShop(page);

    const url = await foHomePage.getCurrentURL(page);
    expect(url).to.contains('index.php');
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

    // Go back to BO
    page = await foHomePage.closePage(browserContext, page, 0);

    const pageTitle = await seoAndUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  it('should enable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFriendlyUrl', baseContext);

    const result = await seoAndUrlsPage.enableDisableFriendlyURL(page, true);
    expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await seoAndUrlsPage.viewMyShop(page);
    await foHomePage.changeLanguage(page, 'en');

    const url = await foHomePage.getCurrentURL(page);
    expect(url).to.contains('/en/');
  });
});
