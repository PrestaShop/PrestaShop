// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import {deleteProductTest} from '@commonTests/BO/catalog/product';

import {expect} from 'chai';
import {
  type APIRequestContext,
  boProductsPage,
  boProductsCreatePage,
  boProductsCreateTabDescriptionPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataLanguages,
  FakerProduct,
  type Page,
  utilsAPI,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_postProduct';

describe('API : POST /products', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let jsonResponse: any;

  const clientScope: string = 'product_write';
  const createProduct: FakerProduct = new FakerProduct({});

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

  describe('API : Create the Product', async () => {
    it('should request the endpoint /products', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'requestEndpoint', baseContext);

      const apiResponse = await apiContext.post('products', {
        headers: {
          Authorization: `Bearer ${accessToken}`,
        },
        data: {
          type: createProduct.type,
          names: {
            [dataLanguages.english.locale]: createProduct.name,
            [dataLanguages.french.locale]: createProduct.nameFR,
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
        'enabled',
        'additionalShippingCost',
        'availableForOrder',
        'availableLaterLabels',
        'availableNowLabels',
        'categories',
        'carrierReferenceIds',
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

    it('should check the JSON Response', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseJSON', baseContext);

      expect(jsonResponse.productId).to.be.gt(0);
      expect(jsonResponse.type).to.equal(createProduct.type);
      expect(jsonResponse.enabled).to.equal(false);
      expect(jsonResponse.names[dataLanguages.english.locale]).to.equal(createProduct.name);
      expect(jsonResponse.names[dataLanguages.french.locale]).to.equal(createProduct.nameFR);
      expect(jsonResponse.descriptions[dataLanguages.english.locale]).to.equal('');
      expect(jsonResponse.descriptions[dataLanguages.french.locale]).to.equal('');
      expect(jsonResponse.shopIds).to.deep.equal([1]);
    });
  });

  describe('BackOffice : Check the Product is created', async () => {
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

      const pageTitle: string = await boProductsCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boProductsCreatePage.pageTitle);
    });

    it('should check the JSON Response : `type`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseType', baseContext);

      const value = await boProductsCreatePage.getProductType(page);
      expect(value).to.equal(jsonResponse.type);
    });

    it('should check the JSON Response : `enabled`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseEnabled', baseContext);

      const value = await boProductsCreatePage.getProductStatus(page);
      expect(value).to.equal(jsonResponse.enabled);
    });

    it('should check the JSON Response : `categories`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseCategories', baseContext);

      const value = await boProductsCreateTabDescriptionPage.getSelectedCategories(page);

      for (let incCategory = 0; incCategory < jsonResponse.categories.length; incCategory++) {
        expect(value).to.contains(jsonResponse.categories[incCategory].displayName);
      }
    });

    it('should check the JSON Response : `defaultCategoryId`', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDefaultCategoryId', baseContext);

      const value = parseInt(await boProductsCreateTabDescriptionPage.getValue(page, 'id_category_default'), 10);
      expect(value).to.equal(jsonResponse.defaultCategoryId);
    });

    it('should check the JSON Response : `description` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptionsEN', baseContext);

      const value = await boProductsCreateTabDescriptionPage.getValue(page, 'description', dataLanguages.english.id.toString());
      expect(value).to.equal(jsonResponse.descriptions[dataLanguages.english.locale]);
    });

    it('should check the JSON Response : `description` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseDescriptionsFR', baseContext);

      const value = await boProductsCreateTabDescriptionPage.getValue(page, 'description', dataLanguages.french.id.toString());
      expect(value).to.equal(jsonResponse.descriptions[dataLanguages.french.locale]);
    });

    it('should check the JSON Response : `names` (EN)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesEN', baseContext);

      const value = await boProductsCreatePage.getProductName(page, dataLanguages.english.isoCode);
      expect(value).to.equal(jsonResponse.names[dataLanguages.english.locale]);
    });

    it('should check the JSON Response : `names` (FR)', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkResponseNamesFR', baseContext);

      const value = await boProductsCreatePage.getProductName(page, dataLanguages.french.isoCode);
      expect(value).to.equal(jsonResponse.names[dataLanguages.french.locale]);
    });
  });

  // Post-condition: Delete a Product
  deleteProductTest(createProduct, `${baseContext}_postTest_0`);
});
