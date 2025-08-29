import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boLoginPage,
  boSeoUrlsPage,
  type BrowserContext,
  foClassicHomePage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_setUpUrls_enableDisableFriendlyURL';

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable friendly URL', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > SEO and Urls\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSeoAndUrlsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.trafficAndSeoLink,
    );
    await boSeoUrlsPage.closeSfToolBar(page);

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
  });

  it('should disable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableFriendlyUrl', baseContext);

    const result = await boSeoUrlsPage.enableDisableFriendlyURL(page, false);
    expect(result).to.contains(boSeoUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDisabledFriendlyUrlFO', baseContext);

    page = await boSeoUrlsPage.viewMyShop(page);

    const url = await foClassicHomePage.getCurrentURL(page);
    expect(url).to.contains('index.php');
  });

  it('should go back to BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goBackToBO', baseContext);

    page = await foClassicHomePage.closePage(browserContext, page, 0);

    const pageTitle = await boSeoUrlsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
  });

  it('should enable friendly URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableFriendlyUrl', baseContext);

    const result = await boSeoUrlsPage.enableDisableFriendlyURL(page, true);
    expect(result).to.contains(boSeoUrlsPage.successfulSettingsUpdateMessage);
  });

  it('should go to FO and check the URL', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkEnabledFriendlyUrlFO', baseContext);

    // Go to FO
    page = await boSeoUrlsPage.viewMyShop(page);
    await foClassicHomePage.changeLanguage(page, 'en');

    const url = await foClassicHomePage.getCurrentURL(page);
    expect(url).to.not.contains('index.php');
  });
});
