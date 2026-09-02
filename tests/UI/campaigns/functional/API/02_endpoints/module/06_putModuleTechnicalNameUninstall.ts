import testContext from '@utils/testContext';

import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {installModule} from '@commonTests/BO/modules/moduleManager';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boDashboardPage,
  boModuleManagerPage,
  boLoginPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_module_putModuleTechnicalNameUninstall';

describe('API : PUT /modules/{technicalName}/uninstall', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;

  const clientScope: string = 'module_write';

  // PRE-CONDITION : Install module
  installModule(dataModules.keycloak, true, `${baseContext}_preTest_1`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Check the module is installed', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModulesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleInstalled', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.keycloak);
      expect(isModuleVisible).to.be.equal(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(moduleInfo.technicalName).to.equal(dataModules.keycloak.tag);
      expect(dataModules.keycloak.versionCurrent).to.contains(moduleInfo.version);
      expect(moduleInfo.enabled).to.equal(true);
      expect(moduleInfo.installed).to.equal(true);
    });
  });

  describe('API : PUT /modules/{technicalName}/uninstall with deleteFiles = false', async () => {
    it('should request the endpoint /modules/{technicalName}/uninstall', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointDeleteFilesFalse', baseContext);

      const apiResponse = await apiContext.put(`modules/${dataModules.keycloak.tag}/uninstall`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          deleteFiles: false,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the module is uninstalled', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleUninstalled', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.keycloak);
      expect(isModuleVisible).to.be.equal(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(moduleInfo.technicalName).to.equal(dataModules.keycloak.tag);
      expect(dataModules.keycloak.versionCurrent).to.contains(moduleInfo.version);
      expect(moduleInfo.enabled).to.equal(false);
      expect(moduleInfo.installed).to.equal(false);
    });

    it(`should install the module '${dataModules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'installModule', baseContext);

      const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.keycloak, 'install');
      expect(successMessage).to.eq(boModuleManagerPage.installModuleSuccessMessage(dataModules.keycloak.tag));
    });
  });

  describe('API : PUT /modules/{technicalName}/uninstall with deleteFiles = true', async () => {
    it('should request the endpoint /modules/{technicalName}/uninstall', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointDeleteFilesTrue', baseContext);

      const apiResponse = await apiContext.put(`modules/${dataModules.keycloak.tag}/uninstall`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          deleteFiles: true,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the module is uninstalled and deleted', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleDeleted', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.keycloak);
      expect(isModuleVisible).to.be.equal(false);
    });
  });
});
