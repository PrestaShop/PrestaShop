require('module-alias/register');

// Using chai
const {expect} = require('chai');

const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_modules_moduleManager_filterModulesByStatus';


let browserContext;
let page;

describe('Filter modules by status', async () => {
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

  it('should go to module manager page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );

    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
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

        await moduleManagerPage.filterByStatus(page, test.enabled);

        const modules = await moduleManagerPage.getAllModulesStatus(page);

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
