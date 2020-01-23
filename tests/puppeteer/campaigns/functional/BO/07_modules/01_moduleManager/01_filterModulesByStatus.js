require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ModuleManagerPage = require('@pages/BO/modules/moduleManager');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    moduleManagerPage: new ModuleManagerPage(page),
  };
};

describe('Filter modules by status', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to module manager page
  loginCommon.loginBO();

  it('should go to module manager page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.modulesParentLink,
      this.pageObjects.boBasePage.moduleManagerLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.moduleManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.moduleManagerPage.pageTitle);
  });

  describe('Filter modules by status', async () => {
    const tests = [
      {enabled: false},
      {enabled: true},
    ];
    tests.forEach((test) => {
      it(`should filter by status enabled : '${test.enabled}'`, async function () {
        await this.pageObjects.moduleManagerPage.filterByStatus(test.enabled);
        const modules = await this.pageObjects.moduleManagerPage.getAllModulesStatus();
        await modules.map(
          module => expect(module.status, `${module.name} is not ${test.enabled ? 'enabled' : 'disabled'}`)
            .to.equal(test.enabled),
        );
      });
    });
  });
});
