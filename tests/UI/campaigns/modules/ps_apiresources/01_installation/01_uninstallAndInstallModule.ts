// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import apiAccessPage from '@pages/BO/advancedParameters/APIAccess';
import addNewApiAccessPage from '@pages/BO/advancedParameters/APIAccess/add';
import featureFlagPage from '@pages/BO/advancedParameters/featureFlag';
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_apiresources_installation_uninstallAndInstallModule';

describe('PrestaShop API Resources module - Uninstall and install module', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');
  });

  // Pre-condition: Enable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, true, `${baseContext}_enableAuthorizationServer`);

  describe('BackOffice - Login', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });
  });

  describe('BackOffice - Uninstall Module', async () => {
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

    it(`should search the module ${Modules.psApiResources.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psApiResources);
      expect(isModuleVisible).to.eq(true);
    });

    it('should display the uninstall modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModuleAndCancel', baseContext);

      const textResult = await moduleManagerPage.setActionInModule(page, Modules.psApiResources, 'uninstall', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await moduleManagerPage.isModuleVisible(page, Modules.psApiResources);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await moduleManagerPage.isModalActionVisible(page, Modules.psApiResources, 'uninstall');
      expect(isModalVisible).to.eq(false);

      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psApiResources.tag}/`);
      expect(dirExists).to.eq(true);
    });

    it('should uninstall the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psApiResources, 'uninstall', false);
      expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(Modules.psApiResources.tag));

      // Check the directory `modules/Modules.psApiResources.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psApiResources.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('BackOffice - Check that the module is not present', async () => {
    it('should go to \'Advanced Parameters > API Access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiAccessPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiAccessPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIAccessPage', baseContext);

      await apiAccessPage.goToNewAPIAccessPage(page);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleCreate);
    });

    it('should check that scopes from Core are present and enabled', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopesCore', baseContext);

      const scopes = await addNewApiAccessPage.getApiScopes(page, '__core_scopes');
      expect(scopes.length).to.be.gt(0);
    });

    it('should check that scopes from Module are not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopeModule', baseContext);

      const scopes = await addNewApiAccessPage.getApiScopes(page, Modules.psApiResources.tag);
      expect(scopes.length).to.be.eq(0);
    });
  });

  describe('BackOffice - Install the module', async () => {
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

    it(`should search the module ${Modules.psApiResources.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psApiResources);
      expect(isModuleVisible).to.eq(true);
    });

    it('should install the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, Modules.psApiResources, 'install', false);
      expect(successMessage).to.eq(moduleManagerPage.installModuleSuccessMessage(Modules.psApiResources.tag));

      // Check the directory `modules/Modules.psEmailAlerts.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${Modules.psApiResources.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('BackOffice - Check that the module is present', async () => {
    it('should go to \'Advanced Parameters > API Access\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.authorizationServerLink,
      );

      const pageTitle = await apiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiAccessPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiAccessPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Access page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIAccessPage', baseContext);

      await apiAccessPage.goToNewAPIAccessPage(page);

      const pageTitle = await addNewApiAccessPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiAccessPage.pageTitleCreate);
    });

    it('should check that scopes from Core are present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopesCore', baseContext);

      const scopes = await addNewApiAccessPage.getApiScopes(page, '__core_scopes');
      expect(scopes.length).to.be.gt(0);
    });

    it('should check that scopes from Module are present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopeModule', baseContext);

      const scopes = await addNewApiAccessPage.getApiScopes(page, Modules.psApiResources.tag);
      expect(scopes.length).to.be.gt(0);
    });
  });

  // Post-condition: Disable experimental feature : Authorization server
  setFeatureFlag(featureFlagPage.featureFlagAuthorizationServer, false, `${baseContext}_disableAuthorizationServer`);
});
