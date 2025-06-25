// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {
  type APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  FakerAPIClient,
  FakerModule,
  type ModuleInfo,
  type Page,
  utilsAPI,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_API_endpoints_module_putModulesToggleStatus';

describe('API : PUT /modules/toggle-status', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let moduleInfo1: ModuleInfo;
  let moduleInfo2: ModuleInfo;

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

  describe('BackOffice : Fetch two modules', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModulesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
      await boModuleManagerPage.closeSfToolBar(page);
      await boModuleManagerPage.filterByStatus(page, 'installed');

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it('should fetch modules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchModules', baseContext);

      const isModule1Visible = await boModuleManagerPage.searchModule(page, {tag: 'statscarrier'} as FakerModule);
      expect(isModule1Visible).to.be.equal(true);
      moduleInfo1 = await boModuleManagerPage.getModuleInformationNth(page, 1);

      const isModule2Visible = await boModuleManagerPage.searchModule(page, {tag: 'pagesnotfound'} as FakerModule);
      expect(isModule2Visible).to.be.equal(true);
      moduleInfo2 = await boModuleManagerPage.getModuleInformationNth(page, 1);
    });
  });

  [
    {
      status: false,
      verb: 'disable',
    },
    {
      status: true,
      verb: 'enable',
    },
  ].forEach((arg: {status: boolean, verb: string}, index: number) => {
    describe(`API : Update modules (${utilsCore.capitalize(arg.verb)})`, async () => {
      it('should request the endpoint /modules/toggle-status', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${index}`, baseContext);

        const apiResponse = await apiContext.put('modules/toggle-status', {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: {
            modules: [
              moduleInfo1.technicalName,
              moduleInfo2.technicalName,
            ],
            enabled: arg.status,
          },
        });

        expect(apiResponse.status()).to.eq(204);

        const response = (await apiResponse.body()).toString();
        expect(response).to.be.equal('');
      });
    });

    describe(`BackOffice : Check modules are ${arg.verb}d`, async () => {
      it('should check module status by technical name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkModules${index}`, baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
        await boModuleManagerPage.closeSfToolBar(page);

        const pageTitle = await boModuleManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);

        const modules: ModuleInfo[] = [
          moduleInfo1,
          moduleInfo2,
        ];

        for (let idxModule = 0; idxModule < modules.length; idxModule++) {
          const isModuleEnabled = await boModuleManagerPage.isModuleStatus(page, modules[idxModule].technicalName, 'enable');
          expect(isModuleEnabled).to.eq(arg.status);
        }
      });
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
