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
import {productPage as foProductPage} from '@pages/FO/classic/product';

// Import data
import Attributes from '@data/demo/attributes';
import Products from '@data/demo/products';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoOptions_'
  + 'displayAttributesInProductMetaTitle';

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable display attributes in product meta title', async () => {
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

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
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

  const tests = [
    {
      args: {
        action: 'enable',
        enable: true,
        metaTitle: `${Products.demo_1.name} ${Attributes.size.name} ${Attributes.size.values[0].value}`
          + ` ${Attributes.color.name} ${Attributes.color.values[3].value}`,
      },
    },
    {
      args: {
        action: 'disable',
        enable: false,
        metaTitle: Products.demo_1.name,
      },
    },
  ];
  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} display attributes in product meta title`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayAttributes`, baseContext);

      const result = await seoAndUrlsPage.setStatusAttributesInProductMetaTitle(page, test.args.enable);
      expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO_${index}`, baseContext);

      // Go to FO
      page = await seoAndUrlsPage.viewMyShop(page);
      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page and check the title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkTitle_${index}`, baseContext);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await foProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(test.args.metaTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${index}`, baseContext);

      // Close page and init page objects
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await seoAndUrlsPage.getPageTitle(page);
      expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
    });
  });
});
