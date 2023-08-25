// Import utils
import helpers from '@utils/helpers';
import testContext from '@utils/testContext';

import {expect} from 'chai';
import type {APIRequestContext} from 'playwright';

const baseContext: string = 'functional_API_basicTest';

describe('API : Basic Test', async () => {
  let apiContext: APIRequestContext;

  before(async () => {
    apiContext = await helpers.createAPIContext(global.BO.URL);
  });

  describe('Basic Test', async () => {
    it('should request the endpoint /admin-dev/api/', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestNewApi', baseContext);

      const apiResponse = await apiContext.get('api/');
      await expect(apiResponse.status()).to.eq(404);
    });

    it('should request the endpoint /admin-dev/api/hook-status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestNewApiHookStatus', baseContext);

      const apiResponse = await apiContext.get('api/hook-status');
      await expect(apiResponse.status()).to.eq(405);
    });
  });
});
