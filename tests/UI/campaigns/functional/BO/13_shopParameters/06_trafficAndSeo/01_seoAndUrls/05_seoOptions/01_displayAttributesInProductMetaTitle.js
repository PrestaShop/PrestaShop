require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const seoAndUrlsPage = require('@pages/BO/shopParameters/trafficAndSeo/seoAndUrls');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const productPage = require('@pages/FO/product');

// Import data
const {Products} = require('@data/demo/products');
const {Attributes} = require('@data/demo/attributes');

const baseContext = 'functional_BO_shopParameters_TrafficAndSeo_seoAndUrls_displayAttributesInProductMetaTitle';

let browserContext;
let page;

describe('Enable/Disable display attributes in product meta title', async () => {
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
    await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
  });

  const tests = [
    {
      args: {
        action: 'enable',
        enable: true,
        metaTitle: `${Products.demo_1.name} ${Attributes.size.name} ${Attributes.size.values.small.value}`
          + ` ${Attributes.color.name} ${Attributes.color.values.white.value}`,
      },
    },
    {args: {action: 'disable', enable: false, metaTitle: Products.demo_1.name}},
  ];
  tests.forEach((test, index) => {
    it(`should ${test.args.action} display attributes in product meta title`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayAttributes`, baseContext);

      const result = await seoAndUrlsPage.setStatusAttributesInProductMetaTitle(page, test.args.enable);
      await expect(result).to.contains(seoAndUrlsPage.successfulSettingsUpdateMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO_${index}`, baseContext);

      // Go to FO
      page = await seoAndUrlsPage.viewMyShop(page);

      // Change FO language
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to the first product page and check the title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkTitle_${index}`, baseContext);

      // Go to the first product page
      await foHomePage.goToProductPage(page, 1);

      const pageTitle = await productPage.getPageTitle(page);
      await expect(pageTitle).to.equal(test.args.metaTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${index}`, baseContext);

      // Close page and init page objects
      page = await foHomePage.closePage(browserContext, page, 0);

      const pageTitle = await seoAndUrlsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(seoAndUrlsPage.pageTitle);
    });
  });
});
