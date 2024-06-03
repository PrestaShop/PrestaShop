// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from '@pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import {moduleManager} from '@pages/BO/modules/moduleManager';

// Import data
import APIClientData from '@data/faker/APIClient';

import {
  boDashboardPage,
  boModuleManagerPage,
  FakerModule,
  type ModuleInfo,
} from '@prestashop-core/ui-testing';
import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_modules_getModules';

describe('API : GET /modules', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;
  const jsonResponseItems: ModuleInfo [] = [];

  const clientScope: string = 'module_read';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.API.URL);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  describe('BackOffice : Fetch the access token', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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

    it('should create API Client', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

      const textResult = await addNewApiClientPage.addAPIClient(page, clientData);
      expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

      const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
      expect(textMessage).to.contains(addNewApiClientPage.apiClientGeneratedMessage);
    });

    it('should copy client secret', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

      await addNewApiClientPage.copyClientSecret(page);

      clientSecret = await addNewApiClientPage.getClipboardText(page);
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
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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
        expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it(`should check the JSON Response keys (page ${arg.page})`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkResponseKeys${index}`, baseContext);

        const keys = [
          'totalItems',
          'sortOrder',
          'limit',
          'filters',
          'items',
        ];

        if (arg.page > 0) {
          keys.push('offset');
        }
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
            'version',
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

      await boDashboardPage.goToSubMenu(page, boDashboardPage.modulesParentLink, boDashboardPage.modulesParentLink);
      await boModuleManagerPage.closeSfToolBar(page);
      await moduleManager.filterByStatus(page, 'installed');

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);

      const numModules = await moduleManager.getNumberOfModules(page);
      expect(numModules).to.eq(jsonResponseItems.length);
    });

    it('should filter list by technicaleName', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponseItems.length; idxItem++) {
        // eslint-disable-next-line no-loop-func
        const isModuleVisible = await moduleManager.searchModule(
          page,
          {tag: jsonResponseItems[idxItem].technicalName} as FakerModule,
        );
        expect(isModuleVisible).to.be.equal(true);

        const moduleInfos = await moduleManager.getModuleInformationNth(page, 1);
        expect(moduleInfos.moduleId).to.equal(jsonResponseItems[idxItem].moduleId);
        expect(moduleInfos.technicalName).to.equal(jsonResponseItems[idxItem].technicalName);
        expect(moduleInfos.version).to.equal(jsonResponseItems[idxItem].version);
        expect(moduleInfos.enabled).to.equal(jsonResponseItems[idxItem].enabled);
      }
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
