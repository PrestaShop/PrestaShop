import api from '@utils/api';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {APIRequestContext, request} from 'playwright';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_externalAuthServer_authorizationEndpoint';

// @todo : https://github.com/PrestaShop/PrestaShop/issues/33946
describe('API : External Auth Server - Authorization Endpoint', async () => {
  let apiContextBO: APIRequestContext;
  let apiContextKeycloak: APIRequestContext;
  let accessTokenKeycloak: string;

  before(async () => {
    apiContextBO = await request.newContext({
      baseURL: global.BO.URL,
      // @todo : Remove it when Puppeteer will accept self signed certificates
      ignoreHTTPSErrors: true,
    });
    if (!global.GENERATE_FAILED_STEPS) {
      /*
      apiContextKeycloak = await request.newContext({
        baseURL: global.keycloakConfig.keycloakExternalUrl,
        // @todo : Remove it when Puppeteer will accept self signed certificates
        ignoreHTTPSErrors: true,
      });

      accessTokenKeycloak = await keycloakHelper.createClient(
        global.keycloakConfig.keycloakClientId,
        'PrestaShop Client ID',
        false,
        true,
      );
      expect(accessTokenKeycloak.length).to.be.gt(0);
      */
    }
  });

  after(async () => {
    if (!global.GENERATE_FAILED_STEPS) {
      /*
      const isRemoved: boolean = await keycloakHelper.removeClient(global.keycloakConfig.keycloakClientId);
      expect(isRemoved).to.eq(true);
      */
    }
  });

  describe('Authorization Endpoint', async () => {
    it('should request the endpoint /access_token with method GET', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodGET', baseContext);

      this.skip();

      const apiResponse = await apiContextBO.get('access_token');
      expect(apiResponse.status()).to.eq(405);
    });

    it('should request the endpoint /access_token with method POST', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOST', baseContext);

      this.skip();

      const apiResponse = await apiContextBO.post('access_token');
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /access_token with method POST with unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTUnusefulData', baseContext);

      this.skip();

      const apiResponse = await apiContextBO.post('access_token', {
        form: {
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /access_token with method POST with invalid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTInvalidData', baseContext);

      this.skip();

      const apiResponse = await apiContextBO.post('access_token', {
        form: {
          client_id: 'bad_client_id',
          client_secret: 'bad_client_secret',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(401);
    });

    it('should request the endpoint /access_token with method POST with valid + unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidAndUnusefulData', baseContext);

      this.skip();

      const apiResponse = await apiContextBO.post('access_token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(200);
    });

    it('should request the endpoint /access_token with method POST with valid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidData', baseContext);

      this.skip();

      const apiResponse = await apiContextBO.post('access_token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(200);
      expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('token_type');
      expect(jsonResponse.token_type).to.be.eq('Bearer');
      expect(jsonResponse).to.have.property('expires_in');
      expect(jsonResponse.expires_in).to.be.eq(3600);
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.token_type).to.be.a('string');
    });

    it(
      'should request the endpoint /realms/master/protocol/openid-connect/token (Keycloak) with method POST with valid data',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestKeycloakWithMethodPOSTValidData', baseContext);

        this.skip();

        const apiResponse = await apiContextKeycloak.post('/realms/master/protocol/openid-connect/token', {
          form: {
            client_id: global.keycloakConfig.keycloakClientId,
            client_secret: accessTokenKeycloak,
            grant_type: 'client_credentials',
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property('token_type');
        expect(jsonResponse.token_type).to.be.eq('Bearer');
        expect(jsonResponse).to.have.property('expires_in');
        expect(jsonResponse.expires_in).to.be.eq(60);
        expect(jsonResponse).to.have.property('access_token');
        expect(jsonResponse.token_type).to.be.a('string');
      },
    );
  });
});
