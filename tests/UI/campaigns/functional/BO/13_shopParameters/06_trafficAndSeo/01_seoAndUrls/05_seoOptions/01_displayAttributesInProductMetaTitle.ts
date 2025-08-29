import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSeoUrlsPage,
  type BrowserContext,
  dataAttributes,
  dataProducts,
  foClassicHomePage,
  foClassicProductPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_trafficAndSeo_seoAndUrls_seoOptions_'
  + 'displayAttributesInProductMetaTitle';

describe('BO - Shop Parameters - Traffic & SEO : Enable/Disable display attributes in product meta title', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Shop Parameters > Traffic & SEO\' page', async function () {
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

  const tests = [
    {
      args: {
        action: 'enable',
        enable: true,
        metaTitle: `${dataProducts.demo_1.name} ${dataAttributes.size.name} ${dataAttributes.size.values[0].value}`
          + ` ${dataAttributes.color.name} ${dataAttributes.color.values[3].value}`,
      },
    },
    {
      args: {
        action: 'disable',
        enable: false,
        metaTitle: dataProducts.demo_1.name,
      },
    },
  ];
  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} display attributes in product meta title`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayAttributes`, baseContext);

      const result = await boSeoUrlsPage.setStatusAttributesInProductMetaTitle(page, test.args.enable);
      expect(result).to.contains(boSeoUrlsPage.successfulSettingsUpdateMessage);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO_${index}`, baseContext);

      // Go to FO
      page = await boSeoUrlsPage.viewMyShop(page);
      // Change FO language
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to the first product page and check the title', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkTitle_${index}`, baseContext);

      // Go to the first product page
      await foClassicHomePage.goToProductPage(page, 1);

      const pageTitle = await foClassicProductPage.getPageTitle(page);
      expect(pageTitle).to.equal(test.args.metaTitle);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${index}`, baseContext);

      // Close page and init page objects
      page = await foClassicHomePage.closePage(browserContext, page, 0);

      const pageTitle = await boSeoUrlsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boSeoUrlsPage.pageTitle);
    });
  });
});
