// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

// Import pages
import createProductsPage from '@pages/BO/catalog/products/add';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boApiClientsPage,
  boApiClientsCreatePage,
  boProductsPage,
  boProductsCreateTabDescriptionPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataLanguages,
  FakerAPIClient,
  FakerProduct,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_postProduct';

describe('API : POST /product', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let clientSecret: string;
  let jsonResponse: any;

  const clientScope: string = 'product_write';
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });
  const createProduct: FakerProduct = new FakerProduct({});

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

  describe('API : Create the Product', async () => {
    it('should request the endpoint /product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('product', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          type: createProduct.type,
          active: createProduct.status,
          names: {
            [dataLanguages.english.id]: createProduct.name,
            [dataLanguages.french.id]: createProduct.nameFR,
          },
          descriptions: {
            [dataLanguages.english.id]: createProduct.description,
            [dataLanguages.french.id]: createProduct.descriptionFR,
          },
        },
      });
      expect(apiResponse.status()).to.eq(201);
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

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
      expect(jsonResponse.names[dataLanguages.english.id]).to.equal(createProduct.name);
      expect(jsonResponse.names[dataLanguages.french.id]).to.equal(createProduct.nameFR);
      // @todo : https://github.com/PrestaShop/PrestaShop/issues/35619
      //expect(jsonResponse.descriptions[dataLanguages.english.id]).to.equal(createProduct.description);
      //expect(jsonResponse.descriptions[dataLanguages.french.id]).to.equal(createProduct.descriptionFR);
    });
  });

  describe('BackOffice : Check the Product is created', async () => {
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
    });

    it('should check the JSON Response : `productId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseProductId', baseContext);

      const value = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
      expect(value).to.equal(jsonResponse.productId);
    });

    it('should go to edit product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

      await boProductsPage.goToProductPage(page, 1);

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

      const value = await createProductsPage.getProductName(page, dataLanguages.english.isoCode);
      expect(value).to.equal(jsonResponse.names[dataLanguages.english.id]);
    });

    it('should check the JSON Response : `names` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesFR', baseContext);

      const value = await createProductsPage.getProductName(page, dataLanguages.french.isoCode);
      expect(value).to.equal(jsonResponse.names[dataLanguages.french.id]);
    });

    it('should check the JSON Response : `description` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptionsEN', baseContext);

      const value = await boProductsCreateTabDescriptionPage.getValue(page, 'description', dataLanguages.english.id.toString());
      expect(value).to.equal(jsonResponse.descriptions[dataLanguages.english.id]);
    });

    it('should check the JSON Response : `description` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptionsFR', baseContext);

      const value = await boProductsCreateTabDescriptionPage.getValue(page, 'description', dataLanguages.french.id.toString());
      expect(value).to.equal(jsonResponse.descriptions[dataLanguages.french.id]);
    });
  });

  // Post-condition: Delete a Product
  deleteProductTest(createProduct, `${baseContext}_postTest_0`);

  // Post-condition: Delete an API Client
  deleteAPIClientTest(`${baseContext}_postTest_1`);
});
