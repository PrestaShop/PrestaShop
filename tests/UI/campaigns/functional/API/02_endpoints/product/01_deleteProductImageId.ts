// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  dataProducts,
  type Page,
  utilsPlaywright,
  utilsFile,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_deleteProductImageId';

describe('API : DELETE /admin-api/products/images/{imageId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let imageId: number = 1;

  const clientScope: string = 'product_write';
  const productCoverImage: string = 'productCoverImage.png';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);

    await utilsFile.generateImage(productCoverImage);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);

    await utilsFile.deleteFile(productCoverImage);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Fetch the Product Image ID', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'filterProduct', baseContext);

      await boProductsPage.resetFilter(page);
      await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_1.name);

      const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numProducts).to.be.equal(1);

      const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.contains(dataProducts.demo_1.name);
    });

    it('should go to edit product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPage', baseContext);

      await boProductsPage.goToProductPage(page, 1);

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);

      const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
      expect(numOfImages).to.eq(2);
    });

    it('should add image', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

      await boProductsCreateTabDescriptionPage.addProductImages(page, [productCoverImage]);

      const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
      expect(numOfImages).to.eq(3);
    });

    it('should fetch images informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      const productImageInformation = await boProductsCreateTabDescriptionPage.getProductImageInformation(page, 3);

      expect(productImageInformation.id).to.gt(0);

      imageId = productImageInformation.id;
    });
  });

  describe('API : Delete the Product Image', async () => {
    it('should request the endpoint /products/images/{imageId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.delete(`products/images/${imageId}`, {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
      });
      expect(apiResponse.status()).to.eq(204);
    });
  });

  describe('BackOffice : Check the Product Image is deleted', async () => {
    it('should check the image is deleted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkImageDeleted', baseContext);

      await boProductsCreateTabDescriptionPage.reloadPage(page);

      const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
      expect(numOfImages).to.eq(2);
    });
  });
});
