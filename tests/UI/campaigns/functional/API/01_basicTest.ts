// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  type APIRequestContext,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_basicTest';

describe('API : Basic Test', async () => {
  let apiContext: APIRequestContext;

  before(async () => {
    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  describe('Basic Test', async () => {
    it('should request the endpoint /admin-api', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestNewApi', baseContext);

      const apiResponse = await apiContext.get('');
      expect(apiResponse.status()).to.eq(404);
    });

    it('should request the endpoint /hooks', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestMethodNotAllowed', baseContext);

      const apiResponse = await apiContext.put('hooks');
      expect(apiResponse.status()).to.eq(405);
    });
  });
});
