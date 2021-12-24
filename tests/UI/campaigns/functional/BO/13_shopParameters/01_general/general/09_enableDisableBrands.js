require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const generalPage = require('@pages/BO/shopParameters/general');
const brandsPage = require('@pages/BO/catalog/brands');
const homePage = require('@pages/FO/home');
const siteMapPage = require('@pages/FO/siteMap');

const baseContext = 'functional_BO_shopParameters_general_general_enableDisableDisplayBrands';

let browserContext;
let page;

/*
Enable/Disable brands
Check the alert message from BO Brands page
Go to FO to check brands link in sitemap page
 */
describe('BO - Shop Parameters - General : Enable/Disable display brands', async () => {
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

  tests.forEach((test, index) => {
    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage_${index}`, baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.shopParametersParentLink,
        dashboardPage.shopParametersGeneralLink,
      );

      await generalPage.closeSfToolBar(page);

      const pageTitle = await generalPage.getPageTitle(page);
      await expect(pageTitle).to.contains(generalPage.pageTitle);
    });

    it(`should ${test.args.action} display brands`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplayBrands`, baseContext);

      const result = await generalPage.setDisplayBrands(page, test.args.exist);
      await expect(result).to.contains(generalPage.successfulUpdateMessage);
    });

    it('should go to \'Brands & Suppliers\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToBrandsPage_${index}`, baseContext);

      await generalPage.goToSubMenu(
        page,
        generalPage.catalogParentLink,
        generalPage.brandsAndSuppliersLink,
      );

      const pageTitle = await brandsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(brandsPage.pageTitle);
    });

    it(`should check that the message alert contains '${test.args.action}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAlertContains_${test.args.action}`, baseContext);

      const text = await brandsPage.getAlertInfoBlockParagraphContent(page);
      await expect(text).to.contains(test.args.action);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO_${test.args.action}`, baseContext);

      // View shop
      page = await brandsPage.viewMyShop(page);

      // Change FO language
      await homePage.changeLanguage(page, 'en');

      const isHomePage = await homePage.isHomePage(page);
      await expect(isHomePage).to.be.true;
    });

    it('should verify the existence of the brands page link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkBrandsPage_${test.args.action}`, baseContext);

      await homePage.goToFooterLink(page, 'Sitemap');
      const pageTitle = await siteMapPage.getPageTitle(page);
      await expect(pageTitle).to.equal(siteMapPage.pageTitle);

      const exist = await siteMapPage.isBrandsLinkVisible(page);
      await expect(exist).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${test.args.action}`, baseContext);

      page = await siteMapPage.closePage(browserContext, page, 0);

      const pageTitle = await brandsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(brandsPage.pageTitle);
    });
  });
});
