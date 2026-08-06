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
  FakerModule,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_module_putModulesBulkUninstall';

describe('API : PUT /modules/bulk-uninstall', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;

  const clientScope: string = 'module_write';
  const modules: FakerModule[] = [
    dataModules.keycloak,
    dataModules.psCashOnDelivery,
  ];
  const tagModules: string[] = [
    dataModules.keycloak.tag,
    dataModules.psCashOnDelivery.tag,
  ];

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

    modules.forEach((module: FakerModule, index: number) => {
      it(`should filter list by name : ${module.name}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModuleInstalled${index}`, baseContext);

        const isModuleVisible = await boModuleManagerPage.searchModule(page, module);
        expect(isModuleVisible).to.be.equal(true);

        const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
        expect(moduleInfo.technicalName).to.equal(module.tag);
        expect(module.versionCurrent).to.contains(moduleInfo.version);
        expect(moduleInfo.enabled).to.equal(true);
        expect(moduleInfo.installed).to.equal(true);
      });
    });
  });

  describe('API : PUT /modules/bulk-uninstall with deleteFiles = false', async () => {
    it('should request the endpoint /modules/bulk-uninstall', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointDeleteFilesFalse', baseContext);

      const apiResponse = await apiContext.put('modules/bulk-uninstall', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          deleteFiles: false,
          modules: tagModules,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check modules are uninstalled', async () => {
    modules.forEach((module: FakerModule, index: number) => {
      it(`should filter list by name : ${module.name}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModuleUninstalled${index}`, baseContext);

        await boModuleManagerPage.reloadPage(page);

        const isModuleVisible = await boModuleManagerPage.searchModule(page, module);
        expect(isModuleVisible).to.be.equal(true);

        const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
        expect(moduleInfo.technicalName).to.equal(module.tag);
        expect(module.versionCurrent).to.contains(moduleInfo.version);
        expect(moduleInfo.enabled).to.equal(false);
        expect(moduleInfo.installed).to.equal(false);
      });

      it(`should install the module '${module.name}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `installModule${index}`, baseContext);

        const successMessage = await boModuleManagerPage.setActionInModule(page, module, 'install');
        expect(successMessage).to.eq(boModuleManagerPage.installModuleSuccessMessage(module.tag));
      });
    });
  });

  describe('API : PUT /modules/bulk-uninstall with deleteFiles = true', async () => {
    it('should request the endpoint /modules/bulk-uninstall', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointDeleteFilesTrue', baseContext);

      const apiResponse = await apiContext.put('modules/bulk-uninstall', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          deleteFiles: true,
          modules: tagModules,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the module are uninstalled and deleted', async () => {
    modules.forEach((module: FakerModule, index: number) => {
      it(`should filter list by name : ${module.name}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModuleDeleted${index}`, baseContext);

        await boModuleManagerPage.reloadPage(page);

        const isModuleVisible = await boModuleManagerPage.searchModule(page, module);

        if (module.tag === dataModules.keycloak.tag) {
          // Module (not native) is not listed by the API Distribution Client
          expect(isModuleVisible).to.be.equal(false);
        } else {
          // Module (native) is listed by the API Distribution Client
          expect(isModuleVisible).to.be.equal(true);

          const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
          expect(moduleInfo.technicalName).to.equal(module.tag);
          expect(module.versionCurrent).to.contains(moduleInfo.version);
          expect(moduleInfo.enabled).to.equal(false);
          expect(moduleInfo.installed).to.equal(false);
        }
      });
    });
  });

  // POST-CONDITION: Reinstall module
  installModule(dataModules.psCashOnDelivery, true, `${baseContext}_postTest_0`);
});
