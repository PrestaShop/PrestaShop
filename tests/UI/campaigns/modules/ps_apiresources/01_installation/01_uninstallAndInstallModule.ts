// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
} from '@prestashop-core/ui-testing';

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

  describe('BackOffice - Login', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });
  });

  describe('BackOffice - Uninstall Module', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psApiResources.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psApiResources);
      expect(isModuleVisible).to.eq(true);
    });

    it('should display the uninstall modal and cancel it', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModuleAndCancel', baseContext);

      const textResult = await moduleManagerPage.setActionInModule(page, dataModules.psApiResources, 'uninstall', true);
      expect(textResult).to.eq('');

      const isModuleVisible = await moduleManagerPage.isModuleVisible(page, dataModules.psApiResources);
      expect(isModuleVisible).to.eq(true);

      const isModalVisible = await moduleManagerPage.isModalActionVisible(page, dataModules.psApiResources, 'uninstall');
      expect(isModalVisible).to.eq(false);

      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${dataModules.psApiResources.tag}/`);
      expect(dirExists).to.eq(true);
    });

    it('should uninstall the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'uninstallModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.psApiResources, 'uninstall', false);
      expect(successMessage).to.eq(moduleManagerPage.uninstallModuleSuccessMessage(dataModules.psApiResources.tag));

      // Check the directory `modules/dataModules.psApiResources.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${dataModules.psApiResources.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('BackOffice - Check that the module is not present', async () => {
    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await apiClientPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

      await apiClientPage.goToNewAPIClientPage(page);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
    });

    it('should check that scopes from Core are no more present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopesCore', baseContext);

      const hasScopes = await addNewApiClientPage.hasScopes(page);
      expect(hasScopes).to.equal(false);
    });

    it('should check that scopes from Module are not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopeModule', baseContext);

      const scopes = await addNewApiClientPage.getApiScopes(page, dataModules.psApiResources.tag);
      expect(scopes.length).to.be.eq(0);
    });
  });

  describe('BackOffice - Install the module', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPageInstall', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psApiResources.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleInstall', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psApiResources);
      expect(isModuleVisible).to.eq(true);
    });

    it('should install the module', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

      const successMessage = await moduleManagerPage.setActionInModule(page, dataModules.psApiResources, 'install', false);
      expect(successMessage).to.eq(moduleManagerPage.installModuleSuccessMessage(dataModules.psApiResources.tag));

      // Check the directory `modules/dataModules.psEmailAlerts.tag`
      const dirExists = await files.doesFileExist(`${files.getRootPath()}/modules/${dataModules.psApiResources.tag}/`);
      expect(dirExists).to.eq(true);
    });
  });

  describe('BackOffice - Check that the module is present', async () => {
    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPageAfterInstall', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await apiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(apiClientPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFoundAfterInstall', baseContext);

      const noRecordsFoundText = await apiClientPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPageAfterInstall', baseContext);

      await apiClientPage.goToNewAPIClientPage(page);

      const pageTitle = await addNewApiClientPage.getPageTitle(page);
      expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
    });

    it('should check that scopes are present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopePresent', baseContext);

      const hasScopes = await addNewApiClientPage.hasScopes(page);
      expect(hasScopes).to.equal(true);
    });

    it('should check that scopes from Core are not present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopesCoreNotPresent', baseContext);

      const scopes = await addNewApiClientPage.getApiScopes(page, '__core_scopes');
      expect(scopes.length).to.be.eq(0);
    });

    it('should check that scopes from Module are present', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkScopeModulePresent', baseContext);

      const scopes = await addNewApiClientPage.getApiScopes(page, dataModules.psApiResources.tag);
      expect(scopes.length).to.be.gt(0);
    });
  });
});
