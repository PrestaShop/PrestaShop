// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataModules,
  FakerAPIClient,
  type Page,
  utilsCore,
  utilsPlaywright,
  utilsAPI,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_apiresources_installation_disableEnableModule';

describe('PrestaShop API Resources module - Disable/Enable module', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;

  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: ['api_client_read'],
  });

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('BackOffice - Login', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });
  });

  [
    {
      state: false,
      action: 'disable',
    },
    {
      state: true,
      action: 'enable',
    },
  ].forEach((test: {state: boolean, action: string}, index: number) => {
    describe(`${utilsCore.capitalize(test.action)} the module`, async () => {
      it('should go to \'Modules > Module Manager\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToModuleManagerPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.modulesParentLink,
          boDashboardPage.moduleManagerLink,
        );
        await boModuleManagerPage.closeSfToolBar(page);

        const pageTitle = await boModuleManagerPage.getPageTitle(page);
        expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
      });

      it(`should search the module ${dataModules.psApiResources.name}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `searchModule${index}`, baseContext);

        const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psApiResources);
        expect(isModuleVisible).to.eq(true);
      });

      it(`should ${test.action} the module`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.action}Module`, baseContext);

        const successMessage = await boModuleManagerPage.setActionInModule(page, dataModules.psApiResources, test.action);

        if (test.state) {
          expect(successMessage).to.eq(boModuleManagerPage.enableModuleSuccessMessage(dataModules.psApiResources.tag));
        } else {
          expect(successMessage).to.eq(boModuleManagerPage.disableModuleSuccessMessage(dataModules.psApiResources.tag));
        }
      });

      it('should go to \'Advanced Parameters > API Client\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAdminAPIPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await boApiClientsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkThatNoRecordFound${index}`, baseContext);

        const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
        expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New API Client page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToNewAPIClientPage${index}`, baseContext);

        await boApiClientsPage.goToNewAPIClientPage(page);

        const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
      });

      it('should check that scopes from Core are present and enabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkScopesCore${index}`, baseContext);

        const scopes = await boApiClientsCreatePage.getApiScopes(page, '__core_scopes');
        expect(scopes.length).to.be.eq(0);

        // eslint-disable-next-line no-restricted-syntax
        for (const scope of scopes) {
          const isScopeDisabled = await boApiClientsCreatePage.isAPIScopeDisabled(page, scope);
          expect(isScopeDisabled).to.be.equal(false);
        }
      });

      it('should check that scopes from Module are present and enabled', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `checkScopesModule${index}`, baseContext);

        const scopes = await boApiClientsCreatePage.getApiScopes(page, dataModules.psApiResources.tag);

        // eslint-disable-next-line no-restricted-syntax
        for (const scope of scopes) {
          const isScopeDisabled = await boApiClientsCreatePage.isAPIScopeDisabled(page, scope);
          expect(isScopeDisabled).to.be.equal(false);
        }
      });

      it('should create API Client', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAPIClient${index}`, baseContext);

        const textResult = await boApiClientsCreatePage.addAPIClient(page, clientData);
        expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

        const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
        expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
      });

      it('should copy client secret', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `copyClientSecret${index}`, baseContext);

        await boApiClientsCreatePage.copyClientSecret(page);

        clientSecret = await boApiClientsCreatePage.getClipboardText(page);
        expect(clientSecret.length).to.be.gt(0);
      });

      it('should request the endpoint /access_token', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `requestOauth2Token${index}`, baseContext);

        const apiResponse = await apiContext.post('access_token', {
          form: {
            client_id: clientData.clientId,
            client_secret: clientSecret,
            grant_type: 'client_credentials',
            scope: clientData.scopes[0],
          },
        });

        expect(apiResponse.status()).to.eq(test.state ? 200 : 400);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();

        if (test.state) {
          expect(jsonResponse).to.have.property('access_token');
          expect(jsonResponse.access_token).to.be.a('string');
        } else {
          expect(jsonResponse).to.have.property('error');
          expect(jsonResponse.error).to.be.a('string');
          expect(jsonResponse.error).to.equals('invalid_scope');
        }
      });

      it('should go to \'Advanced Parameters > API Client\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToAdminAPIPage${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await boApiClientsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
      });

      it('should delete API Client', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteAPIClient${index}`, baseContext);

        const textResult = await boApiClientsPage.deleteAPIClient(page, 1);
        expect(textResult).to.equal(boApiClientsCreatePage.successfulDeleteMessage);

        const numElements = await boApiClientsPage.getNumberOfElementInGrid(page);
        expect(numElements).to.equal(0);
      });
    });
  });
});
