require('module-alias/register');

// Using chai
const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ModuleManagerPage = require('@pages/BO/modules/moduleManager');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_moduleManager_filterModulesByStatus';

let browser;
let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    moduleManagerPage: new ModuleManagerPage(page),
  };
};

describe('Filter modules by status', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to module manager page
  loginCommon.loginBO();

  it('should go to module manager page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.modulesParentLink,
      this.pageObjects.dashboardPage.moduleManagerLink,
    );

    await this.pageObjects.moduleManagerPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.moduleManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.moduleManagerPage.pageTitle);
  });

  describe('Filter modules by status', async () => {
    const tests = [
      {
        enabled: false,
      },
      {
        enabled: true,
      },
    ];

    tests.forEach((test) => {
      it(`should filter by status enabled : '${test.enabled}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByStatus${test.enabled}`, baseContext);

        await this.pageObjects.moduleManagerPage.filterByStatus(test.enabled);

        const modules = await this.pageObjects.moduleManagerPage.getAllModulesStatus();

        await modules.map(
          module => expect(
            module.status,
            `${module.name} is not ${test.enabled ? 'enabled' : 'disabled'}`,
          ).to.equal(test.enabled),
        );
      });
    });
  });
});
