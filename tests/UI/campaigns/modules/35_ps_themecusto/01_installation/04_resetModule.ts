// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boThemeAdvancedConfigurationPage,
  boThemeAndLogoPage,
  boThemePagesConfigurationPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_themecusto_installation_resetModule';

describe('Theme Customization module - Reset module', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await boModuleManagerPage.closeSfToolBar(page);

    const pageTitle = await boModuleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.psThemeCusto.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psThemeCusto);
    expect(isModuleVisible).to.eq(true);
  });

  it('should display the reset modal and cancel it', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModuleAndCancel', baseContext);

    const textResult = await boModuleManagerPage.setActionInModule(page, dataModules.psThemeCusto, 'reset', true);
    expect(textResult).to.eq('');

    const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psThemeCusto);
    expect(isModuleVisible).to.eq(true);

    const isModalVisible = await boModuleManagerPage.isModalActionVisible(page, dataModules.psThemeCusto, 'reset');
    expect(isModalVisible).to.eq(false);
  });

  it('should reset the module', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetModule', baseContext);

    const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psThemeCusto, 'reset');
    expect(successMessage).to.eq(boModuleManagerPage.resetModuleSuccessMessage(dataModules.psThemeCusto.tag));
  });

  it('should go to \'Design > Theme & Logo\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToThemeAndLogoPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.themeAndLogoParentLink,
    );
    await boThemeAndLogoPage.closeSfToolBar(page);

    const pageTitle = await boThemeAndLogoPage.getPageTitle(page);
    expect(pageTitle).to.contains(boThemeAndLogoPage.pageTitle);
  });

  it('should go to \'Pages configuration\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPagesConfigurationPage', baseContext);

    await boThemeAndLogoPage.goToSubTabPagesConfiguration(page);

    const pageTitle = await boThemePagesConfigurationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boThemePagesConfigurationPage.pageTitle);
  });

  it('should go to \'Advanced Customization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAdvancedCustomizationPage', baseContext);

    await boThemeAndLogoPage.goToSubTabAdvancedCustomization(page);

    const pageTitle = await boThemeAdvancedConfigurationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boThemeAdvancedConfigurationPage.pageTitle);
  });
});
