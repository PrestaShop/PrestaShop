import api from '@utils/api';
import helpers from '@utils/helpers';
import keycloakHelper from '@utils/keycloakHelper';
import testContext from '@utils/testContext';

import loginCommon from '@commonTests/BO/loginBO';
import {installModule, uninstallModule} from '@commonTests/BO/modules/moduleManager';

import dashboardPage from '@pages/BO/dashboard';
import keycloakConnectorDemo from '@pages/BO/modules/keycloakConnectorDemo';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {APIRequestContext} from 'playwright';
import {
  APIResponse, BrowserContext, Page, request,
} from 'playwright';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_externalAuthServer_resourceEndpoint';

describe('API : External Auth Server - Resource Endpoint', async () => {
  // Browser
  let browserContext: BrowserContext;
  let page: Page;
  // API
  let apiContext: APIRequestContext;
  let accessTokenKeycloak: string;
  let accessTokenExpiredKeycloak: string;

  before(async function () {
    browserContext = await helpers.createBrowserContext(this.browser);
    page = await helpers.newTab(browserContext);

    apiContext = await helpers.createAPIContext(global.BO.URL);

    if (!global.GENERATE_FAILED_STEPS) {
      const apiContextKeycloak: APIRequestContext = await request.newContext({
        baseURL: global.keycloakConfig.keycloakExternalUrl,
        // @todo : Remove it when Puppeteer will accept self signed certificates
        ignoreHTTPSErrors: true,
      });

      const clientSecretKeycloak: string = await keycloakHelper.createClient(
        global.keycloakConfig.keycloakClientId,
        'PrestaShop Client ID',
        false,
        true,
      );
      await expect(clientSecretKeycloak.length).to.be.gt(0);

      const apiResponse: APIResponse = await apiContextKeycloak.post('realms/master/protocol/openid-connect/token', {
        form: {
          client_id: global.keycloakConfig.keycloakClientId,
          client_secret: clientSecretKeycloak,
          grant_type: 'client_credentials',
        },
      });
      await expect(apiResponse.status()).to.eq(200);

      const jsonResponse = await apiResponse.json();
      await expect(jsonResponse).to.have.property('access_token');
      await expect(jsonResponse.access_token).to.be.a('string');

      accessTokenKeycloak = jsonResponse.access_token;
      accessTokenExpiredKeycloak = api.setAccessTokenAsExpired(accessTokenKeycloak);
    }
  });

  after(async () => {
    await helpers.closeBrowserContext(browserContext);

    if (!global.GENERATE_FAILED_STEPS) {
      const isRemoved: boolean = await keycloakHelper.removeClient(global.keycloakConfig.keycloakClientId);
      await expect(isRemoved).to.be.true;
    }
  });

  installModule(Modules.keycloak, `${baseContext}_preTest_1`);

  describe('Resource Endpoint', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module '${Modules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.keycloak);
      await expect(isModuleVisible, 'Module is not visible!').to.be.true;
    });

    it(`should go to the configuration page of the module '${Modules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await moduleManagerPage.goToConfigurationPage(page, Modules.keycloak.tag);

      const pageTitle = await keycloakConnectorDemo.getPageTitle(page);
      await expect(pageTitle).to.eq(keycloakConnectorDemo.pageTitle);
    });

    it('should define the Keycloak Realm endpoint', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setKeycloakRealmEndpoint', baseContext);

      const textResult = await keycloakConnectorDemo.setKeycloakEndpoint(
        page,
        `${global.keycloakConfig.keycloakInternalUrl}/realms/master`,
      );
      await expect(textResult).to.be.eq(keycloakConnectorDemo.successfulUpdateMessage);
    });

    it('should request the endpoint /admin-dev/new-api/hook-status/1 without access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithoutAccessToken', baseContext);

      const apiResponse = await apiContext.get('new-api/hook-status/1');
      await expect(apiResponse.status()).to.eq(401);
      await expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.true;
      await expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/new-api/hook-status/1 with invalid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithInvalidAccessToken', baseContext);

      const apiResponse = await apiContext.get('new-api/hook-status/1', {
        headers: {
          Authorization: 'Bearer INVALIDTOKEN',
        },
      });
      await expect(apiResponse.status()).to.eq(401);
      await expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.true;
      await expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/new-api/hook-status/1 with expired access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithExpiredAccessToken', baseContext);

      const apiResponse = await apiContext.get('new-api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessTokenExpiredKeycloak}`,
        },
      });
      console.log((await apiResponse.body()).toString());
      console.log(apiResponse.headersArray());

      await expect(apiResponse.status()).to.eq(401);
      await expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.true;
      await expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/new-api/hook-status/1 with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithValidAccessToken', baseContext);

      const apiResponse = await apiContext.get('new-api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessTokenKeycloak}`,
        },
      });
      console.log((await apiResponse.body()).toString());
      console.log(apiResponse.headersArray());

      await expect(apiResponse.status()).to.eq(200);
      await expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.be.true;
      await expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/ld+json');

      const jsonResponse = await apiResponse.json();
      await expect(jsonResponse).to.have.property('id');
      await expect(jsonResponse.id).to.be.a('number');
      await expect(jsonResponse).to.have.property('active');
      await expect(jsonResponse.active).to.be.a('boolean');
    });
  });

  uninstallModule(Modules.keycloak, `${baseContext}_postTest_1`);
});
