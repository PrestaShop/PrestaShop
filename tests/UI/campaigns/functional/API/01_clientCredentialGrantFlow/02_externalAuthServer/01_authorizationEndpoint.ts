import api from '@utils/api';
import keycloakHelper from '@utils/keycloakHelper';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {APIRequestContext, request} from 'playwright';
import {installKeycloak, createKeycloakClient, uninstallKeycloak} from '@commonTests/API/keycloak';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_externalAuthServer_authorizationEndpoint';

describe('API : Authorization Endpoint', async () => {
  let apiContextBO: APIRequestContext;
  let apiContextKeycloak: APIRequestContext;

  before(async () => {
    apiContextBO = await request.newContext({
      baseURL: global.BO.URL,
      // @todo : Remove it when Puppeteer will accept self signed certificates
      ignoreHTTPSErrors: true,
    });
    apiContextKeycloak = await request.newContext({
      baseURL: 'http://127.0.0.1:8003',
      // @todo : Remove it when Puppeteer will accept self signed certificates
      ignoreHTTPSErrors: true,
    });
  });

  // Pre-Condition: Setup Keycloak
  createKeycloakClient(`${baseContext}_preTest_1`);

  describe('Authorization Endpoint', async () => {
    it('should request the endpoint /admin-dev/api/oauth2/token with method GET', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodGET', baseContext);

      const apiResponse = await apiContextBO.get('api/oauth2/token');
      await expect(apiResponse.status()).to.eq(405);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOST', baseContext);

      const apiResponse = await apiContextBO.post('api/oauth2/token');
      await expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTUnusefulData', baseContext);

      const apiResponse = await apiContextBO.post('api/oauth2/token', {
        form: {
          notUsed: 'notUsed',
        },
      });
      await expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with invalid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTInvalidData', baseContext);

      const apiResponse = await apiContextBO.post('api/oauth2/token', {
        form: {
          client_id: 'bad_client_id',
          client_secret: 'bad_client_secret',
          grant_type: 'client_credentials',
        },
      });
      await expect(apiResponse.status()).to.eq(401);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with valid + unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidAndUnusefulData', baseContext);

      const apiResponse = await apiContextBO.post('api/oauth2/token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
          notUsed: 'notUsed',
        },
      });
      await expect(apiResponse.status()).to.eq(200);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with valid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidData', baseContext);

      const apiResponse = await apiContextBO.post('api/oauth2/token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
        },
      });
      await expect(apiResponse.status()).to.eq(200);
      await expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.be.true;
      await expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      await expect(jsonResponse).to.have.property('token_type');
      await expect(jsonResponse.token_type).to.be.eq('Bearer');
      await expect(jsonResponse).to.have.property('expires_in');
      await expect(jsonResponse.expires_in).to.be.eq(3600);
      await expect(jsonResponse).to.have.property('access_token');
      await expect(jsonResponse.token_type).to.be.a('string');
    });

    it(
      'should request the endpoint /realms/master/protocol/openid-connect/token (Keycloak) with method POST with valid data',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestKeycloakWithMethodPOSTValidData', baseContext);

        const accessTokenKeycloak: string = await keycloakHelper.getClientSecret(apiContextKeycloak);
        const apiResponse = await apiContextKeycloak.post('/realms/master/protocol/openid-connect/token', {
          form: {
            client_id: 'prestashop_client_id',
            client_secret: accessTokenKeycloak,
            grant_type: 'client_credentials',
          },
        });
        await expect(apiResponse.status()).to.eq(200);
        await expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.be.true;
        await expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        await expect(jsonResponse).to.have.property('token_type');
        await expect(jsonResponse.token_type).to.be.eq('Bearer');
        await expect(jsonResponse).to.have.property('expires_in');
        await expect(jsonResponse.expires_in).to.be.eq(60);
        await expect(jsonResponse).to.have.property('access_token');
        await expect(jsonResponse.token_type).to.be.a('string');
      },
    );
  });
});
