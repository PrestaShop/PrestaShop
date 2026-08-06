// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boProductsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  FakerProduct,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_deleteProductId';

describe('API : DELETE /products/{productId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let idProduct: number;
  let accessToken: string;

  const clientScope: string = 'product_write';
  const productData: FakerProduct = new FakerProduct({type: 'standard', status: true});

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // Pre-Condition : Create a product
  createProductTest(productData, `${baseContext}_preTest_0`);

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Fetch the ID of the product', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
      await boProductsPage.closeSfToolBar(page);

      const pageTitle = await boProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsPage.pageTitle);
    });

    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForCreation', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', productData.name);

      const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numProducts).to.be.equal(1);

      const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.contains(productData.name);

      idProduct = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
      expect(idProduct).to.be.gt(0);
    });
  });

  describe('API : Delete the Product', async () => {
    it('should request the endpoint /products/{productId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`products/${idProduct}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Product is deleted', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterAfterDeletion', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', productData.name);

      const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numProducts).to.be.equal(0);
    });
  });
});
