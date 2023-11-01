import api from '@utils/api';
import helpers from '@utils/helpers';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {APIRequestContext} from 'playwright';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_internalAuthServer_resourceEndpoint';

describe('API : Internal Auth Server - Resource Endpoint', async () => {
  let apiContext: APIRequestContext;
  let accessToken: string;
  let accessTokenExpired: string;

  before(async () => {
    apiContext = await helpers.createAPIContext(global.BO.URL);
  });

  describe('Resource Endpoint', async () => {
    it('should request the authorization endpoint and fetch valid token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestAuthorizationEndpoint', baseContext);

      // @todo : https://github.com/PrestaShop/PrestaShop/issues/34297
      const apiResponse = await apiContext.post('api/oauth2/token', {
        form: {
          client_id: 'my_client_id',
          client_secret: 'prestashop',
          grant_type: 'client_credentials',
        },
      });
      expect(apiResponse.status()).to.eq(200);

      const jsonResponse = await apiResponse.json();
      expect(jsonResponse).to.have.property('access_token');
      expect(jsonResponse.access_token).to.be.a('string');

      accessToken = jsonResponse.access_token;
      accessTokenExpired = api.setAccessTokenAsExpired(accessToken);
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 without access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithoutAccessToken', baseContext);

      const apiResponse = await apiContext.get('api/hook-status/1');
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with invalid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithInvalidAccessToken', baseContext);

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

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessTokenExpired}`,
        },
      });
      expect(apiResponse.status()).to.eq(401);
      expect(api.hasResponseHeader(apiResponse, 'WWW-Authenticate')).to.eq(true);
      expect(api.getResponseHeader(apiResponse, 'WWW-Authenticate')).to.be.eq('Bearer');
    });

    it('should request the endpoint /admin-dev/api/hook-status/1 with valid access token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointWithValidAccessToken', baseContext);

      const apiResponse = await apiContext.get('api/hook-status/1', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
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
});
