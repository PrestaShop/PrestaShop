// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import apiClientPage from 'pages/BO/advancedParameters/APIClient';
import addNewApiClientPage from '@pages/BO/advancedParameters/APIClient/add';
import productsPage from '@pages/BO/catalog/products';
import createProductsPage from '@pages/BO/catalog/products/add';
import descriptionTab from '@pages/BO/catalog/products/add/descriptionTab';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Languages from '@data/demo/languages';
import APIClientData from '@data/faker/APIClient';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_product_patchAPIProductId';

describe('API : PATCH /product/{productId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let idProduct: number;

  const clientScope: string = 'product_write';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createProduct: ProductData = new ProductData({
    type: 'standard',
    status: true,
  });
  const patchProduct: ProductData = new ProductData({
    type: 'virtual',
    status: false,
  });

  // Pre Condition : Create a product
  createProductTest(createProduct, `${baseContext}_preTest_0`);

  describe('API : PATCH /product/{productId}', async () => {
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
        await testContext.addContextItem(this, 'testIdentifier', 'goToAuthorizationServerPage', baseContext);

        await dashboardPage.goToSubMenu(
          page,
          dashboardPage.advancedParametersLink,
          dashboardPage.authorizationServerLink,
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

    describe('BackOffice : Fetch the ID product', async () => {
      it('should go to \'Catalog > Products\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

        await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
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
        await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

        await productsPage.goToProductPage(page, 1);

        const pageTitle: string = await createProductsPage.getPageTitle(page);
        expect(pageTitle).to.contains(createProductsPage.pageTitle);
      });
    });

    [
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35626
      /*{
        propertyName: 'type',
        propertyValue: patchProduct.type,
      },*/
      {
        propertyName: 'active',
        propertyValue: patchProduct.status,
      },
      {
        propertyName: 'names',
        propertyValue: {
          [Languages.english.id]: patchProduct.name,
          [Languages.french.id]: patchProduct.nameFR,
        },
      },
      {
        propertyName: 'descriptions',
        propertyValue: {
          [Languages.english.id]: patchProduct.description,
          [Languages.french.id]: patchProduct.descriptionFR,
        },
      },
    ].forEach((data: { propertyName: string, propertyValue: boolean|string|object}) => {
      describe(`Update the property \`${data.propertyName}\` with API and check in BO`, async () => {
        it('should request the endpoint /product/{productId}', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `requestEndpoint${data.propertyName}`, baseContext);

          const dataPatch: any = {};
          dataPatch[data.propertyName] = data.propertyValue;

          const apiResponse = await apiContext.patch(`product/${idProduct}`, {
            headers: {
              Authorization: `Bearer ${accessToken}`,
            },
            data: dataPatch,
          });
          expect(apiResponse.status()).to.eq(200);
          expect(api.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
          expect(api.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

          const jsonResponse = await apiResponse.json();
          expect(jsonResponse).to.have.property(data.propertyName);
          expect(jsonResponse[data.propertyName]).to.deep.equal(data.propertyValue);
        });

        it(`should check that the property "${data.propertyName}"`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `checkProperty${data.propertyName}`, baseContext);

          await addNewApiClientPage.reloadPage(page);

          if (data.propertyName === 'type') {
            const valueProperty = await createProductsPage.getProductType(page);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'active') {
            const valueProperty = await createProductsPage.getProductStatus(page);
            expect(valueProperty).to.equal(data.propertyValue);
          } else if (data.propertyName === 'names') {
            const valuePropertyEN = await createProductsPage.getProductName(page, Languages.english.isoCode);
            const valuePropertyFR = await createProductsPage.getProductName(page, Languages.french.isoCode);
            expect({
              [Languages.english.id]: valuePropertyEN,
              [Languages.french.id]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          } else if (data.propertyName === 'descriptions') {
            const valuePropertyEN = await descriptionTab.getValue(page, 'description', Languages.english.id.toString());
            const valuePropertyFR = await descriptionTab.getValue(page, 'description', Languages.french.id.toString());
            expect({
              [Languages.english.id]: valuePropertyEN,
              [Languages.french.id]: valuePropertyFR,
            }).to.deep.equal(data.propertyValue);
          }
        });
      });
    });
  });

  // Pre-condition: Delete a product
  deleteProductTest(patchProduct, `${baseContext}_postTest_0`);

  // Pre-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_1`);
});
