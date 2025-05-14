// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {deleteAPIClientTest} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boApiClientsPage,
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

const baseContext: string = 'functional_API_endpoints_product_getProductId';

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
  const clientData: FakerAPIClient = new FakerAPIClient({
    enabled: true,
    scopes: [
      clientScope,
    ],
  });

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

  describe('BackOffice : Expected data', async () => {
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
      await boProductsPage.filterProducts(page, 'product_name', dataProducts.demo_1.name);

      const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
      expect(numProducts).to.be.equal(1);

      const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
      expect(productName).to.contains(dataProducts.demo_1.name);

      idProduct = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
      expect(idProduct).to.be.gt(0);
    });

    it('should go to edit product page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToEditProductPageAfterPost', baseContext);

      await boProductsPage.goToProductPage(page, 1);

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should fetch informations', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fetchInformations', baseContext);

      productType = await boProductsCreatePage.getProductType(page);
      expect(productType).to.be.a('string');

      productActive = await boProductsCreatePage.getProductStatus(page);
      expect(productActive).to.be.a('boolean');

      productNameEn = await boProductsCreatePage.getProductName(page, dataLanguages.english.isoCode);
      expect(productNameEn).to.be.a('string');

      productNameFr = await boProductsCreatePage.getProductName(page, dataLanguages.french.isoCode);
      expect(productNameFr).to.be.a('string');

      productDescriptionEn = await boProductsCreateTabDescriptionPage.getValue(
        page,
        'description',
        dataLanguages.english.id.toString(),
      );
      expect(productDescriptionEn).to.be.a('string');

      productDescriptionFr = await boProductsCreateTabDescriptionPage.getValue(
        page,
        'description',
        dataLanguages.french.id.toString(),
      );
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
      expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
      expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

      jsonResponse = await apiResponse.json();
    });

    it('should check the JSON Response keys', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeys', baseContext);
      expect(jsonResponse).to.have.all.keys(
        'active',
        'additionalShippingCost',
        'availableForOrder',
        'availableLaterLabels',
        'availableNowLabels',
        'carrierReferenceIds',
        'condition',
        'coverThumbnailUrl',
        'deliveryTimeInStockNotes',
        'deliveryTimeNoteType',
        'deliveryTimeOutOfStockNotes',
        'depth',
        'descriptions',
        'ecotaxTaxExcluded',
        'ecotaxTaxIncluded',
        'gtin',
        'height',
        'isbn',
        'linkRewrites',
        'location',
        'lowStockAlertEnabled',
        'lowStockThreshold',
        'manufacturerId',
        'metaDescriptions',
        'metaTitles',
        'minimalQuantity',
        'mpn',
        'names',
        'onSale',
        'onlineOnly',
        'outOfStockType',
        'packStockType',
        'priceTaxExcluded',
        'priceTaxIncluded',
        'productId',
        'quantity',
        'redirectType',
        'reference',
        'shopIds',
        'shortDescriptions',
        'showCondition',
        'showPrice',
        'tags',
        'taxRulesGroupId',
        'type',
        'unitPriceRatio',
        'unitPriceTaxExcluded',
        'unitPriceTaxIncluded',
        'unity',
        'upc',
        'visibility',
        'weight',
        'wholesalePrice',
        'width',
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
      expect(jsonResponse.names[dataLanguages.english.locale]).to.be.equal(productNameEn);
      expect(jsonResponse.names[dataLanguages.french.locale]).to.be.equal(productNameFr);
    });

    it('should check the JSON Response : `descriptions`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptions', baseContext);

      expect(jsonResponse).to.have.property('descriptions');
      expect(jsonResponse.descriptions).to.be.a('object');
      expect(jsonResponse.descriptions[dataLanguages.english.locale]).to.be.equal(productDescriptionEn);
      expect(jsonResponse.descriptions[dataLanguages.french.locale]).to.be.equal(productDescriptionFr);
    });

    it('should check the JSON Response : `shopIds`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseShopIds', baseContext);

      expect(jsonResponse).to.have.property('shopIds');
      expect(jsonResponse.shopIds).to.be.a('array');
      expect(jsonResponse.shopIds).to.be.deep.equal([1]);
      expect(jsonResponse.shopIds[0]).to.be.a('number');
      expect(jsonResponse.shopIds[0]).to.be.equal(1);
    });
  });

  // Pre-condition: Create an API Client
  deleteAPIClientTest(`${baseContext}_postTest`);
});
