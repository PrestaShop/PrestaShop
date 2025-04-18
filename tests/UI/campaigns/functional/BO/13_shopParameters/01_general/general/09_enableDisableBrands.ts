import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  boShopParametersPage,
  type BrowserContext,
  foClassicHomePage,
  foClassicSitemapPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    {args: {action: 'Disable', exist: false}},
    {args: {action: 'Enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    describe(`${test.args.action} Display brands`, async () => {
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

      it(`should ${test.args.action} display brands`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayBrands`, baseContext);

        const result = await boShopParametersPage.setDisplayBrands(page, test.args.exist);
        expect(result).to.contains(boShopParametersPage.successfulUpdateMessage);
      });

      if (test.args.action === 'Disable') {
        it('should go to \'Brands & Suppliers\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goToBrandsPage_${index}`, baseContext);

          await boShopParametersPage.goToSubMenu(
            page,
            boShopParametersPage.catalogParentLink,
            boShopParametersPage.brandsAndSuppliersLink,
          );

          const pageTitle = await boBrandsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boBrandsPage.pageTitle);
        });

        it(`should check that the message alert contains '${test.args.action}'`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkAlertContains_${test.args.action}`, baseContext);

          const text = await boBrandsPage.getAlertInfoBlockParagraphContent(page);
          expect(text).to.contains(test.args.action.toLowerCase());
        });
      }

      it('should go to FO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToFO_${test.args.action}`, baseContext);

        // View shop
        page = await boBrandsPage.viewMyShop(page);

        // Change FO language
        await foClassicHomePage.changeLanguage(page, 'en');

        const isHomePage = await foClassicHomePage.isHomePage(page);
        expect(isHomePage).to.eq(true);
      });

      it('should verify the existence of the brands page link', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkBrandsPage_${test.args.action}`, baseContext);

        await foClassicHomePage.goToFooterLink(page, 'Sitemap');

        const pageTitle = await foClassicSitemapPage.getPageTitle(page);
        expect(pageTitle).to.equal(foClassicSitemapPage.pageTitle);

        const exist = await foClassicSitemapPage.isBrandsLinkVisible(page);
        expect(exist).to.be.equal(test.args.exist);
      });

      if (test.args.action === 'Disable') {
        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${test.args.action}`, baseContext);

          page = await foClassicSitemapPage.closePage(browserContext, page, 0);

          const pageTitle = await boBrandsPage.getPageTitle(page);
          expect(pageTitle).to.contains(boBrandsPage.pageTitle);
        });
      }
    });
  });
});
