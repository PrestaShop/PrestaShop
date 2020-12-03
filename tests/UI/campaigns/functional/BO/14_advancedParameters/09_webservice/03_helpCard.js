require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const webservicePage = require('@pages/BO/advancedParameters/webservice');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_webservice_helpCard';

let browserContext;
let page;

// Check that help card is in english in webservice page
describe('Webservice help card', async () => {
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

  it('should go to webservice page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.webserviceLink,
    );

    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    await expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await webservicePage.openHelpSideBar(page);
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await webservicePage.getHelpDocumentURL(page);
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarNotVisible = await webservicePage.closeHelpSideBar(page);
    await expect(isHelpSidebarNotVisible).to.be.true;
  });
});
