import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_cartRule_putCartRuleId';

describe('API : PUT /cart-rule', async () => {
  // @todo : https://github.com/PrestaShop/PrestaShop/issues/34505
  it('should test the API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'testAPI', baseContext);

    this.skip();
  });
});
