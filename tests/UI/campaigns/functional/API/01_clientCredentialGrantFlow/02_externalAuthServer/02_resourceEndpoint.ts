import api from '@utils/api';
import helpers from '@utils/helpers';
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
  BrowserContext, Page,
} from 'playwright';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_externalAuthServer_resourceEndpoint';

// @todo : https://github.com/PrestaShop/PrestaShop/issues/33946
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
      /*
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
        expect(clientSecretKeycloak.length).to.be.gt(0);

        const apiResponse: APIResponse = await apiContextKeycloak.post('realms/master/protocol/openid-connect/token', {
          form: {
            client_id: global.keycloakConfig.keycloakClientId,
            client_secret: clientSecretKeycloak,
            grant_type: 'client_credentials',
          },
        });
        expect(apiResponse.status()).to.eq(200);

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property('access_token');
        expect(jsonResponse.access_token).to.be.a('string');

        accessTokenKeycloak = jsonResponse.access_token;
        accessTokenExpiredKeycloak = api.setAccessTokenAsExpired(accessTokenKeycloak);
      */
    }
  });

  after(async () => {
    await helpers.closeBrowserContext(browserContext);

    if (!global.GENERATE_FAILED_STEPS) {
      /*
      const isRemoved: boolean = await keycloakHelper.removeClient(global.keycloakConfig.keycloakClientId);
      expect(isRemoved).to.eq(true);
      */
    }
  });

  installModule(Modules.keycloak, `${baseContext}_preTest_1`);

  describe('Resource Endpoint', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);

      this.skip();
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      this.skip();

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.modulesParentLink,
        dashboardPage.moduleManagerLink,
      );
      await moduleManagerPage.closeSfToolBar(page);

      const pageTitle = await moduleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
    });

    it(`should search the module '${Modules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      this.skip();

      const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.keycloak);
      expect(isModuleVisible, 'Module is not visible!').to.eq(true);
    });

    it(`should go to the configuration page of the module '${Modules.keycloak.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      this.skip();

      await moduleManagerPage.goToConfigurationPage(page, Modules.keycloak.tag);

      const pageTitle = await keycloakConnectorDemo.getPageTitle(page);
      expect(pageTitle).to.eq(keycloakConnectorDemo.pageTitle);
    });

    it('should define the Keycloak Realm endpoint', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'setKeycloakRealmEndpoint', baseContext);

      this.skip();

      const textResult = await keycloakConnectorDemo.setKeycloakEndpoint(
        page,
        `${global.keycloakConfig.keycloakInternalUrl}/realms/master`,
      );
      expect(textResult).to.be.eq(keycloakConnectorDemo.successfulUpdateMessage);
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 without access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithoutAccessToken', baseContext);

      this.skip();

      const apiResponse = await apiContext.get('api/hook-status/1');
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with invalid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithInvalidAccessToken', baseContext);

      this.skip();

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: 'Bearer INVALIDTOKEN',
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with expired access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithExpiredAccessToken', baseContext);

      this.skip();

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessTokenExpiredKeycloak}`,
        },
      });

      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithValidAccessToken', baseContext);

      this.skip();

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessTokenKeycloak}`,
        },
      });

      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('id');
      expect(jsonResponse.id).to.be.a('number');
      expect(jsonResponse).to.have.property('active');
      expect(jsonResponse.active).to.be.a('boolean');
    });
  });

  uninstallModule(Modules.keycloak, `${baseContext}_postTest_1`);
});
