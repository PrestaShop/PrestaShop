// Import utils
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import pagesConfigurationPage from '@pages/BO/design/themeAndLogo/pagesConfiguration';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_themeAndLogo_pagesConfiguration_installUninstallModule';

describe('BO - Design - Theme & Logo : Install/Uninstall module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.themeAndLogoParentLink,
    );
    await themeAndLogoPage.closeSfToolBar(page);

    const pageTitle = await themeAndLogoPage.getPageTitle(page);
    expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
  });

  it('should go to \'Pages configuration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPagesConfigurationPage', baseContext);

    await themeAndLogoPage.goToSubTabPagesConfiguration(page);

    const pageTitle = await pagesConfigurationPage.getPageTitle(page);
    expect(pageTitle).to.contains(pagesConfigurationPage.pageTitle);
  });

  it('should uninstall the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

    const successMessage = await pagesConfigurationPage.setActionInModule(page, dataModules.mainMenu, 'uninstall');
    expect(successMessage).to.eq(pagesConfigurationPage.successMessage);
  });

  it('should install the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

    const successMessage = await pagesConfigurationPage.setActionInModule(page, dataModules.mainMenu, 'install');
    expect(successMessage).to.eq(pagesConfigurationPage.successMessage);
  });
});
