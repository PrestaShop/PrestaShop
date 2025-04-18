import testContext from '@utils/testContext';

import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  boThemeAndLogoPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_themecusto_installation_disableEnableModule';

describe('Theme Customization module - Disable/Enable module', async () => {
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

  describe('Disable/Enable module', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageForEnable', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule$ForDisable', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psThemeCusto);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable and cancel the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableCancelModule', baseContext);

      await boModuleManagerPage.setActionInModule(page, dataModules.psThemeCusto, 'disable', true);

      const isModuleVisible = await boModuleManagerPage.isModuleVisible(page, dataModules.psThemeCusto);
      expect(isModuleVisible).to.eq(true);
    });

    it('should disable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psThemeCusto, 'disable');
      expect(successMessage).to.eq(boModuleManagerPage.disableModuleSuccessMessage(dataModules.psThemeCusto.tag));
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

    // @todo : https://github.com/PrestaShop/PrestaShop/issues/36590
    it('should check that tabs are not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTabsNotPresent', baseContext);

      this.skip();

      const hasSubTabAdvancedCustomization = await boThemeAndLogoPage.hasSubTabAdvancedCustomization(page);
      expect(hasSubTabAdvancedCustomization).to.equal(false);

      const hasSubTabPagesConfiguration = await boThemeAndLogoPage.hasSubTabPagesConfiguration(page);
      expect(hasSubTabPagesConfiguration).to.equal(false);
    });

    it('should return to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToModuleManagerPageForEnable', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleForEnable', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psThemeCusto);
      expect(isModuleVisible).to.eq(true);
    });

    it('should enable the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psThemeCusto, 'enable');
      expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.psThemeCusto.tag));
    });

    it('should go to \'Design > Theme & Logo\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'returnToThemeAndLogoPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.designParentLink,
        boDashboardPage.themeAndLogoParentLink,
      );
      await boThemeAndLogoPage.closeSfToolBar(page);

      const pageTitle = await boThemeAndLogoPage.getPageTitle(page);
      expect(pageTitle).to.contains(boThemeAndLogoPage.pageTitle);
    });

    it('should check that tabs are present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTabsPresent', baseContext);

      const hasSubTabAdvancedCustomization = await boThemeAndLogoPage.hasSubTabAdvancedCustomization(page);
      expect(hasSubTabAdvancedCustomization).to.equal(true);

      const hasSubTabPagesConfiguration = await boThemeAndLogoPage.hasSubTabPagesConfiguration(page);
      expect(hasSubTabPagesConfiguration).to.equal(true);
    });
  });
});
