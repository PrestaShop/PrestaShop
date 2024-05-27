import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_hooks_getHooks';

describe('API : GET /hooks', async () => {
  // @todo : https://github.com/PrestaShop/PrestaShop/issues/35616
  it('should test the API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'testAPI', baseContext);

    this.skip();
  });
});
