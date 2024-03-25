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
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Languages from '@data/demo/languages';
import Products from '@data/demo/products';
import APIClientData from '@data/faker/APIClient';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_product_getAPIProductId';

describe('API : GET /product/{productId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let clientSecret: string;
  let accessToken: string;
  let jsonResponse: any;
  let idProduct: number;
  let productType: string;
  let productActive: boolean;
  let productNameEn: string;
  let productNameFr: string;
  let productDescriptionEn: string;
  let productDescriptionFr: string;

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

  describe('BackOffice : Expected data', async () => {
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
      await productsPage.filterProducts(page, 'product_name', Products.demo_1.name);

      const numProducts = await productsPage.getNumberOfProductsFromList(page);
      expect(numProducts).to.be.equal(1);

      const productName = await productsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.contains(Products.demo_1.name);

      idProduct = parseInt((await productsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
      expect(idProduct).to.be.gt(0);
    });

    it('should go to edit product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

      await productsPage.goToProductPage(page, 1);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should fetch informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchInformations', baseContext);

      productType = await createProductsPage.getProductType(page);
      expect(productType).to.be.a('string');

      productActive = await createProductsPage.getProductStatus(page);
      expect(productActive).to.be.a('boolean');

      productNameEn = await createProductsPage.getProductName(page, Languages.english.isoCode);
      expect(productNameEn).to.be.a('string');

      productNameFr = await createProductsPage.getProductName(page, Languages.french.isoCode);
      expect(productNameFr).to.be.a('string');

      productDescriptionEn = await descriptionTab.getValue(page, 'description', Languages.english.id.toString());
      expect(productDescriptionEn).to.be.a('string');

      productDescriptionFr = await descriptionTab.getValue(page, 'description', Languages.french.id.toString());
      expect(productDescriptionFr).to.be.a('string');
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /product/{productId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`product/${idProduct}`, {
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
        'productId',
        'type',
        'active',
        'names',
        'descriptions',
      );
    });

    it('should check the JSON Response : `productId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseProductId', baseContext);

      expect(jsonResponse).to.have.property('productId');
      expect(jsonResponse.productId).to.be.a('number');
      expect(jsonResponse.productId).to.be.equal(idProduct);
    });

    it('should check the JSON Response : `type`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseType', baseContext);

      expect(jsonResponse).to.have.property('type');
      expect(jsonResponse.type).to.be.a('string');
      expect(jsonResponse.type).to.be.equal(productType);
    });

    it('should check the JSON Response : `active`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseActive', baseContext);

      expect(jsonResponse).to.have.property('active');
      expect(jsonResponse.active).to.be.a('boolean');
      expect(jsonResponse.active).to.be.equal(productActive);
    });

    it('should check the JSON Response : `names`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNames', baseContext);

      expect(jsonResponse).to.have.property('names');
      expect(jsonResponse.names).to.be.a('object');
      expect(jsonResponse.names[Languages.english.id]).to.be.equal(productNameEn);
      expect(jsonResponse.names[Languages.french.id]).to.be.equal(productNameFr);
    });

    it('should check the JSON Response : `descriptions`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptions', baseContext);

      expect(jsonResponse).to.have.property('descriptions');
      expect(jsonResponse.descriptions).to.be.a('object');
      expect(jsonResponse.descriptions[Languages.english.id]).to.be.equal(productDescriptionEn);
      expect(jsonResponse.descriptions[Languages.french.id]).to.be.equal(productDescriptionFr);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
