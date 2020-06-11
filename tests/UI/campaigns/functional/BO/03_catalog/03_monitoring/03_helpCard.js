require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const MonitoringPage = require('@pages/BO/catalog/monitoring');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_monitoring_helpCard';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    monitoringPage: new MonitoringPage(page),
  };
};

// Check help card language in monitoring page
describe('Help card in monitoring page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to monitoring page
  loginCommon.loginBO();

  it('should go to \'Catalog > Monitoring\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.monitoringLink,
    );

    await this.pageObjects.monitoringPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.monitoringPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.monitoringPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.monitoringPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await this.pageObjects.monitoringPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await this.pageObjects.monitoringPage.closeHelpSideBar();
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
