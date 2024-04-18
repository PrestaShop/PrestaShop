import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_hookStatus_putHookStatusId';

describe('API : PUT /hook-status', async () => {
  // @todo : https://github.com/PrestaShop/PrestaShop/issues/34507
  it('should test the API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'testAPI', baseContext);

    this.skip();
  });
});
