// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {request, APIRequestContext} from 'playwright';

const baseContext: string = 'functional_API_basicTest';

describe('API : Basic Test', async () => {
  let apiContext: APIRequestContext;

  before(async () => {
    apiContext = await request.newContext({
      baseURL: global.BO.URL,
      // @todo : Remove it when Puppeteer will accept self signed certificates
      ignoreHTTPSErrors: true,
    });
  });

  describe('Basic Test', async () => {
    it('should request the endpoint /admin-dev/new-api/', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestNewApi', baseContext);

      const apiResponse = await apiContext.get('new-api/');
      await expect(apiResponse.status()).to.eq(404);
    });

    it('should request the endpoint /admin-dev/new-api/hook-status', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestNewApiHookStatus', baseContext);

      const apiResponse = await apiContext.get('new-api/hook-status');
      await expect(apiResponse.status()).to.eq(404);
    });
  });
});
