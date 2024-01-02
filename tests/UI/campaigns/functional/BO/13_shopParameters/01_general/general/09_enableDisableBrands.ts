// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import generalPage from '@pages/BO/shopParameters/general';
import brandsPage from '@pages/BO/catalog/brands';
import {homePage} from '@pages/FO/home';
import {siteMapPage} from '@pages/FO/siteMap';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_shopParameters_general_general_enableDisableBrands';

/*
Enable/Disable brands
Check the alert message from BO Brands page
Go to FO to check brands link in sitemap page
 */
describe('BO - Shop Parameters - General : Enable/Disable display brands', async () => {
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
    {args: {action: 'Disable', exist: false}},
    {args: {action: 'Enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} Display brands`, async () => {
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

      it(`should ${test.args.action} display brands`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayBrands`, baseContext);

        const result = await generalPage.setDisplayBrands(page, test.args.exist);
        expect(result).to.contains(generalPage.successfulUpdateMessage);
      });

      it('should go to \'Brands & Suppliers\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToBrandsPage_${index}`, baseContext);

        await generalPage.goToSubMenu(
          page,
          generalPage.catalogParentLink,
          generalPage.brandsAndSuppliersLink,
        );

        const pageTitle = await brandsPage.getPageTitle(page);
        expect(pageTitle).to.contains(brandsPage.pageTitle);
      });

      it(`should check that the message alert contains '${test.args.action}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkAlertContains_${test.args.action}`, baseContext);

        const text = await brandsPage.getAlertInfoBlockParagraphContent(page);
        expect(text).to.contains(test.args.action.toLowerCase());
      });

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO_${test.args.action}`, baseContext);

        // View shop
        page = await brandsPage.viewMyShop(page);

        // Change FO language
        await homePage.changeLanguage(page, 'en');

        const isHomePage = await homePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should verify the existence of the brands page link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBrandsPage_${test.args.action}`, baseContext);

        await homePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await siteMapPage.getPageTitle(page);
        expect(pageTitle).to.equal(siteMapPage.pageTitle);

        const exist = await siteMapPage.isBrandsLinkVisible(page);
        expect(exist).to.be.equal(test.args.exist);
      });

      it('should go back to BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${test.args.action}`, baseContext);

        page = await siteMapPage.closePage(browserContext, page, 0);

        const pageTitle = await brandsPage.getPageTitle(page);
        expect(pageTitle).to.contains(brandsPage.pageTitle);
      });
    });
  });
});
