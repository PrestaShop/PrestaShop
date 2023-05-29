// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import pagesConfigurationPage from '@pages/BO/design/themeAndLogo/pagesConfiguration';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_design_themeAndLogo_pagesConfiguration_installUninstallModule';

describe('BO - Design - Theme & Logo : Install/Uninstall module', async () => {
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

  it('should go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.themeAndLogoParentLink,
    );
    await themeAndLogoPage.closeSfToolBar(page);

    const pageTitle = await themeAndLogoPage.getPageTitle(page);
    await expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
  });

  it('should go to \'Pages configuration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPagesConfigurationPage', baseContext);

    await themeAndLogoPage.goToSubTabPagesConfiguration(page);

    const pageTitle = await pagesConfigurationPage.getPageTitle(page);
    await expect(pageTitle).to.contains(pagesConfigurationPage.pageTitle);
  });

  it('should uninstall the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

    const successMessage = await pagesConfigurationPage.setActionInModule(page, Modules.mainMenu, 'uninstall');
    await expect(successMessage).to.eq(pagesConfigurationPage.successMessage);
  });

  it('should install the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

    const successMessage = await pagesConfigurationPage.setActionInModule(page, Modules.mainMenu, 'install');
    await expect(successMessage).to.eq(pagesConfigurationPage.successMessage);
  });
});
