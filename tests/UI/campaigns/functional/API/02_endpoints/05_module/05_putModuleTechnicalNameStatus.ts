// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boModuleManagerPage,
  boLoginPage,
  type BrowserContext,
  dataModules,
  FakerAPIClient,
  type ModuleInfo,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_module_putModuleTechnicalNameStatus';

describe('API : PUT /module/{technicalName}/status', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;
  let moduleInfo: ModuleInfo;

  const clientScope: string = 'module_write';
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BackOffice : Fetch the access token', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Advanced Parameters > API Client\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.adminAPILink,
      );

      const pageTitle = await boApiClientsPage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
    });

    it('should check that no records found', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

      const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
      expect(noRecordsFoundText).to.contains('warning No records found');
    });

    it('should go to add New API Client page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

      await boApiClientsPage.goToNewAPIClientPage(page);

      const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
      expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
    });

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const textResult = await boApiClientsCreatePage.addAPIClient(page, clientData);
      expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

      const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await boApiClientsCreatePage.copyClientSecret(page);

      clientSecret = await boApiClientsCreatePage.getClipboardText(page);
      expect(clientSecret.length).to.be.gt(0);
    });

    it('should request the endpoint /access_token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      const apiResponse = await apiContext.post('access_token', {
        form: {
          client_id: clientData.clientId,
          client_secret: clientSecret,
          grant_type: 'client_credentials',
          scope: clientScope,
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');

      accessToken = jsonResponse.access_token;
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModulesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
      expect(isModuleVisible).to.be.equal(true);

      moduleInfo = await boModuleManagerPage.getModuleInformationNth(page, 1);
    });
  });

  [
    false,
    true,
  ].forEach((argStatus: boolean, index: number) => {
    describe(`API : Check Data with status = ${argStatus}`, async () => {
      it('should request the endpoint /module/{technicalName}/status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${index}`, baseContext);

        const apiResponse = await apiContext.put(`module/${moduleInfo.technicalName}/status`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: {
            enabled: argStatus,
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it('should check the JSON Response keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseKeys${index}`, baseContext);
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
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseModuleId${index}`, baseContext);

        expect(jsonResponse).to.have.property('moduleId');
        expect(jsonResponse.moduleId).to.be.a('number');
        expect(jsonResponse.moduleId).to.be.equal(moduleInfo.moduleId);
      });

      it('should check the JSON Response : `technicalName`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseTechnicalName${index}`, baseContext);

        expect(jsonResponse).to.have.property('technicalName');
        expect(jsonResponse.technicalName).to.be.a('string');
        expect(jsonResponse.technicalName).to.be.equal(moduleInfo.technicalName);
      });

      it('should check the JSON Response : `moduleVersion`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseModuleVersion${index}`, baseContext);

        expect(jsonResponse).to.have.property('moduleVersion');
        expect(jsonResponse.moduleVersion).to.be.a('string');
        expect(jsonResponse.moduleVersion).to.be.equal(moduleInfo.version);
      });

      it('should check the JSON Response : `installedVersion`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseInstalledVersion${index}`, baseContext);

        expect(jsonResponse).to.have.property('installedVersion');
        expect(jsonResponse.installedVersion).to.be.a('string');
        expect(jsonResponse.installedVersion).to.be.equal(moduleInfo.version);
      });

      it('should check the JSON Response : `enabled`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseEnabled${index}`, baseContext);

        expect(jsonResponse).to.have.property('enabled');
        expect(jsonResponse.enabled).to.be.a('boolean');
        expect(jsonResponse.enabled).to.be.equal(argStatus);
      });

      it('should check the JSON Response : `installed`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseInstalled${index}`, baseContext);

        expect(jsonResponse).to.have.property('installed');
        expect(jsonResponse.installed).to.be.a('boolean');
        expect(jsonResponse.installed).to.be.equal(moduleInfo.installed);
      });

      it('should reload the \'Modules > Module Manager\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToModulesPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
        await boDashboardPage.reloadPage(page);

        const pageTitle = await boModuleManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
      });

      it('should search the module', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModule${index}`, baseContext);

        const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.blockwishlist);
        expect(isModuleVisible).to.be.equal(true);
      });

      it(`should check the module is ${argStatus ? 'enabled' : 'disabled'}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkStatus${index}`, baseContext);

        const moduleStatus = await boModuleManagerPage.getModuleInformationNth(page, 1);
        expect(moduleStatus.enabled).to.equal(argStatus);
      });
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
