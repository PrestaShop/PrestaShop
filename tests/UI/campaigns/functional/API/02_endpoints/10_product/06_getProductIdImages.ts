// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataLanguages,
  dataProducts,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_getProductIdImages';

describe('API : GET /product/{productId}/images', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let jsonResponse: any;

  const clientScope: string = 'product_read';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

  describe('GET /product/{productId}/images', async () => {
    before(async function () {
      browserContext = await helper.createBrowserContext(this.browser);
      page = await helper.newTab(browserContext);

      apiContext = await helper.createAPIContext(global.API.URL);
    });

    after(async () => {
      await helper.closeBrowserContext(browserContext);
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

    describe('API : Create the Product Image', async () => {
      it('should request the endpoint /product/{productId}/image', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

        const apiResponse = await apiContext.get(`product/${dataProducts.demo_1.id}/images`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
        });

        expect(apiResponse.status()).to.eq(200);
        expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
      });

      it('should check the JSON Response keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);

        expect(jsonResponse.length).to.be.gt(0);

        for (let i:number = 0; i < jsonResponse.length; i++) {
          expect(jsonResponse[i]).to.have.all.keys(
            'imageId',
            'imageUrl',
            'thumbnailUrl',
            'legends',
            'cover',
            'position',
            'shopIds',
          );

          expect(jsonResponse[i].imageId).to.be.gt(0);
          expect(jsonResponse[i].imageUrl).to.be.a('string');
          expect(jsonResponse[i].thumbnailUrl).to.be.a('string');
          expect(jsonResponse[i].legends[dataLanguages.english.id]).to.be.a('string');
          expect(jsonResponse[i].legends[dataLanguages.french.id]).to.be.a('string');
          expect(jsonResponse[i].cover).to.be.a('boolean');
          expect(jsonResponse[i].position).to.be.a('number');
          expect(jsonResponse[i].shopIds).to.be.a('array');
        }
      });
    });

    describe('BackOffice : Check the Product Images', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await boDashboardPage.goToSubMenu(page, boDashboardPage.catalogParentLink, boDashboardPage.productsLink);
        await productsPage.closeSfToolBar(page);

        const pageTitle = await productsPage.getPageTitle(page);
        expect(pageTitle).to.contains(productsPage.pageTitle);
      });

      it('should filter list by name', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'filterProduct', baseContext);

        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'product_name', dataProducts.demo_1.name);

        const numProducts = await productsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await productsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(dataProducts.demo_1.name);
      });

      it('should go to edit product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPage', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle: string = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);

        const numImages = await descriptionTab.getNumberOfImages(page);
        expect(numImages).to.be.equals(jsonResponse.length);
      });

      it('should fetch images informations', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

        for (let idxItem: number = 0; idxItem < jsonResponse.length; idxItem++) {
          const productImageInformation = await descriptionTab.getProductImageInformation(page, idxItem + 1);

          expect(productImageInformation.id).to.equal(jsonResponse[idxItem].imageId);

          expect(productImageInformation.caption.en).to.equal(jsonResponse[idxItem].legends[dataLanguages.english.id]);
          expect(productImageInformation.caption.fr).to.equal(jsonResponse[idxItem].legends[dataLanguages.french.id]);

          expect(productImageInformation.isCover).to.equal(jsonResponse[idxItem].cover);

          expect(productImageInformation.position).to.equal(jsonResponse[idxItem].position);
        }
      });
    });
  });

  // Post-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_0`);
});
