// Import utils
import api from '@utils/api';
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';

// Import data
import APIClientData from '@data/faker/APIClient';
import {ProductImageInformation} from '@data/types/product';

import {expect} from 'chai';
import fs from 'fs';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataLanguages,
  FakerProduct,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_postProductImageId';

describe('API : POST /product/image/{imageId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let jsonResponse: any;
  let idProduct: number;
  let productImageInformation: ProductImageInformation;

  const clientScope: string = 'product_write';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
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

  describe('POST /product/{productId}/image', async () => {
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);

      apiContext = await helper.createAPIContext(global.API.URL);

      await files.generateImage(productImageCreated);
      await files.generateImage(productImageUpdated);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);

      await files.deleteFile(productImageCreated);
      await files.deleteFile(productImageUpdated);
    });

    describe('BackOffice : Fetch the access token', async () => {
      it('should login in BO', async function () {
        await loginCommon.loginBO(this, page);
      });

      it('should go to \'Advanced Parameters > API Client\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await apiClientPage.getPageTitle(page);
        expect(pageTitle).to.eq(apiClientPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

        const noRecordsFoundText = await apiClientPage.getTextForEmptyTable(page);
        expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New API Client page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

        await apiClientPage.goToNewAPIClientPage(page);

        const pageTitle = await addNewApiClientPage.getPageTitle(page);
        expect(pageTitle).to.eq(addNewApiClientPage.pageTitleCreate);
      });

      it('should create API Client', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

        const textResult = await addNewApiClientPage.addAPIClient(page, clientData);
        expect(textResult).to.contains(addNewApiClientPage.successfulCreationMessage);

        const textMessage = await addNewApiClientPage.getAlertInfoBlockParagraphContent(page);
        expect(textMessage).to.contains(addNewApiClientPage.apiClientGeneratedMessage);
      });

      it('should copy client secret', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

        await addNewApiClientPage.copyClientSecret(page);

        clientSecret = await addNewApiClientPage.getClipboardText(page);
        expect(clientSecret.length).to.be.gt(0);
      });

      it('should request the endpoint /access_token', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

        const apiResponse = await apiContext.post('access_token', {
          form: {
            client_id: clientData.clientId,
            client_secret: clientSecret,
            grant_type: 'client_credentials',
            scope: clientScope,
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        const jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property('access_token');
        expect(jsonResponse.token_type).to.be.a('string');

        accessToken = jsonResponse.access_token;
      });
    });

    describe('BackOffice : Fetch the ID of the Product', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterForCreation', baseContext);

        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'product_name', createProduct.name);

        const numProducts = await productsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await productsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(createProduct.name);

        idProduct = parseInt((await productsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
        expect(idProduct).to.be.gt(0);
      });

      it('should go to edit product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPage', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle: string = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);

        const numImages = await descriptionTab.getNumberOfImages(page);
        expect(numImages).to.be.equals(0);
      });

      it('should add image', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addImage', baseContext);

        await descriptionTab.addProductImages(page, [productImageCreated]);

        const numOfImages = await descriptionTab.getNumberOfImages(page);
        expect(numOfImages).to.eq(1);
      });

      it('should set image information', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'setImageInformation', baseContext);

        const message = await descriptionTab.setProductImageInformation(
          page,
          1,
          true,
          productCaptionEN,
          productCaptionFR,
          false,
          true,
          true,
        );
        expect(message).to.be.eq(descriptionTab.settingUpdatedMessage);

        productImageInformation = await descriptionTab.getProductImageInformation(page, 1);
      });
    });

    describe('API : Update the Product Image', async () => {
      it('should request the endpoint /product/image/{imageId}', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

        const dataMultipart: any = {};
        dataMultipart[`legends[${dataLanguages.english.id}]`] = productCaptionUpdatedEN;
        dataMultipart[`legends[${dataLanguages.french.id}]`] = productCaptionUpdatedFR;

        const apiResponse = await apiContext.post(`product/image/${productImageInformation.id}`, {
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
        expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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

        expect(jsonResponse.legends[dataLanguages.english.id]).to.be.a('string');
        expect(jsonResponse.legends[dataLanguages.english.id]).to.equals(productCaptionUpdatedEN);
        expect(jsonResponse.legends[dataLanguages.french.id]).to.be.a('string');
        expect(jsonResponse.legends[dataLanguages.french.id]).to.equals(productCaptionUpdatedFR);

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

        await productsPage.reloadPage(page);

        productImageInformation = await descriptionTab.getProductImageInformation(page, 1);
      });

      it('should check the JSON Response : `imageId`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseImageId', baseContext);

        expect(productImageInformation.id).to.equal(jsonResponse.imageId);
      });

      it('should check the JSON Response : `legends`', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseLegends', baseContext);

        expect(productImageInformation.caption.en).to.equal(jsonResponse.legends[dataLanguages.english.id]);
        expect(productImageInformation.caption.en).to.equal(productCaptionUpdatedEN);

        expect(productImageInformation.caption.fr).to.equal(jsonResponse.legends[dataLanguages.french.id]);
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

  // Post-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_1`);
});
