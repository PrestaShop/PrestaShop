require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const SqlManagerPage = require('@pages/BO/advancedParameters/database/sqlManager');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_database_sqlManager_helpCard';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    sqlManagerPage: new SqlManagerPage(page),
  };
};

// Check that help card is in english in sql manager page
describe('Sql manager help card', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to sql manager page
  loginCommon.loginBO();

  it('should go to database > sql manager page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.advancedParametersLink,
      this.pageObjects.dashboardPage.databaseLink,
    );

    await this.pageObjects.sqlManagerPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.sqlManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.sqlManagerPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.sqlManagerPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await this.pageObjects.sqlManagerPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.sqlManagerPage.closeHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;
  });
});
