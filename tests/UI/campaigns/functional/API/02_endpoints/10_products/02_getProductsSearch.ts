import testContext from '@utils/testContext';

const baseContext: string = 'functional_API_endpoints_products_getProductsSearch';

describe('API : GET /products/search/{phrase}/{resultsLimit}/{isoCode}', async () => {
  // @todo : https://github.com/PrestaShop/PrestaShop/issues/34486
  it('should test the API', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'testAPI', baseContext);

    this.skip();
  });
});
