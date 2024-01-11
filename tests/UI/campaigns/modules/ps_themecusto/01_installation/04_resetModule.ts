// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import advancedCustomizationPage from '@pages/BO/design/themeAndLogo/advancedCustomization';
import pagesConfigurationPage from '@pages/BO/design/themeAndLogo/pagesConfiguration';
import themeAndLogoPage from '@pages/BO/design/themeAndLogo/themeAndLogo';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_themecusto_installation_resetModule';

describe('Theme Customization module - Reset module', async () => {
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
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search the module ${Modules.psThemeCusto.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psThemeCusto);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await moduleManagerPage.setActionInModule(page, Modules.psThemeCusto, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psThemeCusto);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await moduleManagerPage.isModalActionVisible(page, Modules.psThemeCusto, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psThemeCusto, 'reset');
    expect(successMessage).to.eq(moduleManagerPage.resetModuleSuccessMessage(Modules.psThemeCusto.tag));
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
    expect(pageTitle).to.contains(themeAndLogoPage.pageTitle);
  });

  it('should go to \'Pages configuration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPagesConfigurationPage', baseContext);

    await themeAndLogoPage.goToSubTabPagesConfiguration(page);

    const pageTitle = await pagesConfigurationPage.getPageTitle(page);
    expect(pageTitle).to.contains(pagesConfigurationPage.pageTitle);
  });

  it('should go to \'Advanced Customization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage', baseContext);

    await themeAndLogoPage.goToSubTabAdvancedCustomization(page);

    const pageTitle = await advancedCustomizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(advancedCustomizationPage.pageTitle);
  });
});
