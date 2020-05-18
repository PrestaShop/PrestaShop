require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmployeesPage = require('@pages/BO/advancedParameters/team');
const ProfilesPage = require('@pages/BO/advancedParameters/team/profiles');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_team_profiles_helpCard';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    employeesPage: new EmployeesPage(page),
    profilesPage: new ProfilesPage(page),
  };
};

// Check that help card is in english in profiles page
describe('Profiles help card', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to profiles page
  loginCommon.loginBO();

  it('should go to employees page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.advancedParametersLink,
      this.pageObjects.dashboardPage.teamLink,
    );

    await this.pageObjects.employeesPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.employeesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.employeesPage.pageTitle);
  });

  it('should go to "Profiles" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProfilesPage', baseContext);

    await this.pageObjects.employeesPage.goToProfilesPage();
    const pageTitle = await this.pageObjects.profilesPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.profilesPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.profilesPage.openHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await this.pageObjects.profilesPage.getHelpDocumentURL();
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarVisible = await this.pageObjects.profilesPage.closeHelpSideBar();
    await expect(isHelpSidebarVisible).to.be.true;
  });
});
