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
import dashboardPage from '@pages/BO/dashboard';

// Import data
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_productList_getAPIProducts';

describe('API : GET /api/products', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'product_read';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    apiContext = await helper.createAPIContext(global.BO.URL);
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

    it('should request the endpoint /admin-dev/api/oauth2/token', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);

      const apiResponse = await apiContext.post('api/oauth2/token', {
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

  describe('API : Fetch Data', async () => {
    it('should request the endpoint /admin-dev/api/products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get('api/products', {
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
      expect(jsonResponse).to.have.all.keys(
        'totalItems',
        'orderBy',
        'sortOrder',
        'limit',
        'filters',
        'items',
      );

      expect(jsonResponse.totalItems).to.be.gt(0);

      for (let i:number = 0; i < jsonResponse.totalItems; i++) {
        expect(jsonResponse.items[i]).to.have.all.keys(
          'productId',
          'active',
          'name',
          'quantity',
          'price',
          'category',
        );
      }
    });
  });

  describe('BackOffice : Expected data', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);
      await productsPage.closeSfToolBar(page);
      await productsPage.resetFilter(page);

      const pageTitle = await productsPage.getPageTitle(page);
      expect(pageTitle).to.contains(productsPage.pageTitle);

      const numProducts = await productsPage.getNumberOfProductsFromHeader(page);
      expect(numProducts).to.eq(jsonResponse.totalItems);
    });

    it('should filter list by id', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkJSONItems', baseContext);

      for (let idxItem: number = 0; idxItem < jsonResponse.totalItems; idxItem++) {
        // eslint-disable-next-line no-loop-func
        await productsPage.resetFilter(page);
        await productsPage.filterProducts(page, 'id_product', {
          min: jsonResponse.items[idxItem].productId,
          max: jsonResponse.items[idxItem].productId,
        });

        const numProducts = await productsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productId = parseInt((await productsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
        expect(productId).to.equal(jsonResponse.items[idxItem].productId);

        const productActive = await productsPage.getTextColumn(page, 'active', 1);
        expect(productActive).to.equal(jsonResponse.items[idxItem].active);

        const productName = await productsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.equal(jsonResponse.items[idxItem].name);

        const productQuantity = await productsPage.getTextColumn(page, 'quantity', 1);
        expect(productQuantity).to.equal(jsonResponse.items[idxItem].quantity);

        const productPrice = await productsPage.getTextColumn(page, 'price', 1);
        expect(productPrice).to.equal(parseFloat(jsonResponse.items[idxItem].price));

        const productCategory = await productsPage.getTextColumn(page, 'category', 1);
        expect(productCategory).to.equal(jsonResponse.items[idxItem].category);
      }
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      await productsPage.resetFilter(page);

      const numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
      expect(numberOfProducts).to.be.above(0);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
