// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
// Import FO pages
import {homePage} from '@pages/FO/home';
import {siteMapPage} from '@pages/FO/siteMap';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  const tests = [
    {args: {action: 'disable', exist: false}},
    {args: {action: 'enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} Display best sellers`, async () => {
      it('should go to \'Shop parameters > General\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage_${index}`, baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.shopParametersParentLink,
          dashboardPage.shopParametersGeneralLink,
        );
        await generalPage.closeSfToolBar(page);

        const pageTitle = await generalPage.getPageTitle(page);
        expect(pageTitle).to.contains(generalPage.pageTitle);
      });

      it(`should ${test.args.action} display best sellers`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayBestSellers`, baseContext);

        const result = await generalPage.setDisplayBestSellers(page, test.args.exist);
        expect(result).to.contains(generalPage.successfulUpdateMessage);
      });

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO_${test.args.action}`, baseContext);

        // View shop
        page = await generalPage.viewMyShop(page);
        // Change shop language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should go to site map page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToBestSellersPage_${test.args.action}`, baseContext);

        await homePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        expect(pageTitle).to.equal(siteMapPage.pageTitle);
      });

      it('should verify the existence of the best sellers page link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBestSellersPage_${test.args.action}`, baseContext);

        const exist = await siteMapPage.isBestSellersLinkVisible(page);
        expect(exist).to.equal(test.args.exist);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${test.args.action}`, baseContext);

        page = await siteMapPage.closePage(browserContext, page, 0);

        const pageTitle = await generalPage.getPageTitle(page);
        expect(pageTitle).to.contains(generalPage.pageTitle);
      });
    });
  });
});
