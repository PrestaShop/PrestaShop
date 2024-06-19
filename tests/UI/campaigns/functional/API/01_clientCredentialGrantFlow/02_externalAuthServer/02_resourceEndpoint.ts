import testContext from '@utils/testContext';

import loginCommon from '@commonTests/BO/loginBO';
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import {installModule, uninstallModule} from '@commonTests/BO/modules/moduleManager';

import keycloakConnectorDemo from '@pages/BO/modules/keycloakConnectorDemo';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import {
  APIRequestContext, APIResponse, BrowserContext, Page,
} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_externalAuthServer_resourceEndpoint';

describe('API : External Auth Server - Resource Endpoint', async () => {
  // Browser
  let browserContext: BrowserContext;
  let page: Page;
  // API
  let apiContext: APIRequestContext;
  let accessTokenKeycloak: string;
  let accessTokenExpiredKeycloak: string;
  let dynamicApiClientId: number;
  const allowedIssuers = [
    `${global.keycloakConfig.keycloakExternalUrl}/realms/prestashop`,
    `${global.keycloakConfig.keycloakInternalUrl}/realms/prestashop`,
  ];

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);

    if (!global.GENERATE_FAILED_STEPS) {
      const apiContextKeycloak: APIRequestContext = await utilsPlaywright
        .createAPIContext(global.keycloakConfig.keycloakExternalUrl);
      const apiResponse: APIResponse = await apiContextKeycloak.post('realms/prestashop/protocol/openid-connect/token', {
        form: {
          client_id: global.keycloakConfig.keycloakClientId,
          client_secret: global.keycloakConfig.keycloakClientSecret,
          grant_type: 'client_credentials',
          scope: 'api_client_read',
        },
      });
      expect(apiResponse.status(), await apiResponse.text()).to.eq(200);

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.access_token).to.be.a('string');
      expect(jsonResponse).to.have.property('token_type');
      expect(jsonResponse.token_type).to.be.eq('Bearer');
      expect(jsonResponse).to.have.property('expires_in');
      expect(jsonResponse.expires_in).to.be.eq(300);
      expect(jsonResponse).to.have.property('scope');
      expect(jsonResponse.scope).to.be.eq('api_client_read profile email');

      accessTokenKeycloak = jsonResponse.access_token;
      accessTokenExpiredKeycloak = utilsAPI.setAccessTokenAsExpired(accessTokenKeycloak);
    }
  });

  installModule(dataModules.keycloak, `${baseContext}_preTest_1`);

  describe('Resource Endpoint', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

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

    it(`should search the module '${dataModules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);
      const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.keycloak);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);
      await moduleManagerPage.goToConfigurationPage(page, dataModules.keycloak.tag);

      const pageTitle = await keycloakConnectorDemo.getPageTitle(page);
      expect(pageTitle).to.eq(keycloakConnectorDemo.pageTitle);
    });

    it('should define the Keycloak Realm endpoint', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setKeycloakRealmEndpoint', baseContext);
      // The module will use the internal URL (accessible in docker container) as the realm url,
      // but it defines two allowed issuers the internal URL and the external one (used by the host to create
      // the initial access token)
      const textResult = await keycloakConnectorDemo.setKeycloakEndpoint(
        page,
        `${global.keycloakConfig.keycloakInternalUrl}/realms/prestashop`,
        allowedIssuers,
      );
      expect(textResult).to.be.eq(keycloakConnectorDemo.successfulUpdateMessage);
    });

    it('should request the endpoint /api-clients without access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithoutAccessToken', baseContext);
      const apiResponse = await apiContext.get('api-clients');
      expect(apiResponse.status()).to.eq(401);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /api-clients with invalid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithInvalidAccessToken', baseContext);
      const apiResponse = await apiContext.get('api-clients', {
        headers: {
          Authorization: 'Bearer INVALIDTOKEN',
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /api-clients with expired access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithExpiredAccessToken', baseContext);
      const apiResponse = await apiContext.get('api-clients', {
        headers: {
          Authorization: `Bearer ${accessTokenExpiredKeycloak}`,
        },
      });

      expect(apiResponse.status()).to.eq(401);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /api-clients with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestListEndpointWithValidAccessToken', baseContext);

      // Get the whole list of api clients, there should be only one that was automatically added when the external
      // access token was used
      const apiResponse = await apiContext.get('api-clients', {
        headers: {
          Authorization: `Bearer ${accessTokenKeycloak}`,
        },
      });

      expect(apiResponse.status(), await apiResponse.text()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('totalItems');
      expect(jsonResponse.totalItems).to.be.a('number');
      expect(jsonResponse.items).to.be.a('array');
      const apiClient = jsonResponse.items[0];
      expect(apiClient).to.have.property('apiClientId');
      expect(apiClient.apiClientId).to.be.a('number');
      expect(apiClient).to.have.property('clientId');
      expect(apiClient.clientId).to.be.a('string');
      expect(apiClient.clientId).to.eq('prestashop-keycloak');
      expect(apiClient).to.have.property('clientName');
      expect(apiClient.clientName).to.be.a('string');
      expect(apiClient.clientName).to.eq('prestashop-keycloak');
      expect(apiClient).to.have.property('description');
      expect(apiClient.description).to.be.a('string');
      expect(apiClient.description).to.eq('');
      expect(apiClient).to.have.property('enabled');
      expect(apiClient.enabled).to.be.a('boolean');
      expect(apiClient.enabled).to.eq(true);
      expect(apiClient).to.have.property('lifetime');
      expect(apiClient.lifetime).to.be.a('number');
      expect(apiClient.lifetime).to.eq(3600);
      expect(apiClient.externalIssuer).to.be.a('string');
      expect(allowedIssuers).to.include(apiClient.externalIssuer);

      // Use dynamic ID because some other data may have been created before and the ID incremented already
      dynamicApiClientId = apiClient.apiClientId;
    });

    it('should request the endpoint /api-client/{apiClientId} with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestSingleEndpointWithValidAccessToken', baseContext);

      const apiResponse = await apiContext.get(`api-client/${dynamicApiClientId}`, {
        headers: {
          Authorization: `Bearer ${accessTokenKeycloak}`,
        },
      });

      expect(apiResponse.status(), await apiResponse.text()).to.eq(200);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('apiClientId');
      expect(jsonResponse.apiClientId).to.be.a('number');
      expect(jsonResponse.apiClientId).to.eq(dynamicApiClientId);
      expect(jsonResponse).to.have.property('clientId');
      expect(jsonResponse.clientId).to.be.a('string');
      expect(jsonResponse.clientId).to.eq('prestashop-keycloak');
      expect(jsonResponse).to.have.property('clientName');
      expect(jsonResponse.clientName).to.be.a('string');
      expect(jsonResponse.clientName).to.eq('prestashop-keycloak');
      expect(jsonResponse).to.have.property('description');
      expect(jsonResponse.description).to.be.a('string');
      expect(jsonResponse.description).to.eq('');
      expect(jsonResponse).to.have.property('externalIssuer');
      expect(jsonResponse.externalIssuer).to.be.a('string');
      expect(allowedIssuers).to.include(jsonResponse.externalIssuer);
      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.eq(true);
      expect(jsonResponse).to.have.property('lifetime');
      expect(jsonResponse.lifetime).to.be.a('number');
      expect(jsonResponse.lifetime).to.eq(3600);
      expect(jsonResponse).to.have.property('scopes');
      expect(jsonResponse.scopes).to.be.a('array');
      expect(jsonResponse.scopes).to.deep.equal([]);
    });
  });

  deleteAPIClientTest(`${baseContext}_postTest_0`);
  uninstallModule(dataModules.keycloak, `${baseContext}_postTest_1`);
});
