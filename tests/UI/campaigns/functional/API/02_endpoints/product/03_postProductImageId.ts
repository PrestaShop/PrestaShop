// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import fs from 'fs';
import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  dataLanguages,
  FakerProduct,
  type Page,
  type ProductImageInformation,
  utilsAPI,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_postProductImageId';

describe('API : POST /products/images/{imageId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idProduct: number;
  let productImageInformation: ProductImageInformation;

  const clientScope: string = 'product_write';
  const productImageCreated: string = 'coverCreated.jpg';
  const productImageUpdated: string = 'coverUpdated.jpg';
  const productCaptionEN: string = 'Caption EN';
  const productCaptionFR: string = 'Caption FR';
  const productCaptionUpdatedEN: string = `${productCaptionEN} UPDATED`;
  const productCaptionUpdatedFR: string = `${productCaptionEN} MIS A JOUR`;
  const createProduct: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });

  createProductTest(createProduct, `${baseContext}_preTest_0`);

  describe('POST /products/{productId}/images', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);

      apiContext = await utilsPlaywright.createAPIContext(global.API.URL);

      await utilsFile.generateImage(productImageCreated);
      await utilsFile.generateImage(productImageUpdated);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);

      await utilsFile.deleteFile(productImageCreated);
      await utilsFile.deleteFile(productImageUpdated);
    });

    describe('API : Fetch the access token', async () => {
      it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
        accessToken = await requestAccessToken(clientScope);
      });
    });

    describe('BackOffice : Fetch the ID of the Product', async () => {
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
        await boProductsPage.filterProducts(page, 'product_name', createProduct.name);

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(createProduct.name);

        idProduct = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
        expect(idProduct).to.be.gt(0);
      });

      it('should go to edit product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPage', baseContext);

        await boProductsPage.goToProductPage(page, 1);

        const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);

        const numImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
        expect(numImages).to.be.equals(0);
      });

      it('should add image', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

        await boProductsCreateTabDescriptionPage.addProductImages(page, [productImageCreated]);

        const numOfImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
        expect(numOfImages).to.eq(1);
      });

      it('should set image information', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setImageInformation', baseContext);

        const message = await boProductsCreateTabDescriptionPage.setProductImageInformation(
          page,
          1,
          true,
          productCaptionEN,
          productCaptionFR,
          false,
          true,
          true,
        );
        expect(message).to.be.eq(boProductsCreateTabDescriptionPage.settingUpdatedMessage);

        productImageInformation = await boProductsCreateTabDescriptionPage.getProductImageInformation(page, 1);
      });
    });

    describe('API : Update the Product Image', async () => {
      it('should request the endpoint /products/images/{imageId}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

        const dataMultipart: any = {};
        dataMultipart[`legends[${dataLanguages.english.locale}]`] = productCaptionUpdatedEN;
        dataMultipart[`legends[${dataLanguages.french.locale}]`] = productCaptionUpdatedFR;

        const apiResponse = await apiContext.post(`products/images/${productImageInformation.id}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
            ContentType: 'multipart/form-data',
          },
          multipart: {
            ...dataMultipart,
            image: {
              name: productImageUpdated,
              mimeType: 'image/jpg',
              buffer: fs.readFileSync(productImageUpdated),
            },
          },
        });

        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it('should check the JSON Response keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

        expect(jsonResponse).to.have.all.keys(
          'imageId',
          'imageUrl',
          'thumbnailUrl',
          'legends',
          'cover',
          'position',
          'shopIds',
        );
      });

      it('should check the JSON Response', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

        expect(jsonResponse.imageId).to.be.gt(0);

        expect(jsonResponse.imageUrl).to.be.a('string');

        expect(jsonResponse.thumbnailUrl).to.be.a('string');

        expect(jsonResponse.legends[dataLanguages.english.locale]).to.be.a('string');
        expect(jsonResponse.legends[dataLanguages.english.locale]).to.equals(productCaptionUpdatedEN);
        expect(jsonResponse.legends[dataLanguages.french.locale]).to.be.a('string');
        expect(jsonResponse.legends[dataLanguages.french.locale]).to.equals(productCaptionUpdatedFR);

        expect(jsonResponse.cover).to.be.a('boolean');
        expect(jsonResponse.cover).to.be.equals(true);

        expect(jsonResponse.position).to.be.a('number');
        expect(jsonResponse.position).to.be.equals(1);

        expect(jsonResponse.shopIds).to.be.a('array');
      });
    });

    describe('BackOffice : Check the Product Image is updated', async () => {
      it('should reload the page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'reloadPage', baseContext);

        await boProductsPage.reloadPage(page);

        productImageInformation = await boProductsCreateTabDescriptionPage.getProductImageInformation(page, 1);
      });

      it('should check the JSON Response : `imageId`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseImageId', baseContext);

        expect(productImageInformation.id).to.equal(jsonResponse.imageId);
      });

      it('should check the JSON Response : `legends`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLegends', baseContext);

        expect(productImageInformation.caption.en).to.equal(jsonResponse.legends[dataLanguages.english.locale]);
        expect(productImageInformation.caption.en).to.equal(productCaptionUpdatedEN);

        expect(productImageInformation.caption.fr).to.equal(jsonResponse.legends[dataLanguages.french.locale]);
        expect(productImageInformation.caption.fr).to.equal(productCaptionUpdatedFR);
      });

      it('should check the JSON Response : `cover`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCover', baseContext);

        expect(productImageInformation.isCover).to.equal(jsonResponse.cover);
      });

      it('should check the JSON Response : `position`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponsePosition', baseContext);

        expect(productImageInformation.position).to.equal(jsonResponse.position);
      });
    });
  });

  // Post-condition: Delete a Product
  deleteProductTest(createProduct, `${baseContext}_postTest_0`);
});
