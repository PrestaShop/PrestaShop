import api from '@utils/api';
import helpers from '@utils/helpers';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {APIRequestContext} from 'playwright';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_internalAuthServer_authorizationEndpoint';

describe('API : Internal Auth Server - Authorization Endpoint', async () => {
  let apiContext: APIRequestContext;

  before(async () => {
    apiContext = await helpers.createAPIContext(global.BO.URL);
  });

  describe('Authorization Endpoint', async () => {
    it('should request the endpoint /admin-dev/api/oauth2/token with method GET', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodGET', baseContext);

      const apiResponse = await apiContext.get('api/oauth2/token');
      expect(apiResponse.status()).to.eq(405);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOST', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token');
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTUnusefulData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(400);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with invalid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTInvalidData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: 'bad_client_id',
          client_secret: 'bad_client_secret',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(401);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with valid + unuseful data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidAndUnusefulData', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
          notUsed: 'notUsed',
        },
      });
      expect(apiResponse.status()).to.eq(200);
    });

    it('should request the endpoint /admin-dev/api/oauth2/token with method POST with valid data', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthWithMethodPOSTValidData', baseContext);

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34297
      const apiResponse = await apiContext.post('api/oauth2/token', {
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
  });
});
