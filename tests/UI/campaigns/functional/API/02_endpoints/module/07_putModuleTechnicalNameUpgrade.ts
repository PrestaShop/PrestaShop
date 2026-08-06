import {expect} from 'chai';
import testContext from '@utils/testContext';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {installModule, uninstallModule} from '@commonTests/BO/modules/moduleManager';

import {
  type APIRequestContext,
  boDashboardPage,
  boModuleManagerPage,
  boLoginPage,
  type BrowserContext,
  dataModules,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_module_putModuleTechnicalNameUpgrade';

describe('API : PUT /modules/{technicalName}/upgrade', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'module_write';

  // PRE-TEST : Uninstall ps_cashondelivery
  uninstallModule(dataModules.psCashOnDelivery, `${baseContext}_preTest_0`);
  // PRE-TEST : Install ps_cashondelivery (old version)
  installModule(dataModules.psCashOnDelivery, false, `${baseContext}_preTest_1`);

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

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.be.equal(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(moduleInfo.technicalName).to.equal(dataModules.psCashOnDelivery.tag);
      expect(dataModules.psCashOnDelivery.versionOld).to.contains(moduleInfo.version);
      expect(moduleInfo.enabled).to.equal(true);
      expect(moduleInfo.installed).to.equal(true);
    });
  });

  describe('API : PUT /modules/{technicalName}/upgrade', async () => {
    // Upload the module before upgrading
    it('should request the endpoint /modules/upload-source', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointUploadSource', baseContext);

      const apiResponse = await apiContext.post('modules/upload-source', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          source: dataModules.psCashOnDelivery.releaseZip(dataModules.psCashOnDelivery.versionCurrent),
        },
      });
      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');
    });

    it('should request the endpoint /modules/{technicalName}/upgrade', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.put(`modules/${dataModules.psCashOnDelivery.tag}/upgrade`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });

      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);
      expect(jsonResponse).to.have.all.keys(
        'moduleId',
        'technicalName',
        'moduleVersion',
        'installedVersion',
        'enabled',
        'installed',
      );
    });

    it('should check the JSON Response : `moduleId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseModuleId', baseContext);

      expect(jsonResponse).to.have.property('moduleId');
      expect(jsonResponse.moduleId).to.be.a('number');
      expect(jsonResponse.moduleId).to.be.gt(0);
    });

    it('should check the JSON Response : `technicalName`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseTechnicalName', baseContext);

      expect(jsonResponse).to.have.property('technicalName');
      expect(jsonResponse.technicalName).to.be.a('string');
      expect(jsonResponse.technicalName).to.be.equal(dataModules.psCashOnDelivery.tag);
    });

    it('should check the JSON Response : `moduleVersion`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseModuleVersion', baseContext);

      expect(jsonResponse).to.have.property('moduleVersion');
      expect(jsonResponse.moduleVersion).to.be.a('string');
      expect(dataModules.psCashOnDelivery.versionCurrent).to.contains(jsonResponse.moduleVersion);
    });

    it('should check the JSON Response : `installedVersion`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseInstalledVersion', baseContext);

      expect(jsonResponse).to.have.property('installedVersion');
      expect(jsonResponse.moduleVersion).to.be.a('string');
      expect(dataModules.psCashOnDelivery.versionCurrent).to.contains(jsonResponse.installedVersion);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.be.equal(true);
    });

    it('should check the JSON Response : `installed`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseInstalled', baseContext);

      expect(jsonResponse).to.have.property('installed');
      expect(jsonResponse.installed).to.be.a('boolean');
      expect(jsonResponse.installed).to.be.equal(true);
    });
  });

  describe('BackOffice : Check the module is upgraded', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModuleUpgraded', baseContext);

      await boModuleManagerPage.reloadPage(page);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psCashOnDelivery);
      expect(isModuleVisible).to.be.equal(true);

      const moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
      expect(moduleInfo.technicalName).to.equal(dataModules.psCashOnDelivery.tag);
      expect(dataModules.psCashOnDelivery.versionCurrent).to.contains(moduleInfo.version);
      expect(moduleInfo.enabled).to.equal(true);
      expect(moduleInfo.installed).to.equal(true);
    });
  });
});
