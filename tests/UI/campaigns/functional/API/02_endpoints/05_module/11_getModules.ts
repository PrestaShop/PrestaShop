import testContext from '@utils/testContext';
import {expect} from 'chai';

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
  type ModuleApiInfo,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_module_getModules';

describe('API : GET /modules', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;
  const jsonResponseItems: ModuleApiInfo[] = [];

  const clientScope: string = 'module_read';
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

  describe('API : Fetch Data', async () => {
    [
      {
        page: 0,
      },
      {
        page: 1,
      },
    ].forEach((arg: {page: number}, index: number) => {
      it(`should request the endpoint /modules (page ${arg.page})`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${index}`, baseContext);

        const apiResponse = await apiContext.get(`modules${arg.page > 0 ? `?offset=${arg.page * 50}` : ''}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it(`should check the JSON Response keys (page ${arg.page})`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseKeys${index}`, baseContext);

        // All keys are visible for modules event offset and orderBy when they are null
        // That is because we want null values in module infos to be displayed (like the installedVersion)
        // But we can't fine tune ApiPlatform enough to select which nullable fields are displayed, so they are all visible
        const keys = [
          'totalItems',
          'sortOrder',
          'orderBy',
          'limit',
          'offset',
          'filters',
          'items',
        ];

        expect(jsonResponse).to.have.all.keys(keys);

        expect(jsonResponse.items.length).to.be.gt(0);
        if (arg.page === 0) {
          expect(jsonResponse.items.length).to.be.equal(jsonResponse.limit);
        } else {
          expect(jsonResponse.items.length).to.be.lt(jsonResponse.limit);
        }

        for (let i:number = 0; i < jsonResponse.items.length; i++) {
          expect(jsonResponse.items[i]).to.have.all.keys(
            'moduleId',
            'technicalName',
            'moduleVersion',
            'installedVersion',
            'enabled',
          );
          jsonResponseItems.push(jsonResponse.items[i]);
        }
      });
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModulesPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.moduleManagerLink);
      await boModuleManagerPage.closeSfToolBar(page);
      await boModuleManagerPage.filterByStatus(page, 'installed');

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);

      const numModules = await boModuleManagerPage.getNumberOfModules(page);
      expect(numModules).to.eq(jsonResponseItems.length);
    });

    it('should filter list by technicaleName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponseItems.length; idxItem++) {
        // eslint-disable-next-line no-loop-func
        const isModuleVisible = await boModuleManagerPage.searchModule(
          page,
          {tag: jsonResponseItems[idxItem].technicalName} as FakerModule,
        );
        expect(isModuleVisible).to.be.equal(true);

        const moduleInfos = await boModuleManagerPage.getModuleInformationNth(page, 1);
        expect(moduleInfos.moduleId).to.equal(jsonResponseItems[idxItem].moduleId);
        expect(moduleInfos.technicalName).to.equal(jsonResponseItems[idxItem].technicalName);
        expect(moduleInfos.version).to.equal(jsonResponseItems[idxItem].moduleVersion);
        expect(moduleInfos.version).to.equal(jsonResponseItems[idxItem].installedVersion);
        expect(moduleInfos.enabled).to.equal(jsonResponseItems[idxItem].enabled);
      }
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
