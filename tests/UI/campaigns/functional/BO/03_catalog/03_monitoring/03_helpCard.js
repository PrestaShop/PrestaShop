require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const monitoringPage = require('@pages/BO/catalog/monitoring');

const baseContext = 'functional_BO_catalog_monitoring_helpCard';

let browserContext;
let page;

// Check help card language in monitoring page
describe('BO - Catalog - Monitoring : Help card in monitoring page', async () => {
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

  it('should go to \'Catalog > Monitoring\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToMonitoringPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.monitoringLink,
    );

    await monitoringPage.closeSfToolBar(page);

    const pageTitle = await monitoringPage.getPageTitle(page);
    await expect(pageTitle).to.contains(monitoringPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await monitoringPage.openHelpSideBar(page);
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await monitoringPage.getHelpDocumentURL(page);
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await monitoringPage.closeHelpSideBar(page);
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
