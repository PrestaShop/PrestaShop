import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boShopParametersPage,
  type BrowserContext,
  foClassicHomePage,
  foClassicSitemapPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_general_general_enableDisableBestSellers';

/*
Enable/Disable suppliers
Go to FO to check best sellers link in sitemap page
 */
describe('BO - Shop Parameters - General : Enable/Disable display best sellers', async () => {
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

  const tests = [
    {args: {action: 'disable', exist: false}},
    {args: {action: 'enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} Display best sellers`, async () => {
      it('should go to \'Shop parameters > General\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage_${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shopParametersParentLink,
          boDashboardPage.shopParametersGeneralLink,
        );
        await boShopParametersPage.closeSfToolBar(page);

        const pageTitle = await boShopParametersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
      });

      it(`should ${test.args.action} display best sellers`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayBestSellers`, baseContext);

        const result = await boShopParametersPage.setDisplayBestSellers(page, test.args.exist);
        expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
      });

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO_${test.args.action}`, baseContext);

        // View shop
        page = await boShopParametersPage.viewMyShop(page);
        // Change shop language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to site map page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToBestSellersPage_${test.args.action}`, baseContext);

        await foClassicHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await foClassicSitemapPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);
      });

      it('should verify the existence of the best sellers page link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBestSellersPage_${test.args.action}`, baseContext);

        const exist = await foClassicSitemapPage.isBestSellersLinkVisible(page);
        expect(exist).to.equal(test.args.exist);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${test.args.action}`, baseContext);

        page = await foClassicSitemapPage.closePage(browserContext, page, 0);

        const pageTitle = await boShopParametersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boShopParametersPage.pageTitle);
      });
    });
  });
});
