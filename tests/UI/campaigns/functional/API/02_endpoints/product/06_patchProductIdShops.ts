import testContext from '@utils/testContext';
import {requestAccessToken} from '@commonTests/BO/advancedParameters/authServer';
import setMultiStoreStatus from '@commonTests/BO/advancedParameters/multistore';
import setFeatureFlag from '@commonTests/BO/advancedParameters/newFeatures';
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';
import {expect} from 'chai';

import {
  type APIRequestContext,
  boDashboardPage,
  boLoginPage,
  boMultistorePage,
  boMultistoreShopPage,
  boMultistoreShopCreatePage,
  boMultistoreShopUrlCreatePage,
  boProductsPage,
  type BrowserContext,
  FakerProduct,
  FakerShop,
  type Page,
  utilsAPI,
  utilsPlaywright,
  boFeatureFlagPage,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_API_endpoints_product_patchProductIdShops';

describe('API : PATCH /products/{productId}/shops', async () => {
  let apiContext: APIRequestContext;
  let browserContext: BrowserContext;
  let page: Page;
  let accessToken: string;
  let idProduct: number;
  let jsonResponse: any;
  let shopID: number = 0;

  const clientScope: string = 'product_write';
  const productData: FakerProduct = new FakerProduct({
    type: 'standard',
    status: true,
  });
  const shopData: FakerShop = new FakerShop({
    shopGroup: 'Default',
    categoryRoot: 'Home',
    importDataFromAnotherShop: false,
  });

  // Pre Condition : Create a product
  createProductTest(productData, `${baseContext}_preTest_0`);

  // Pre-condition: Enable multistore
  setMultiStoreStatus(true, `${baseContext}_preTest_1`);

  // Pre-condition: Enable "Admin API - Multistore"
  setFeatureFlag(boFeatureFlagPage.featureFlagAdminAPIMultistore, true, `${baseContext}_preTest_2`);

  describe('API : PATCH /products/{productId}/shops', async () => {
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

    describe('BackOffice : Create new store and set URL', async () => {
      it('should login in BO', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

        await boLoginPage.goTo(page, global.BO.URL);
        await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

        const pageTitle = await boDashboardPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDashboardPage.pageTitle);
      });

      it('should go to \'Advanced Parameters > Multistore\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToMultiStorePage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.multistoreLink,
        );
        await boMultistorePage.closeSfToolBar(page);

        const pageTitle = await boMultistorePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistorePage.pageTitle);
      });

      it('should go to add new shop page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewShopPage', baseContext);

        await boMultistorePage.goToNewShopPage(page);

        const pageTitle = await boMultistoreShopCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreShopCreatePage.pageTitleCreate);
      });

      it('should create shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'createShop', baseContext);

        const textResult = await boMultistoreShopCreatePage.setShop(page, shopData);
        expect(textResult).to.contains(boMultistorePage.successfulCreationMessage);
      });

      it('should get the id of the new shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'getShopID', baseContext);

        const numberOfShops = await boMultistoreShopPage.getNumberOfElementInGrid(page);
        expect(numberOfShops).to.be.above(0);
      });

      it('should go to add URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToAddURL', baseContext);

        await boMultistoreShopPage.filterTable(page, 'a!name', shopData.name);

        shopID = parseInt(await boMultistoreShopPage.getTextColumn(page, 1, 'id_shop'), 10);

        await boMultistoreShopPage.goToSetURL(page, 1);

        const pageTitle = await boMultistoreShopUrlCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistoreShopUrlCreatePage.pageTitleCreate);
      });

      it('should set URL', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'addURL', baseContext);

        const textResult = await boMultistoreShopUrlCreatePage.setVirtualUrl(page, shopData.name);
        expect(textResult).to.contains(boMultistoreShopUrlCreatePage.successfulCreationMessage);
      });
    });

    describe('BackOffice : Fetch the ID product', async () => {
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
        await boProductsPage.filterProducts(page, 'product_name', productData.name);

        const numProducts = await boProductsPage.getNumberOfProductsFromList(page);
        expect(numProducts).to.be.equal(1);

        const productName = await boProductsPage.getTextColumn(page, 'product_name', 1);
        expect(productName).to.contains(productData.name);

        idProduct = parseInt((await boProductsPage.getTextColumn(page, 'id_product', 1)).toString(), 10);
        expect(idProduct).to.be.gt(0);
      });

      it('should check the associated shops', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkAssociatedShopsBefore', baseContext);

        const valueProperty = await boProductsPage.getProductShopsId(page, 1);
        expect(valueProperty).to.deep.equal([1]);
      });
    });

    describe('Update the property `associatedShopIds` with API and check in BO', async () => {
      it('should request the endpoint /products/{productId}/shops', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'requestEndpointAssociatedShopIds', baseContext);

        const apiResponse = await apiContext.patch(`products/${idProduct}/shops`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
          data: {
            sourceShopId: 1,
            associatedShopIds: [1, shopID],
          },
          params: {
            shopId: 1,
          },
        });
        expect(apiResponse.status()).to.eq(200);
        expect(utilsAPI.hasResponseHeader(apiResponse, 'Content-Type')).to.eq(true);
        expect(utilsAPI.getResponseHeader(apiResponse, 'Content-Type')).to.contains('application/json');

        jsonResponse = await apiResponse.json();
        expect(jsonResponse).to.have.property('productId');
        expect(jsonResponse.productId).to.equal(idProduct);
        expect(jsonResponse).to.have.property('shopIds');
        expect(jsonResponse.shopIds).to.deep.equal([1, shopID]);
      });

      it('should check the JSON Response keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkResponseKeysAssociatedShopIds', baseContext);

        expect(jsonResponse).to.have.all.keys([
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
        ]);
      });

      it('should check that the property "associatedShopIds"', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkPropertyAssociatedShopIds', baseContext);

        await boProductsPage.reloadPage(page);

        const valueProperty = await boProductsPage.getProductShopsId(page, 1);
        expect(valueProperty).to.deep.equal([1, shopID]);
      });
    });

    describe('Delete shop', async () => {
      it('should go back to \'Advanced Parameters > Multistore\' page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goBackToMultiStorePage', baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.advancedParametersLink,
          boDashboardPage.multistoreLink,
        );
        await boMultistorePage.closeSfToolBar(page);

        const pageTitle = await boMultistorePage.getPageTitle(page);
        expect(pageTitle).to.contains(boMultistorePage.pageTitle);
      });

      it('should go to the created shop page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'goToCreatedShopPage', baseContext);

        await boMultistorePage.goToShopPage(page, shopID);

        const pageTitle = await boMultistoreShopPage.getPageTitle(page);
        expect(pageTitle).to.contains(shopData.name);
      });

      it('should delete the shop', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'deleteShop', baseContext);

        await boMultistoreShopPage.filterTable(page, 'a!name', shopData.name);

        const textResult = await boMultistoreShopPage.deleteShop(page, 1);
        expect(textResult).to.contains(boMultistoreShopPage.successfulDeleteMessage);
      });
    });
  });

  // Pre-condition: Disable "Admin API - Multistore"
  setFeatureFlag(boFeatureFlagPage.featureFlagAdminAPIMultistore, false, `${baseContext}_preTest_0`);

  // Pre-condition: Disable multistore
  setMultiStoreStatus(false, `${baseContext}_postTest_1`);

  // Pre-condition: Delete a product
  deleteProductTest(productData, `${baseContext}_postTest_2`);
});
