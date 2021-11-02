require('module-alias/register');

// Using chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const moduleManagerPage = require('@pages/BO/modules/moduleManager');

const baseContext = 'functional_BO_modules_moduleManager_filterModulesByStatus';

let browserContext;
let page;

describe('BO - Modules - Module Manager : Filter modules by status', async () => {
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

  it('should go to \'Modules > Module Manager\' page', async function () {
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
    [false, true].forEach((status, index) => {
      it(`should filter by status enabled : '${status}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `filterByStatus${index}`, baseContext);

        await moduleManagerPage.filterByStatus(page, status);

        const modules = await moduleManagerPage.getAllModulesStatus(page);

        modules.map(
          module => expect(
            module.status,
            `${module.name} is not ${status ? 'enabled' : 'disabled'}`,
          ).to.equal(status),
        );
      });
    });
  });
});
