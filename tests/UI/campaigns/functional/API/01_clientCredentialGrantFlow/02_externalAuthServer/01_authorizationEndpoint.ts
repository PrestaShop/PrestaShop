import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type APIRequestContext,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_clientCredentialGrantFlow_externalAuthServer_authorizationEndpoint';

describe('API : External Auth Server - Authorization Endpoint', async () => {
  let apiContextKeycloak: APIRequestContext;

  before(async () => {
    apiContextKeycloak = await utilsPlaywright.createAPIContext(global.keycloakConfig.keycloakExternalUrl);
  });

  describe('Authorization Endpoint', async () => {
    it(
      'should request the endpoint /realms/prestashop/protocol/openid-connect/token (Keycloak) with method POST with valid data',
      async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestKeycloakWithMethodPOSTValidData', baseContext);

        const apiResponse = await apiContextKeycloak.post('/realms/prestashop/protocol/openid-connect/token', {
          form: {
            client_id: global.keycloakConfig.keycloakClientId,
            client_secret: global.keycloakConfig.keycloakClientSecret,
            grant_type: 'client_credentials',
          },
        });

        expect(apiResponse.status(), await apiResponse.text()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property('access_token');
        expect(jsonResponse.access_token).to.be.a('string');
        expect(jsonResponse).to.have.property('token_type');
        expect(jsonResponse.token_type).to.be.eq('Bearer');
        expect(jsonResponse).to.have.property('expires_in');
        expect(jsonResponse.expires_in).to.be.a('number');
        // Value should be 300 but if the call took a bit longer it may be 299
        // We don't need check the exact value anyway
        expect(jsonResponse.expires_in).to.be.greaterThan(200);
        expect(jsonResponse).to.have.property('scope');
        expect(jsonResponse.scope).to.be.eq('profile email');
      },
    );
  });
});
