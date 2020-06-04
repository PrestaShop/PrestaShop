require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const GeneralPage = require('@pages/BO/shopParameters/general');
const BrandsPage = require('@pages/BO/catalog/brands');
const SuppliersPage = require('@pages/BO/catalog/suppliers');
const HomePage = require('@pages/FO/home');
const SiteMapPage = require('@pages/FO/siteMap');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_general_general_enableDisableDisplaySuppliers';

let browser;
let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    generalPage: new GeneralPage(page),
    brandsPage: new BrandsPage(page),
    suppliersPage: new SuppliersPage(page),
    homePage: new HomePage(page),
    siteMapPage: new SiteMapPage(page),
  };
};

describe('Enable display suppliers', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to general page
  loginCommon.loginBO();

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index) => {
    it('should go to \'Shop parameters > General\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToGeneralPage_${index}`, baseContext);

      await this.pageObjects.dashboardPage.goToSubMenu(
        this.pageObjects.dashboardPage.shopParametersParentLink,
        this.pageObjects.dashboardPage.shopParametersGeneralLink,
      );

      await this.pageObjects.generalPage.closeSfToolBar();

      const pageTitle = await this.pageObjects.generalPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.generalPage.pageTitle);
    });

    it(`should ${test.args.action} display suppliers`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}DisplaySuppliers`, baseContext);

      const result = await this.pageObjects.generalPage.setDisplaySuppliers(test.args.exist);
      await expect(result).to.contains(this.pageObjects.generalPage.successfulUpdateMessage);
    });

    it('should go to Brands & Suppliers page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToBrandsPage_${index}`, baseContext);

      await this.pageObjects.generalPage.goToSubMenu(
        this.pageObjects.generalPage.catalogParentLink,
        this.pageObjects.generalPage.brandsAndSuppliersLink,
      );

      const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
    });

    it('should go to Suppliers tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToSuppliersTab_${index}`, baseContext);

      await this.pageObjects.brandsPage.goToSubTabSuppliers();
      const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
    });

    it(`should check that the message alert contains '${test.args.action}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAlertContains_${test.args.action}`, baseContext);

      const text = await this.pageObjects.suppliersPage.getAlertTextMessage();
      await expect(text).to.contains(test.args.action);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToFO_${test.args.action}`, baseContext);

      // View shop
      page = await this.pageObjects.suppliersPage.viewMyShop();
      this.pageObjects = await init();

      // Change shop language
      await this.pageObjects.homePage.changeLanguage('en');

      const isHomePage = await this.pageObjects.homePage.isHomePage();
      await expect(isHomePage).to.be.true;
    });

    it('should verify the existence of the suppliers page link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkSuppliersPage_${test.args.action}`, baseContext);

      await this.pageObjects.homePage.goToSiteMapPage();
      const pageTitle = await this.pageObjects.siteMapPage.getPageTitle();
      await expect(pageTitle).to.equal(this.pageObjects.siteMapPage.pageTitle);

      const exist = await this.pageObjects.siteMapPage.isSuppliersLinkVisible();
      await expect(exist).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo_${test.args.action}`, baseContext);

      page = await this.pageObjects.siteMapPage.closePage(browserContext, 0);
      this.pageObjects = await init();

      const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
    });
  });
});
