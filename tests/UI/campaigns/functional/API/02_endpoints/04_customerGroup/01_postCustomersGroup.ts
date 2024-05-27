import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_customerGroup_postCustomersGroup';

describe('API : POST /customers/group', async () => {
  // @todo : https://github.com/PrestaShop/PrestaShop/issues/34506
  it('should test the API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'testAPI', baseContext);

    this.skip();
  });
});
