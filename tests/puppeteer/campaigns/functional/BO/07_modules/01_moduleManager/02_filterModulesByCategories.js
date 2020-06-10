require('module-alias/register');

// Using chai
const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const {moduleCategories} = require('@data/demo/moduleCategories');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ModuleManagerPage = require('@pages/BO/modules/moduleManager');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_moduleManager_filterModulesByCategory';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    moduleManagerPage: new ModuleManagerPage(page),
  };
};

describe('Filter modules by Categories', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.modulesParentLink,
      this.pageObjects.dashboardPage.moduleManagerLink,
    );

    await this.pageObjects.moduleManagerPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.moduleManagerPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.moduleManagerPage.pageTitle);
  });

  describe('Filter modules by categories', async () => {
    moduleCategories.forEach((category) => {
      it(`should filter by category : '${category}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByCategory${category}`, baseContext);

        // Filter modules by categories
        await this.pageObjects.moduleManagerPage.filterByCategory(category);

        // Check first category displayed
        const firstBlockTitle = await this.pageObjects.moduleManagerPage.getBlockModuleTitle(1);
        await expect(firstBlockTitle).to.equal(category);
      });
    });
  });
});
