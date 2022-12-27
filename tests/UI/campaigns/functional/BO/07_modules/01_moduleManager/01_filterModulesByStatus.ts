// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import moduleManagerPage from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_filterModulesByStatus';

describe('BO - Modules - Module Manager : Filter modules by status', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
          (module) => expect(
            module.status,
            `${module.name} is not ${status ? 'enabled' : 'disabled'}`,
          ).to.equal(status),
        );
      });
    });
  });
});
