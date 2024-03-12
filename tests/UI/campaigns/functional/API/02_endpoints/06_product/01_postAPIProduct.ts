// Import utils
import api from '@utils/api';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import {deleteProductTest} from '@commonTests/BO/catalog/product';
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
import APIClientData from '@data/faker/APIClient';
import ProductData from '@data/faker/product';

import {expect} from 'chai';
import type {APIRequestContext, BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_API_endpoints_product_postAPIProduct';

describe('API : POST /api/product', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let jsonResponse: any;

  const clientScope: string = 'product_write';
  const clientData: APIClientData = new APIClientData({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createProduct: ProductData = new ProductData({});

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

  describe('API : Create the Product', async () => {
    it('should request the endpoint /api/product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('api/product', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          type: createProduct.type,
          active: createProduct.status,
          names: {
            [Languages.english.id]: createProduct.name,
            [Languages.french.id]: createProduct.nameFR,
          },
          descriptions: {
            [Languages.english.id]: createProduct.description,
            [Languages.french.id]: createProduct.descriptionFR,
          },
        },
      });
      expect(apiResponse.status()).to.eq(201);
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

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.productId).to.be.gt(0);
      expect(jsonResponse.type).to.equal(createProduct.type);
      expect(jsonResponse.names[Languages.english.id]).to.equal(createProduct.name);
      expect(jsonResponse.names[Languages.french.id]).to.equal(createProduct.nameFR);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35619
      //expect(jsonResponse.descriptions[Languages.english.id]).to.equal(createProduct.description);
      //expect(jsonResponse.descriptions[Languages.french.id]).to.equal(createProduct.descriptionFR);
    });
  });

  describe('BackOffice : Check the Product is created', async () => {
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
    });

    it('should check the JSON Response : `productId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseProductId', baseContext);

      const value = parseInt((await productsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
      expect(value).to.equal(jsonResponse.productId);
    });

    it('should go to edit product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

      await productsPage.goToProductPage(page, 1);

      const pageTitle: string = await createProductsPage.getPageTitle(page);
      expect(pageTitle).to.contains(createProductsPage.pageTitle);
    });

    it('should check the JSON Response : `type`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseType', baseContext);

      const value = await createProductsPage.getProductType(page);
      expect(value).to.equal(jsonResponse.type);
    });

    it('should check the JSON Response : `active`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseActive', baseContext);

      const value = await createProductsPage.getProductStatus(page);
      expect(value).to.equal(jsonResponse.active);
    });

    it('should check the JSON Response : `names` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesEN', baseContext);

      const value = await createProductsPage.getProductName(page, Languages.english.isoCode);
      expect(value).to.equal(jsonResponse.names[Languages.english.id]);
    });

    it('should check the JSON Response : `names` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesFR', baseContext);

      const value = await createProductsPage.getProductName(page, Languages.french.isoCode);
      expect(value).to.equal(jsonResponse.names[Languages.french.id]);
    });

    it('should check the JSON Response : `description` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptionsEN', baseContext);

      const value = await descriptionTab.getValue(page, 'description', Languages.english.id.toString());
      expect(value).to.equal(jsonResponse.descriptions[Languages.english.id]);
    });

    it('should check the JSON Response : `description` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptionsFR', baseContext);

      const value = await descriptionTab.getValue(page, 'description', Languages.french.id.toString());
      expect(value).to.equal(jsonResponse.descriptions[Languages.french.id]);
    });
  });

  // Post-condition: Delete a Product
  deleteProductTest(createProduct, `${baseContext}_postTest_0`);

  // Post-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_1`);
});
