// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  type BrowserContext,
  dataLanguages,
  dataProducts,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_getProductId';

describe('API : GET /products/{productId}', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;
  let idProduct: number;
  let productType: string;
  let productEnabled: boolean;
  let productCategories: string;
  let productIdCategory: number;
  let productNameEn: string;
  let productNameFr: string;
  let productDescriptionEn: string;
  let productDescriptionFr: string;

  const clientScope: string = 'product_read';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    apiContext = await utilsPlaywright.createAPIContext(global.API.URL);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  describe('API : Fetch the access token', async () => {
    it(`should request the endpoint /access_token with scope ${clientScope}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestOauth2Token', baseContext);
      accessToken = await requestAccessToken(clientScope);
    });
  });

  describe('BackOffice : Expected data', async () => {
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

      productEnabled = await boProductsCreatePage.getProductStatus(page);
      expect(productEnabled).to.be.a('boolean');

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

      productCategories = await boProductsCreateTabDescriptionPage.getSelectedCategories(page);
      expect(productCategories).to.be.a('string');

      productIdCategory = parseInt(await boProductsCreateTabDescriptionPage.getValue(page, 'id_category_default'), 10);
      expect(productIdCategory).to.be.a('number');
    });
  });

  describe('API : Check Data', async () => {
    it('should request the endpoint /products/{productId}', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.get(`products/${idProduct}`, {
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
        'enabled',
        'additionalShippingCost',
        'availableForOrder',
        'availableLaterLabels',
        'availableNowLabels',
        'carrierReferenceIds',
        'categories',
        'condition',
        'coverThumbnailUrl',
        'defaultCategoryId',
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

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      expect(jsonResponse).to.have.property('enabled');
      expect(jsonResponse.enabled).to.be.a('boolean');
      expect(jsonResponse.enabled).to.be.equal(productEnabled);
    });

    it('should check the JSON Response : `categories`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCategories', baseContext);

      expect(jsonResponse).to.have.property('categories');
      expect(jsonResponse.categories).to.be.a('array');
      expect(jsonResponse.categories.length).to.be.greaterThan(0);
      expect(jsonResponse.categories[0]).to.be.a('object');

      for (let incCategory = 0; incCategory < jsonResponse.categories.length; incCategory++) {
        const jsonProductCategory = jsonResponse.categories[incCategory];
        expect(jsonProductCategory.categoryId).to.be.a('number');
        expect(jsonProductCategory.name).to.be.a('string');
        expect(jsonProductCategory.displayName).to.be.a('string');
        expect(productCategories).to.contains(jsonProductCategory.displayName);
      }
    });

    it('should check the JSON Response : `defaultCategoryId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDefaultCategoryId', baseContext);

      expect(jsonResponse).to.have.property('defaultCategoryId');
      expect(jsonResponse.defaultCategoryId).to.be.a('number');
      expect(jsonResponse.defaultCategoryId).to.be.greaterThan(0);
      expect(jsonResponse.defaultCategoryId).to.equal(productIdCategory);
    });

    it('should check the JSON Response : `descriptions`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptions', baseContext);

      expect(jsonResponse).to.have.property('descriptions');
      expect(jsonResponse.descriptions).to.be.a('object');
      expect(jsonResponse.descriptions[dataLanguages.english.locale]).to.be.equal(productDescriptionEn);
      expect(jsonResponse.descriptions[dataLanguages.french.locale]).to.be.equal(productDescriptionFr);
    });

    it('should check the JSON Response : `names`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNames', baseContext);

      expect(jsonResponse).to.have.property('names');
      expect(jsonResponse.names).to.be.a('object');
      expect(jsonResponse.names[dataLanguages.english.locale]).to.be.equal(productNameEn);
      expect(jsonResponse.names[dataLanguages.french.locale]).to.be.equal(productNameFr);
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
});
