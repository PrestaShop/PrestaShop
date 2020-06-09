require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');


// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');
const SuppliersPage = require('@pages/BO/catalog/suppliers');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_suppliers_helperCard';


let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
    suppliersPage: new SuppliersPage(page),
  };
};

describe('Helper card', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to suppliers page
  loginCommon.loginBO();

  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );

    await this.pageObjects.brandsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should go to suppliers page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSuppliersPage', baseContext);

    await this.pageObjects.brandsPage.goToSubTabSuppliers();
    const pageTitle = await this.pageObjects.suppliersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.suppliersPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.suppliersPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await this.pageObjects.suppliersPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await this.pageObjects.suppliersPage.closeHelpSideBar();
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
