// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  boApiClientsPage,
  type APIRequestContext,
  boApiClientsCreatePage,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  dataLanguages,
  dataProducts,
  FakerAPIClient,
  type Page,
  utilsAPI,
  utilsPlaywright,
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
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

  describe('GET /product/{productId}/images', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);

      apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    describe('BackOffice : Fetch the access token', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Advanced Parameters > API Client\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAdminAPIPage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.adminAPILink,
        );

        const pageTitle = await boApiClientsPage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsPage.pageTitle);
      });

      it('should check that no records found', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatNoRecordFound', baseContext);

        const noRecordsFoundText = await boApiClientsPage.getTextForEmptyTable(page);
        expect(noRecordsFoundText).to.contains('warning No records found');
      });

      it('should go to add New API Client page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToNewAPIClientPage', baseContext);

        await boApiClientsPage.goToNewAPIClientPage(page);

        const pageTitle = await boApiClientsCreatePage.getPageTitle(page);
        expect(pageTitle).to.eq(boApiClientsCreatePage.pageTitleCreate);
      });

      it('should create API Client', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createAPIClient', baseContext);

        const textResult = await boApiClientsCreatePage.addAPIClient(page, clientData);
        expect(textResult).to.contains(boApiClientsCreatePage.successfulCreationMessage);

        const textMessage = await boApiClientsCreatePage.getAlertInfoBlockParagraphContent(page);
        expect(textMessage).to.contains(boApiClientsCreatePage.apiClientGeneratedMessage);
      });

      it('should copy client secret', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'copyClientSecret', baseContext);

        await boApiClientsCreatePage.copyClientSecret(page);

        clientSecret = await boApiClientsCreatePage.getClipboardText(page);
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
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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
          expect(jsonResponse[i].legends[dataLanguages.english.locale]).to.be.a('string');
          expect(jsonResponse[i].legends[dataLanguages.french.locale]).to.be.a('string');
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

        const numImages = await boProductsCreateTabDescriptionPage.getNumberOfImages(page);
        expect(numImages).to.be.equals(jsonResponse.length);
      });

      it('should fetch images informations', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

        for (let idxItem: number = 0; idxItem < jsonResponse.length; idxItem++) {
          const productImageInformation = await boProductsCreateTabDescriptionPage.getProductImageInformation(page, idxItem + 1);

          expect(productImageInformation.id).to.equal(jsonResponse[idxItem].imageId);

          expect(productImageInformation.caption.en).to.equal(jsonResponse[idxItem].legends[dataLanguages.english.locale]);
          expect(productImageInformation.caption.fr).to.equal(jsonResponse[idxItem].legends[dataLanguages.french.locale]);

          expect(productImageInformation.isCover).to.equal(jsonResponse[idxItem].cover);

          expect(productImageInformation.position).to.equal(jsonResponse[idxItem].position);
        }
      });
    });
  });

  // Post-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_0`);
});
